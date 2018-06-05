<?php

declare(strict_types=1);



namespace Leven\Packages\Wallet\TargetTypes;

use DB;
use Leven\Packages\Wallet\Order;
use Leven\Packages\Wallet\Wallet;
use Leven\Models\WalletCash as WalletCashModel;

class WidthdrawTarget extends Target
{
    const ORDER_TITLE = '提现';
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
    public function handle($type, $address,$coin_id): bool
    {
        if (! $this->order->hasWait()) {
            return true;
        }


        $this->initWallet();


        $orderHandle = function () use ($type, $address,$coin_id) {
            $this->order->saveStateSuccess();

            $this->wallet->{$this->method[$this->order->getOrderModel()->type]}($this->order->getOrderModel()->amount);

            $this->createCash($type, $address,$coin_id);

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
        $this->wallet = new Wallet(
            $this->order->getOrderModel()->owner_id,
            $this->order->getOrderModel()->coin_id

        );
    }

    /**
     * 创建提现申请.
     *
     * @param $type
     * @param $account
     * @return void
     * @author BS <414606094@qq.com>
     */
    protected function createCash($type, $address,$coin_id)
    {


        $cashModel = new WalletCashModel();
        $cashModel->user_id = $this->order->getOrderModel()->owner_id;
        $cashModel->amount = $this->order->getOrderModel()->amount;
        $cashModel->type = $type;
        $cashModel->address = $address;
        $cashModel->coin_id = $coin_id;


        $cashModel->status = 0;

        $cashModel->save();
    }
}
