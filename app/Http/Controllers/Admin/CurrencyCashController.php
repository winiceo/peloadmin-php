<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use Leven\Models\WalletOrder;
use Leven\Packages\Wallet\Order;
use Leven\Repository\CurrencyConfig;
use Leven\Http\Controllers\Controller;
use Leven\Models\CurrencyOrder as OrderModel;

class CurrencyCashController extends Controller
{
    /**
     * 提现列表.
     *
     * @param Request $request
     * @param OrderModel $orderModel
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function list(Request $request, OrderModel $orderModel)
    {
        $limit = $request->query('limit', 15);
        $offset = $request->query('offset', 0);
        $state = $request->query('state');
        $user = (int) $request->query('user');
        $name = $request->query('name');

        $query = $orderModel->where('target_type', 'cash')
        ->when(! is_null($state) && in_array($state, [-1, 0, 1]), function ($query) use ($state) {
            return $query->where('state', $state);
        })
        ->when($user, function ($query) use ($user) {
            return $query->where('owner_id', $user);
        })
        ->when($name, function ($query) use ($name) {
            return $query->whereHas('user', function ($query) use ($name) {
                return $query->where('name', 'like', '%'.$name.'%');
            });
        });

        $count = $query->count();
        $cashes = $query->limit($limit)
        ->offset($offset)
        ->orderBy('id', 'desc')
        ->with(['user.currency'])
        ->get();

        return response()->json($cashes, 200, ['x-total' => $count]);
    }

    /**
     * 审核积分提现.
     *
     * @param  Request        $request
     * @param  OrderModel     $order
     * @param  CurrencyConfig $config
     * @return mixed
     */
    public function audit(Request $request, OrderModel $order)
    {
        $mark = $request->input('mark');
        $state = (int) $request->input('state');

        if (in_array($order->state, [1, -1])) {
            return response()->json(['message' => '已处理'], 403);
        }

        if (! in_array($state, [1, -1])) {
            return response()->json(['message' => '参数错误'], 422);
        }

        DB::transaction(function () use ($mark, $state, $order) {
            $order->state = $state;
            $order->save();

            if ($state === 1) {
                $order->user->sendNotifyMessage(
                    'user-currency:cash',
                    '积分提现申请审核通过',
                    ['order' => $order]
                );
                // 钱包变更记录.
                $walletOrderModel = new WalletOrder();
                $walletOrderModel->owner_id = $order->owner_id;
                $walletOrderModel->owner_id = $order->owner_id;
                $walletOrderModel->target_type = Order::TARGET_TYPE_WITHDRAW;
                $walletOrderModel->target_id = 0;
                $walletOrderModel->title = '提现';
                $walletOrderModel->body = sprintf('积分提现，钱包增加%s元', $order->amount / 100);
                $walletOrderModel->type = 1;
                $walletOrderModel->amount = $order->amount;
                $walletOrderModel->state = 1;
                $walletOrderModel->save();
                // 用户钱包变更
                $newWallet = $order->user->newWallet;
                $newWallet->balance += $order->amount;
                $newWallet->total_income += $order->amount;
                $newWallet->save();
            }
            // 处理退还积分
            if ($state === -1) {
                $order->user->sendNotifyMessage(
                    'user-currency:cash',
                    sprintf('积分提现申请被驳回,原因：%s', $mark),
                    ['order' => $order]
                );
                $order->user->currency->increment('sum', $order->amount);
            }
        });

        return response()->json(['message' => '处理成功'], 200);
    }
}
