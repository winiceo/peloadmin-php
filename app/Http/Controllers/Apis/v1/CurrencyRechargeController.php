<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Illuminate\Http\Request;
use Leven\Http\Requests\API2\StoreCurrencyRecharge;
use Leven\Models\CurrencyOrder as CurrencyOrderModel;
use Leven\Packages\Currency\Processes\Recharge as RechargeProcess;

class CurrencyRechargeController extends Controller
{
    /**
     * 钱包流水.
     *
     * @param Request $request
     * @param CurrencyOrderModel $currencyOrder
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function index(Request $request, CurrencyOrderModel $currencyOrder)
    {
        $user = $request->user();

        $limit = $request->query('limit', 15);
        $after = $request->query('after');
        $action = $request->query('action');
        $type = $request->query('type');

        $orders = $currencyOrder->where('owner_id', $user->id)
            ->when($after, function ($query) use ($after) {
                return $query->where('id', '<', $after);
            })
            ->when(in_array($action, ['recharge', 'cash']), function ($query) use ($action) {
                return $query->where('target_type', $action);
            })
            ->when(in_array($type, [1, -1]), function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($orders, 200);
    }

    /**
     * 发起充值订单.
     *
     * @param StoreCurrencyRecharge $request
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function store(StoreCurrencyRecharge $request)
    {
        $user = $request->user();
        $amount = $request->input('amount');
        $extra = $request->input('extra', []);
        $type = $request->input('type');

        $recharge = new RechargeProcess();

        if (($result = $recharge->createPingPPOrder((int) $user->id, (int) $amount, $type, $extra)) !== false) {
            return response()->json($result, 201);
        }

        return response()->json(['message' => '操作失败'], 500);
    }

    /**
     * 充值回调通知.
     *
     * @param Request $request
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function webhook(Request $request)
    {
        $webhook = new RechargeProcess();
        if ($webhook->webhook($request) === true) {
            return response('通知成功');
        }

        return response('操作失败', 500);
    }

    /**
     * 主动取回凭据.
     *
     * @param CurrencyOrderModel &$currencyOrder
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function retrieve(CurrencyOrderModel $order)
    {
        $retrieve = new RechargeProcess();
        if ($retrieve->retrieve($order) === true) {
            return response()->json($order, 200);
        }

        return response()->json(['message' => ['操作失败']], 500);
    }
}
