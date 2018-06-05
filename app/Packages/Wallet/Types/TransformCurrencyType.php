<?php

declare(strict_types=1);



namespace Leven\Packages\Wallet\Types;

use Leven\Packages\Wallet\Order;
use Leven\Models\User as UserModel;
use Leven\Models\WalletOrder as WalletOrderModel;
use Leven\Packages\Wallet\TargetTypes\TransformCurrencyTarget;

class TransformCurrencyType extends Type
{
    /**
     * 钱包兑换积分.
     *
     * @param $owner
     * @param int $amount
     * @return bool
     * @author BS <414606094@qq.com>
     */
    public function transform($owner, int $amount): bool
    {
        $owner = $this->checkUserId($owner);
        $order = $this->createOrder($owner, $amount);

        return $order->autoComplete();
    }

    /**
     * Create Order.
     *
     * @param int $owner
     * @param int $amount
     * @return Leven\Models\WalletOrderModel
     * @author BS <414606094@qq.com>
     */
    protected function createOrder(int $owner, int $amount): Order
    {
        $order = new WalletOrderModel();
        $order->owner_id = $owner;
        $order->target_type = Order::TARGET_TYPE_TRANSFORM;
        $order->target_id = 0;
        $order->title = TransformCurrencyTarget::ORDER_TITLE;
        $order->body = '兑换积分';
        $order->type = Order::TYPE_EXPENSES;
        $order->amount = $amount;
        $order->state = Order::STATE_WAIT;

        return new Order($order);
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
}
