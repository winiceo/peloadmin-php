<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\AdminControllers;

use Illuminate\Http\Request;
use Leven\Http\Controllers\Controller;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\NewsApplyLog;

class NewsApplyLogController extends Controller
{
    /**
     * 删除申请列表.
     *
     * @param Request $request
     * @param NewsApplyLog $model
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function index(Request $request, NewsApplyLog $model)
    {
        $limit = $request->query('limit', 15);
        // $offset = $request->query('offset', 0);
        $key = $request->query('key');
        $user_id = $request->query('user_id');
        $news_id = $request->query('news_id');
        $query = $model->when($user_id, function ($query) {
            return $query->where('user_id', $user_id);
        })->when($news_id, function ($query) {
            return $query->where('news_id', $news_id);
        })->whereHas('news', function ($query) use ($key) {
            return $query->when($key, function ($query) use ($key) {
                return $query->where('news.title', 'like', '%'.$key.'%');
            })->withTrashed();
        });
        // $total = $query->count();
        $datas = $query->limit($limit)->with(['news' => function ($query) {
            return $query->withTrashed();
        }, 'user'])->get();

        return response()->json($datas, 200);
    }

    public function accept(NewsApplyLog $log)
    {
        $log->getConnection()->transaction(function () use ($log) {
            $log->load(['news', 'user']);
            $log->news->delete();
            $log->status = 1;
            $log->save();
            $log->user->sendNotifyMessage('news:delete:accept', sprintf('资讯《%s》的删除申请已被通过', $log->news->title), [
                'news' => $log->news,
                'log' => $log,
            ]);
        });

        return response()->json('', 204);
    }

    public function reject(Request $request, NewsApplyLog $log)
    {
        $log->status = 2;
        $log->mark = $request->input('mark');
        $log->save();
        $log->user->sendNotifyMessage('news:delete:reject', sprintf('资讯《%s》的删除申请已被拒绝，拒绝理由为%s', $log->news->title, $log->mark), [
            'news' => $log->news,
            'log' => $log,
        ]);

        return response()->json('', 204);
    }
}
