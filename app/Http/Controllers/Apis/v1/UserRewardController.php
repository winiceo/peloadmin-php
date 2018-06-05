<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Leven\Models\User;
use Illuminate\Http\Request;
use Leven\Models\GoldType;
use Leven\Models\CommonConfig;
use Leven\Models\WalletCharge;

class UserRewardController extends Controller
{
    // 系统货币名称
    protected $goldName;

    // 系统内货币与真实货币兑换比例
    protected $wallet_ratio;

    public function __construct(GoldType $goldModel, CommonConfig $configModel)
    {
        $walletConfig = $configModel->where('name', 'wallet:ratio')->first();

        $this->goldName = $goldModel->where('status', 1)->select('name', 'unit')->value('name') ?? '金币';
        $this->wallet_ratio = $walletConfig->value ?? 100;
    }

    /**
     * 打赏用户.
     *
     * @author bs<414606094@qq.com>
     * @param  Request      $request
     * @param  User         $target
     * @param  WalletCharge $chargeModel
     * @return json
     */
    public function store(Request $request, User $target, WalletCharge $chargeModel)
    {
        $amount = $request->input('amount');
        if (! $amount || $amount < 0) {
            return response()->json([
                'amount' => '请输入正确的打赏金额',
            ], 422);
        }
        $user = $request->user();
        $user->load('wallet');

        if (! $user->wallet || $user->wallet->balance < $amount) {
            return response()->json([
                'message' => '余额不足',
            ], 403);
        }

        if (! $target->wallet) {
            return response()->json([
                'message' => '对方钱包信息有误',
            ], 500);
        }

        $user->getConnection()->transaction(function () use ($user, $target, $chargeModel, $amount) {
            // 扣除操作用户余额
            $user->wallet()->decrement('balance', $amount);

            // 扣费记录
            $userCharge = clone $chargeModel;
            $userCharge->channel = 'user';
            $userCharge->account = $target->id;
            $userCharge->subject = '用户打赏';
            $userCharge->action = 0;
            $userCharge->amount = $amount;
            $userCharge->body = sprintf('打赏用户%s', $target->name);
            $userCharge->status = 1;
            $user->walletCharges()->save($userCharge);

            // 被打赏用户增加金额
            $target->wallet()->increment('balance', $amount);

            // 增加金额记录
            $chargeModel->user_id = $target->id;
            $chargeModel->channel = 'user';
            $chargeModel->account = $user->id;
            $chargeModel->subject = sprintf('被%s打赏', $user->name);
            $chargeModel->action = 1;
            $chargeModel->amount = $amount;
            $chargeModel->body = sprintf('被%s打赏', $user->name);
            $chargeModel->status = 1;
            $chargeModel->save();

            if ($user->id !== $target->id) {
                // 添加被打赏通知
                $targetNotice = sprintf('你被%s打赏%s%s', $user->name, $amount * $this->wallet_ratio / 10000, $this->goldName);
                $target->sendNotifyMessage('user:reward', $targetNotice, [
                    'user' => $user,
                ]);
            }

            // 打赏记录
            $target->reward($user, $amount);
        });

        return response()->json([
            'message' => '打赏成功',
        ], 201);
    }
}
