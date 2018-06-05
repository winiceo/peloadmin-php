<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\API2\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Leven\Services\Push;
use Leven\Models\Comment;
use Leven\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\News;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\NewsPinned;

class CommentController extends Controller
{
    /**
     * 发布资讯评论.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request
     * @param  News    $news
     * @param  Comment $comment
     * @return mix
     */
    public function store(Request $request, News $news, Comment $comment)
    {
        $replyUser = intval($request->input('reply_user', 0));
        $body = $request->input('body');
        $user = $request->user();

        $comment->user_id = $user->id;
        $comment->reply_user = $replyUser;
        $comment->target_user = $news->user_id;
        $comment->body = $body;

        $news->getConnection()->transaction(function () use ($news, $comment, $user) {
            $news->comments()->save($comment);
            $news->increment('comment_count', 1);
            $user->extra()->firstOrCreate([])->increment('comments_count', 1);
            if ($news->user->id !== $user->id) {
                $news->user->unreadCount()->firstOrCreate([])->increment('unread_comments_count', 1);
                app(Push::class)->push(sprintf('%s评论了你的资讯', $user->name), (string) $news->user->id, ['channel' => 'news:comment']);
            }
        });

        if ($replyUser && $replyUser !== $user->id && $replyUser !== $news->user_id) {
            $replyUser = $user->newQuery()->where('id', $replyUser)->first();
            $replyUser->unreadCount()->firstOrCreate([])->increment('unread_comments_count', 1);
            app(Push::class)->push(sprintf('%s 回复了您的评论', $user->name), (string) $replyUser->id, ['channel' => 'news:comment-reply']);
        }

        return response()->json([
            'message' => ['操作成功'],
            'comment' => $comment,
        ])->setStatusCode(201);
    }

    /**
     * 获取一条资讯的评论列表.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request
     * @param  news    $news
     * @return mix
     */
    public function index(Request $request, news $news)
    {
        $after = $request->input('after');
        $limit = $request->input('limit', 15);
        $comments = $news->comments()->when($after, function ($query) use ($after) {
            return $query->where('id', '<', $after);
        })->limit($limit)->with(['user', 'reply'])->orderBy('id', 'desc')->get();

        return response()->json([
            'pinneds' => ! $after ? app()->call([$this, 'pinneds'], ['news' => $news]) : [],
            'comments' => $comments,
        ])->setStatusCode(200);
    }

    /**
     * 获取置顶的评论列表.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request
     * @param  Carbon  $dateTime
     * @param  News    $news
     * @return mix
     */
    public function pinneds(Request $request, Carbon $dateTime, News $news)
    {
        if ($request->input('after')) {
            return [];
        } else {
            return $news->pinnedComments()
                ->with(['user', 'reply'])
                ->where('expires_at', '>', $dateTime)
                ->get();
        }
    }

    /**
     * 删除评论.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request
     * @param  news    $news
     * @param  Comment $comment
     * @return mix
     */
    public function destroy(Request $request, news $news, Comment $comment, NewsPinned $pinnedModel)
    {
        $user = $request->user();
        if ($comment->user_id !== $user->id) {
            return response()->json(['message' => ['没有权限']], 403);
        }

        $pinned = $pinnedModel->where('channel', 'news:comment')->where('raw', $comment->id)->where('state', 0)->first();

        $news->getConnection()->transaction(function () use ($user, $news, $comment, $pinned) {
            if ($pinned) {
                $user->wallet()->increment('balance', $pinned->amount);
            }

            $news->decrement('comment_count', 1);
            $user->extra()->decrement('comments_count', 1);
            $comment->delete();
        });

        return response()->json()->setStatusCode(204);
    }
}
