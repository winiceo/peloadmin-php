<?php

declare(strict_types=1);



namespace Leven\Packages\Wallet\TargetTypes;

use DB;
use Leven\Packages\Wallet\Order;
use Leven\Packages\Wallet\Wallet;
use Leven\Models\WalletOrder as WalletOrderModel;

class CreditTarget extends Target
{
    const ORDER_TITLE = '刷卡奖励';
    protected $ownerWallet;     // \Leven\Packages\Wallet\Wallet
    protected $targetWallet;    // \Leven\Packages\Wallet\Wallet
    protected $targetCreditOrder; // Leven\Packages\Wallet\Order

    /**
     * Handle.
     *
     * @return mixed
     * @author hh <915664508@qq.com>
     */
    public function handle(): bool
    {


        if (! $this->order->hasWait()) {
            return true;
        }

        $this->initWallet();

       $this->createTargetCreditOrder( );

        $transaction = function ()  {
            $this->order->saveStateSuccess();
            //$this->targetCreditOrder->saveStateSuccess();
           $this->transfer($this->order, $this->ownerWallet);
           // $this->transfer($this->targetCreditOrder, $this->targetWallet);

            // 记录打赏记录
           // $this->createCreditRecord($extra['reward_resource'], $this->order);

            return true;
        };

        $transaction->bindTo($this);

        if (($result = DB::transaction($transaction)) === true) {
            // 发送消息通知
            //$this->sendNotification();
        }

        return $result;
    }

    /**
     * return target Order.
     *
     * @return mixed
     */
    public function getTargetOrder()
    {
        return $this->targetCreditOrder->getOrderModel();
    }

    /**
     * Send notification.
     *
     * @return void
     * @author hh <915664508@qq.com>
     */
    protected function sendNotification($extra)
    {
        $target = $extra['order']['target'];
        $notice = $extra['notice'];

        $target->sendNotifyMessage(
            $notice['type'],
            $notice['message'],
            $notice['detail']
        );
    }

    /**
     * Init owner and target user wallet.
     *
     * @return void
     * @author hh <915664508@qq.com>
     */
    protected function initWallet()
    {
//        // Target user wallet.
//        $this->targetWallet = new Wallet(
//            $this->order->getOrderModel()->target_id
//        );

        // owner wallet.
        $this->ownerWallet = new Wallet(
            $this->order->getOrderModel()->owner_id
        );
    }

    /**
     * Create target user order.
     *
     * @return void
     * @author hh <915664508@qq.com>
     */
    protected function createTargetCreditOrder()
    {
        $order = new WalletOrderModel();
        $order->owner_id = $this->ownerWallet->getWalletModel()->owner_id;
        $order->target_type = Order::TARGET_TYPE_CREDIT;
        $order->target_id = $this->ownerWallet->getWalletModel()->owner_id;
        $order->title = static::ORDER_TITLE;
        $order->type = $this->getTargetCreditOrderType();
        $order->amount = $this->order->getOrderModel()->amount;
        $order->state = Order::STATE_WAIT;
        $order->body = "";
        $order->coin_id = $this->ownerWallet->getWalletModel()->coin_id;

        $this->targetCreditOrder = new Order($order);
    }

    /**
     * Get target user order type.
     *
     * @return int
     * @author hh <915664508@qq.com>
     */
    protected function getTargetCreditOrderType(): int
    {
        if ($this->order->getOrderModel()->type === Order::TYPE_INCOME) {
            return Order::TYPE_EXPENSES;
        }

        return Order::TYPE_INCOME;
    }

    /**
     * Transfer.
     *
     * @param \Leven\Packages\Wallet\Order $order
     * @param \Leven\Packages\Wallet\Wallet $wallet
     * @return void
     * @author hh <915664508@qq.com>
     */
    protected function transfer(Order $order, Wallet $wallet)
    {
        $methods = [
            Order::TYPE_INCOME => 'increment',
            Order::TYPE_EXPENSES => 'decrement',
        ];
        $method = $methods[$order->getOrderModel()->type];
        $wallet->$method($order->getOrderModel()->amount);
    }

    /**
     * 记录打赏.
     *
     * @param $resource
     * @param $order
     */
    protected function createCreditRecord($resource, $order)
    {
        $orderModel = $order->getOrderModel();

        $resource->reward($orderModel->owner_id, $orderModel->amount);
    }
}
