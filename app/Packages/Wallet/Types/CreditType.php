<?php

declare(strict_types=1);



namespace Leven\Packages\Wallet\Types;
use Leven\Models\User as UserModel;

use Leven\Packages\Wallet\Order;
use Leven\Models\WalletOrder as WalletOrderModel;
use Leven\Packages\Wallet\TargetTypes\CreditTarget;
use Leven\Packages\Wallet\TargetTypes\RewardTarget;

class CreditType extends Type
{
    /**
     * @param $owner
     * @param $target
     * @param int $amount
     * @param $extra
     * @return bool
     */
    public function credit($owner, $amount,$coin_id)
    {


        $owner = $this->checkUserId($owner);


        $order = $this->createOrder($owner, $amount,$coin_id);

        return $order->autoComplete();
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
    protected function createOrder(int $owner, int $amount,int $coin_id): Order
    {
        $order = new WalletOrderModel();
        $order->owner_id = $owner;
        $order->target_type = Order::TARGET_TYPE_CREDIT;
        $order->target_id = 0;
        $order->coin_id = $coin_id;
        $order->title = CreditTarget::ORDER_TITLE;
        $order->type = Order::TYPE_INCOME;
        $order->amount = $amount;
        $order->state = Order::STATE_WAIT;


        return new Order($order);
    }


}
