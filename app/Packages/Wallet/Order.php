<?php

declare(strict_types=1);



namespace Leven\Packages\Wallet;

use Leven\Models\WalletOrder as WalletOrderModel;

class Order
{
    /**
     * target types.
     */
    const TARGET_TYPE_USER = 'user';                           // 用户之间转账
    const TARGET_TYPE_RECHARGE_PING_P_P = 'recharge_ping_p_p'; // Ping ++ 充值
    const TARGET_TYPE_CREDIT = 'credit';                        // 信用卡套现积分
    const TARGET_TYPE_REWARD = 'reward';                       // 打赏
    const TARGET_TYPE_WITHDRAW = 'widthdraw';                  // 提现
    const TARGET_TYPE_TRANSFORM = 'transform';                 // 兑换货币、积分

    /**
     * types.
     */
    const TYPE_INCOME = 1;    // 收入
    const TYPE_EXPENSES = -1; // 支出

    /**
     * state types.
     */
    const STATE_WAIT = 0;    // 等待
    const STATE_SUCCESS = 1; // 成功
    const STATE_FAIL = -1;   // 失败

    /**
     * The order model.
     *
     * @var \Leven\Models\WalletOrder
     */
    protected $order;

    /**
     * Init order or create a order.
     *
     * @param mixed $order
     * @param array $args see static::createOrder method
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __construct($order = null)
    {
        if ($order instanceof WalletOrderModel) {
            $this->setOrderModel($order);
        }
    }

    /**
     * Set order model.
     *
     * @param \Leven\Models\WalletOrder $order
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function setOrderModel(WalletOrderModel $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order model.
     *
     * @return \Leven\Models\WalletOrder
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function getOrderModel(): WalletOrderModel
    {
        return $this->order;
    }

    /**
     * Has order success.
     *
     * @return bool
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function hasSuccess(): bool
    {
        return $this->getOrderModel()->state === static::STATE_SUCCESS;
    }

    /**
     * Has order fail.
     *
     * @return bool
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function hasFail(): bool
    {
        return $this->getOrderModel()->state === static::STATE_FAIL;
    }

    /**
     * Has order wait.
     *
     * @return bool
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function hasWait(): bool
    {
        return $this->getOrderModel()->state === static::STATE_WAIT;
    }

    /**
     * Save order save method.
     *
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function save()
    {
        return $this->getOrderModel()->save();
    }

    /**
     * Save and set order [state=1].
     *
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function saveStateSuccess()
    {
        $this->getOrderModel()->state = static::STATE_SUCCESS;

        return $this->save();
    }

    /**
     * Save and set order [state=-1].
     *
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function saveStateFial()
    {
        $this->getOrderModel()->state = static::STATE_FAIL;

        return $this->save();
    }

    /**
     * Auth complete.
     *
     * @return bool
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function autoComplete(...$arguments): bool
    {
        if (! $this->hasWait()) {
            return true;
        }

        $manager = $this->getTargetTypeManager();

        $manager->setOrder($this);


        return $manager->handle(...$arguments);
    }

    /**
     * Get TargetTypeManager instance.
     *
     * @return \Leven\Packages\Wallet\TargetTypeManager
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function getTargetTypeManager(): TargetTypeManager
    {
        return app(TargetTypeManager::class);
    }
}
