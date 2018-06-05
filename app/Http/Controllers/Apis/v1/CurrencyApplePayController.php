<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Illuminate\Http\Request;
use Leven\Models\CommonConfig;
use Leven\Models\CurrencyOrder as CurrencyOrderModel;
use Leven\Http\Requests\API2\StoreCurrencyAppleIAPRecharge;
use Leven\Packages\Currency\Processes\AppStorePay as AppStorePayProcess;

class CurrencyApplePayController extends Controller
{
    /**
     * 发起充值订单.
     *
     * @param StoreCurrencyAppleIAPRecharge $request
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function store(StoreCurrencyAppleIAPRecharge $request)
    {
        $user = $request->user();
        $amount = $request->input('amount');

        $recharge = new AppStorePayProcess();

        if (($result = $recharge->createOrder((int) $user->id, (int) $amount)) !== false) {
            return response()->json($result, 201);
        }

        return response()->json(['message' => '操作失败'], 500);
    }

    /**
     * 主动取回凭据.
     *
     * @param Request $request
     * @param CurrencyOrderModel $currencyOrder
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function retrieve(Request $request, CurrencyOrderModel $order)
    {
        $receipt = $request->input('receipt');

        $retrieve = new AppStorePayProcess();
        if ($retrieve->verifyReceipt($receipt, $order) === true) {
            return response()->json($order, 200);
        }

        return response()->json(['message' => '操作失败'], 500);
    }

    /**
     * apple商品列表.
     *
     * @param CommonConfig $config
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function productList(CommonConfig $config)
    {
        $products = ($datas = $config->where('name', 'product')->where('namespace', 'apple')->first()) ? json_decode($datas->value) : [];

        return response()->json($products);
    }
}
