<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Leven\Packages\Wallet\Order;
use Leven\Packages\Wallet\TypeManager;
use Leven\Http\Requests\API2\Transfer as TransferRequest;

class TransferController extends Controller
{
    /**
     * 用户之间转账.
     *
     * @param TransferRequest $request
     * @param TypeManager $manager
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function transfer(TransferRequest $request, TypeManager $manager)
    {
        $user = $request->user();
        $target = $request->input('user');
        $amount = $request->input('amount');

        if ($manager->driver(Order::TARGET_TYPE_USER)->transfer($user, $target, $amount) === true) {
            return response()->json(['message' => '转账成功'], 201); // 成功
        }

        return response()->json(['message' => '操作失败，请稍后重试'], 500); // 失败
    }
}
