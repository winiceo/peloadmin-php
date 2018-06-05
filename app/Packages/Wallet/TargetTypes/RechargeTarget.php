<?php

declare(strict_types=1);



namespace Leven\Packages\Wallet\TargetTypes;

use DB;
use Leven\Packages\Wallet\Order;
use Leven\Packages\Wallet\Wallet;

class RechargeTarget extends Target
{
    const ORDER_TITLE = '充值';
    protected $wallet;

    protected $method = [
        Order::TYPE_INCOME => 'increment',
        Order::TYPE_EXPENSES => 'decrement',
    ];

    /**
     * Handle.
     *
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function handle(): bool
    {
        if (! $this->order->hasWait()) {
            return true;
        }

        $this->initWallet();

        $orderHandle = function () {
            $this->order->saveStateSuccess();
            $this->wallet->{$this->method[$this->order->getOrderModel()->type]}($this->order->getOrderModel()->amount);

            return true;
        };
        $orderHandle->bindTo($this);

        if (($result = DB::transaction($orderHandle)) === true) {
            $this->sendNotification();
        }

        return $result;
    }

    /**
     * 完成后的通知操作.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    protected function sendNotification()
    {
        // TODO
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
