<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Leven\Http\Requests\API2\StoreCurrencyCash;
use Leven\Packages\Currency\Processes\Cash as CashProcess;

class CurrencyCashController extends Controller
{
    /**
     * 发起提现订单.
     *
     * @param StoreCurrencyCash $request
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function store(StoreCurrencyCash $request)
    {
        $user = $request->user();
        $amount = $request->input('amount');

        $cash = new CashProcess();

        if ($cash->createOrder($user->id, (int) $amount) !== false) {
            return response()->json(['message' => '积分提取申请已提交，请等待审核'], 201);
        }

        return response()->json(['message' => '操作失败'], 500);
    }
}
