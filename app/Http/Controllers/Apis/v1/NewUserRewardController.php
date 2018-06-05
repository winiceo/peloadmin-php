<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Leven\Models\User;
use Illuminate\Http\Request;
use Leven\Packages\Wallet\Order;
use Leven\Packages\Wallet\TypeManager;

class NewUserRewardController extends Controller
{
    /**
     * 元到分转换比列.
     */
    const RATIO = 100;

    /**
     * 新版打赏用户.
     *
     * @param Request $request
     * @param User $target
     * @param TypeManager $manager
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, User $target, TypeManager $manager)
    {
        $amount = (int) $request->input('amount');

        if (! $amount || $amount < 0) {
            return response()->json(['amount' => '请输入正确的打赏金额'], 422);
        }

        $user = $request->user();

        if ($user->id == $target->id) {
            return response()->json(['message' => '用户不能打赏自己'], 422);
        }

        if (! $user->newWallet || $user->newWallet->balance < $amount) {
            return response()->json(['message' => '余额不足'], 403);
        }

        if (! $target->wallet) {
            return response()->json(['message' => '对方钱包信息有误'], 500);
        }

        $money = ($amount / self::RATIO);

        $status = $manager->driver(Order::TARGET_TYPE_REWARD)->reward([
            'reward_resource' => $user,
            'order' => [
                'user' => $user,
                'target' => $target,
                'amount' => $amount,
                'user_order_body' => sprintf('打赏用户%s，钱包扣除%s元', $target->name, $money),
                'target_order_body' => sprintf('被用户%s打赏，钱包增加%s元', $user->name, $money),
            ],
            'notice' => [
                'type' => 'user:reward',
                'detail' => ['user' => $user],
                'message' => sprintf('你被%s打赏%s', $user->name, $money),
            ],
        ]);

        if ($status === true) {
            return response()->json(['message' => '打赏成功'], 201);
        } else {
            return response()->json(['message' => '打赏失败'], 500);
        }
    }
}
