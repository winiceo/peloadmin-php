<?php

declare(strict_types=1);


namespace Leven\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Leven\Http\Controllers\Controller;
use Leven\Models\Bill as OrderModel;

class OrderController extends Controller
{

    public function index(Request $request)
    {


        $limit = $request->query('limit');
        $offset = $request->query('offset');

        $query = OrderModel::with(['user']);

        $count = $query->count();
        $items = $query->limit($limit)->offset($offset)->get();


        $items->map(function ($item) {
            $item->view = 333;//route('pc:reportview', ['reportable_id' => $item->reportable_id, 'reportable_type' => $item->reportable_type]);

            return $item;
        });


        return response()->json($items, 200, ['x-total' => $count]);
    }

    /**
     * 处理举报.
     *
     * @param  Request $request
     * @param  OrderModel $report
     * @return mixed
     */
    public function deal(Request $request, OrderModel $report)
    {
        $mark = $request->input('mark');
        $report->status = 1;
        $report->mark = $mark;
        $report->save();

        if ($report->user) {
            $report->user->sendNotifyMessage('user-report:notice', '您的举报已被后台处理：' . $mark, [
                'report' => $report,
            ]);
        }

        if ($report->target) {
            $report->target->sendNotifyMessage(
                'user-report:notice',
                '你的「' . $report->subject . '」已被举报',
                ['repot' => $report]
            );
        }

        return response()->json(['message' => '操作成功'], 201);
    }

    /**
     * 驳回举报.
     *
     * @param Request $request
     * @param OrderModel $report
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function reject(Request $request, OrderModel $report)
    {
        $mark = $request->input('mark');
        $report->status = 2;
        $report->mark = $mark;
        $report->save();

        if ($report->user) {
            $report->user->sendNotifyMessage('user-report:notice', '您的举报已被后台驳回，原因是：' . $mark, [
                'report' => $report,
            ]);
        }

        return response()->json(['message' => '操作成功'], 201);
    }


}
