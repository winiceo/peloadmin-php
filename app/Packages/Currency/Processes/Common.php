<?php

declare(strict_types=1);



namespace Leven\Packages\Currency\Processes;

use Leven\Packages\Currency\Order;
use Leven\Packages\Currency\Process;
use Leven\Models\CurrencyOrder as CurrencyOrderModel;

class Common extends Process
{
    /**
     * 创建默认积分流水订单.
     *
     * @param int $owner_id
     * @param int $amount
     * @param string $title
     * @param string $body
     * @return Leven\Models\CurrencyOrder
     * @author BS <414606094@qq.com>
     */
    public function createOrder(int $owner_id, int $amount, int $type, string $title, string $body): CurrencyOrderModel
    {
        $user = $this->checkUser($owner_id);

        $order = new CurrencyOrderModel();
        $order->owner_id = $user->id;
        $order->title = $title;
        $order->body = $body;
        $order->type = $type;
        $order->currency = $this->currency_type->id;
        $order->target_type = Order::TARGET_TYPE_COMMON;
        $order->target_id = 0;
        $order->amount = $amount;

        return $order;
    }
}
