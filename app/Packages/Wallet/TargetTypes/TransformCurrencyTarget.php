<?php

declare(strict_types=1);



namespace Leven\Packages\Wallet\TargetTypes;

use DB;
use Leven\Packages\Wallet\Wallet;
use Leven\Repository\CurrencyConfig;
use Leven\Packages\Currency\Processes\Recharge;

class TransformCurrencyTarget extends Target
{
    const ORDER_TITLE = '积分转换';

    public function handle(): bool
    {
        if (! $this->order->hasWait()) {
            return true;
        }

        $this->initWallet();

        $config = app(CurrencyConfig::class)->get();
        $order = $this->order->getOrderModel();
        $currency_amount = $order->amount * $config['recharge-ratio'];

        $orderHandle = function () use ($order, $currency_amount) {
            // 钱包订单部分
            $body = sprintf('充值%s积分', $currency_amount);
            $order->body = $body;
            $order->state = 1;

            $order->save();
            $this->wallet->decrement($order->amount);

            // 积分订单部分
            $currency_order = app(Recharge::class)->createOrder($order->owner_id, $currency_amount);
            $currency_order->state = 1;
            $currency_order->save();
            $currency_order->user->currency->increment('sum', $currency_amount);

            return true;
        };

        return DB::transaction($orderHandle);
    }

    /**
     * 初始化钱包.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    protected function initWallet()
    {
        $this->wallet = new Wallet($this->order->getOrderModel()->owner_id);
    }
}
