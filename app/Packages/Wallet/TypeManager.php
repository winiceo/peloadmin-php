<?php

declare(strict_types=1);



namespace Leven\Packages\Wallet;

use Illuminate\Support\Manager;
use Leven\Packages\Wallet\Types\Type;

class TypeManager extends Manager
{
    /**
     * Get default type driver.
     *
     * @return string User type
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function getDefaultDriver()
    {
        return Order::TARGET_TYPE_USER;
    }

    /**
     * Create user driver.
     *
     * @return \Leven\Packages\Wallet\Types\Type
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function createUserDriver(): Type
    {
        return $this->app->make(Types\UserType::class);
    }

    /**
     * Create widthdraw driver.
     *
     * @return \Leven\Packages\Wallet\Types\Type
     * @author BS <414606094@qq.com>
     */
    protected function createWidthdrawDriver(): Type
    {
        return $this->app->make(Types\WidthdrawType::class);
    }

    /**
     * Create reward driver.
     *
     * @return \Leven\Packages\Wallet\Types\Type
     * @author hh <915664508@qq.com>
     */
    protected function createRewardDriver(): Type
    {
        return $this->app->make(Types\RewardType::class);
    }

    /**
     * 刷卡套现
     * @return Type
     */
    protected function createCreditDriver(): Type
    {
        return $this->app->make(Types\CreditType::class);
    }

    /**
     * Create recharge driver.
     *
     * @return \Leven\Packages\Wallet\Types\Type
     * @author BS <414606094@qq.com>
     */
    protected function createRechargePingPPDriver(): Type
    {
        return $this->app->make(Types\RechargeType::class);
    }

    /**
     * Create transform driver.
     *
     * @return \Leven\Packages\Wallet\Types\Type
     * @author BS <414606094@qq.com>
     */
    protected function createTransformDriver(): Type
    {
        return $this->app->make(Types\TransformCurrencyType::class);
    }
}
