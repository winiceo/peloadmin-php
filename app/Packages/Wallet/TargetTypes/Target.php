<?php

declare(strict_types=1);



namespace Leven\Packages\Wallet\TargetTypes;

use Leven\Packages\Wallet\Order;

abstract class Target
{
    /**
     * The order service.
     *
     * @var \Leven\Packages\Wallet\Order
     */
    protected $order;

    /**
     * Set the order service.
     *
     * @param \Leven\Packages\Wallet\Order $order
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }
}
