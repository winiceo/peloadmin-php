<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\API2\Controllers;

use Illuminate\Http\Request;
use Leven\Packages\Wallet\Order;
use Leven\Packages\Wallet\TypeManager;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\News;

class NewRewardController extends Controller
{
    /**
     * 元到分转换比列.
     */
    const RATIO = 100;

    /**
     * 打赏一条资讯.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request
     * @param  News $news
     * @return mix
     */
    public function reward(Request $request, News $news, TypeManager $manager)
    {
        $amount = (int) $request->input('amount');
        if (! $amount || $amount < 0) {
            return response()->json(['amount' => ['请输入正确的打赏金额']], 422);
        }

        $user = $request->user();

        $target = $news->user;

        if ($user->id == $target->id) {
            return response()->json(['message' => ['不能打赏自己的发布的资讯']], 422);
        }

        if (! $user->newWallet || $user->newWallet->balance < $amount) {
            return response()->json(['message' => ['余额不足']], 403);
        }

        $money = $amount / self::RATIO;

        // 记录订单
        $status = $manager->driver(Order::TARGET_TYPE_REWARD)->reward([
            'reward_resource' => $news,
            'order' => [
                'user' => $user,
                'target' => $target,
                'amount' => $amount,
                'user_order_body' => sprintf('打赏《%s》资讯，钱包扣除%s元', $news->title, $money),
                'target_order_body' => sprintf('资讯《%s》被打赏，钱包增加%s元', $news->title, $money),
            ],
            'notice' => [
                'type' => 'news:reward',
                'detail' => ['user' => $user, 'news' => $news],
                'message' => sprintf('你的资讯《%s》被用户%s打赏%s元', $news->title, $user->name, $money),
            ],
        ]);

        if ($status === true) {
            return response()->json(['message' => ['打赏成功']], 201);
        } else {
            return response()->json(['message' => ['打赏失败']], 500);
        }
    }
}
