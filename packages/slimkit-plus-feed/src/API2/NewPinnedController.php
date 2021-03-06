<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\API2;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Leven\Http\Controllers\Controller;
use Leven\Models\Comment as CommentModel;
use Leven\Packages\Currency\Processes\User as UserProcess;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed as FeedModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedPinned as FeedPinnedModel;

class NewPinnedController extends Controller
{
    /**
     * 申请动态评论置顶.
     *
     * @param Request $request
     * @param ResponseContract $response
     * @param FeedModel $feed
     * @param CommentModel $comment
     * @param Carbon $datetime
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function commentPinned(Request $request, FeedModel $feed, CommentModel $comment, Carbon $datetime)
    {
        $user = $request->user();
        if ($comment->user_id !== $user->id) {
            return response()->json(['message' => ['你没有权限申请']])->setStatusCode(403);
        } elseif ($feed->pinnedComments()->newPivotStatementForId($comment->id)->where(function ($query) use ($datetime) {
            return $query->where('expires_at', '>', $datetime)->orwhere('expires_at', null);
        })->first()) {
            return response()->json(['message' => ['已经申请过']])->setStatusCode(422);
        }

        $pinned = new FeedPinnedModel();
        $pinned->user_id = $user->id;
        $pinned->channel = 'comment';
        $pinned->target = $comment->id;
        $pinned->raw = $feed->id;
        $pinned->target_user = $feed->user_id;

        return app()->call([$this, 'validateBase'], [
            'pinned' => $pinned,
            'call' => function (FeedPinnedModel $pinned) use ($user, $comment, $feed) {
                $process = new UserProcess();
                // $order = $process->prepayment($user->id, $pinned->amount, $feed->user_id, '申请动态评论置顶', sprintf('申请评论《%s》置顶', $comment->body));
                $message = '提交成功,等待审核';
                if ($pinned->amount) {
                    $order = $process->prepayment($user->id, $pinned->amount, $feed->user_id, '申请动态评论置顶', sprintf('申请评论《%s》置顶', $comment->body));
                    if ($feed->user_id === $user->id) {
                        $dateTime = new Carbon();
                        $pinned->expires_at = $dateTime->addDay($pinned->day);
                        $message = '置顶成功';
                    }
                }
                if ($order) {
                    $pinned->save();
                    if ($feed->user) {
                        $message = sprintf('%s 在你发布的动态中申请评论置顶', $user->name);
                        $feed->user->sendNotifyMessage('feed:pinned-comment', $message, [
                            'feed' => $feed,
                            'user' => $user,
                            'comment' => $comment,
                            'pinned' => $pinned,
                        ]);
                    }

                    return response()->json(['message' => $message], 201);
                }

                return response()->json(['message' => ['操作失败']], 500);
            },
        ]);
    }

    /**
     * 申请动态置顶.
     *
     * @param Request $request
     * @param FeedModel $feed
     * @param Carbon $datetime
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function feedPinned(Request $request, FeedModel $feed, Carbon $datetime)
    {
        $user = $request->user();

        if ($feed->user_id !== $user->id) {
            return response()->json(['message' => ['你没有权限申请']])->setStatusCode(403);
        } elseif ($feed->pinned()->where('user_id', $user->id)->where(function ($query) use ($datetime) {
            return $query->where('expires_at', '>', $datetime)->orwhere('expires_at', null);
        })->first()) {
            return response()->json(['message' => ['已经申请过']])->setStatusCode(422);
        }

        $pinned = new FeedPinnedModel();
        $pinned->user_id = $user->id;
        $pinned->channel = 'feed';
        $pinned->target = $feed->id;

        return app()->call([$this, 'validateBase'], [
            'pinned' => $pinned,
            'call' => function (FeedPinnedModel $pinned) use ($user, $feed) {
                $process = new UserProcess();
                $order = $process->prepayment($user->id, $pinned->amount, 0, '动态申请置顶', sprintf('申请置顶动态《%s》', str_limit($feed->feed_content, 100)));

                if ($order) {
                    $pinned->save();

                    return response()->json(['message' => ['申请成功']], 201);
                }

                return response()->json(['message' => ['操作失败']], 500);
            },
        ]);
    }

    /**
     * 基础验证.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedPinned $pinned
     * @param callable $call
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function validateBase(Request $request, FeedPinnedModel $pinned, callable $call)
    {
        $user = $request->user();
        $currency = $user->currency()->firstOrCreate(['type' => 1], ['sum' => 0]);

        $rules = [
            'amount' => [
                'required',
                'integer',
                'min:1',
                'max:'.$currency->sum,
            ],
            'day' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
        $messages = [
            'amount.required' => '请输入申请金额',
            'amount.integer' => '参数有误',
            'amount.min' => '输入金额有误',
            'amount.max' => '余额不足',
            'day.required' => '请输入申请天数',
            'day.integer' => '天数只能为整数',
            'day.min' => '输入天数有误',
        ];
        $this->validate($request, $rules, $messages);

        $pinned->amount = intval($request->input('amount'));
        $pinned->day = intval($request->input('day'));

        return app()->call($call, [
            'pinned' => $pinned,
        ]);
    }
}
