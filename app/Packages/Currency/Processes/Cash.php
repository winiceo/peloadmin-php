<?php

declare(strict_types=1);



namespace Leven\Packages\Currency\Processes;

use DB;
use Leven\Packages\Currency\Order;
use Leven\Packages\Currency\Process;
use Leven\Repository\CurrencyConfig;
use Leven\Models\CurrencyOrder as CurrencyOrderModel;

class Cash extends Process
{
    /**
     * 创建积分提取记录.
     *
     * @param int $owner_id
     * @param int $amount
     * @return Leven\Models\CurrencyOrder
     * @author BS <414606094@qq.com>
     */
    public function createOrder(int $owner_id, int $amount): CurrencyOrderModel
    {
        $user = $this->checkUser($owner_id);
        $config = app(CurrencyConfig::class)->get();

        // 积分除兑换比例取整，保证兑换的人民币为分单位的整数
        $amount = $amount - ($amount % $config['recharge-ratio']);

        $title = '积分提取';
        $body = sprintf('提取积分：%s%s%s', $amount, $this->currency_type->unit, $this->currency_type->name);

        $order = new CurrencyOrderModel();
        $order->owner_id = $user->id;
        $order->title = $title;
        $order->body = $body;
        $order->type = -1;
        $order->currency = $this->currency_type->id;
        $order->target_type = Order::TARGET_TYPE_CASH;
        $order->target_id = 0;
        $order->amount = $amount;

        return DB::transaction(function () use ($order, $user, $config) {
            $user->currency->decrement('sum', $order->amount);
            $order->save();

            return $order;
        });
    }
}
