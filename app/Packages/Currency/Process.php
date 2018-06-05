<?php

declare(strict_types=1);



namespace Leven\Packages\Currency;

use Leven\Models\User as UserModel;
use Leven\Models\CurrencyType as CurrencyTypeModel;

class Process
{
    /**
     * 货币类型.
     *
     * @var [CurrencyTypeModel]
     */
    protected $currency_type;

    public function __construct()
    {
        $this->currency_type = CurrencyTypeModel::findOrFail(1);
    }

    /**
     * 检测用户模型.
     *
     * @param $user
     * @return UserModel | bool
     * @author BS <414606094@qq.com>
     */
    public function checkUser($user, $throw = true)
    {
        if (is_numeric($user)) {
            $user = UserModel::find((int) $user);
        }

        if (! $user) {
            if ($throw) {
                throw new \Exception('找不到所属用户', 1);
            }

            return false;
        }

        return $this->checkCurrency($user);
    }

    /**
     * 检测用户货币模型，防止后续操作出现错误.
     *
     * @param UserModel $user
     * @return UserModel
     * @author BS <414606094@qq.com>
     */
    protected function checkCurrency(UserModel $user): UserModel
    {
        if (! $user->currency) {
            $user->currency = $user->currency()->create(['type' => $this->currency_type->id, 'sum' => 0]);
        }

        return $user;
    }
}
