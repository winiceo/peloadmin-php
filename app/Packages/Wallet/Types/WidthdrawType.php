<?php

declare(strict_types=1);



namespace Leven\Packages\Wallet\Types;

use Leven\Packages\Wallet\Order;
use Leven\Models\User as UserModel;
use Leven\Models\WalletOrder as WalletOrderModel;
use Leven\Packages\Wallet\TargetTypes\WidthdrawTarget;

class WidthdrawType extends Type
{
    /**
     * 提现.
     *
     * @param int|UserModel $owner
     * @param int $amount
     * @param string $type
     * @param string $account
     * @return boolen
     * @author BS <414606094@qq.com>
     */
    public function widthdraw($owner, $amount, $coin_id, $type,$address): bool
    {

        $owner = $this->checkUserId($owner);


        $order = $this->createOrder($owner, $amount,$coin_id,$address);

        return $order->autoComplete($type, $address,$coin_id);

    }

    /**
     * Check user.
     *
     * @param int|UserModel $user
     * @return int
     * @author BS <414606094@qq.com>
     */
    protected function checkUserId($user): int
    {
        if ($user instanceof UserModel) {
            $user = $user->id;
        }

        return (int) $user;
    }

    /**
     * Create Order.
     *
     * @param int $owner
     * @param int $amount
     * @return Leven\Models\WalletOrderModel
     * @author BS <414606094@qq.com>
     */
    protected function createOrder(int $owner, int $amount,int $coin_id,string $address): Order
    {
        $order = new WalletOrderModel();
        $order->owner_id = $owner;
        $order->target_type = Order::TARGET_TYPE_WITHDRAW;
        $order->target_id = 0;
        $order->coin_id = $coin_id;
        $order->title = WidthdrawTarget::ORDER_TITLE;
        $order->type = Order::TYPE_EXPENSES;
        $order->amount = $amount;
        $order->address = $address;

        $order->state = Order::STATE_WAIT;


        return new Order($order);
    }
}
