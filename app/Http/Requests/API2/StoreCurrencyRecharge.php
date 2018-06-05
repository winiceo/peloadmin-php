<?php

declare(strict_types=1);



namespace Leven\Http\Requests\API2;

use Leven\Repository\CurrencyConfig;
use Illuminate\Foundation\Http\FormRequest;
use Leven\Repository\WalletRechargeType;

class StoreCurrencyRecharge extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user() && config('currency.recharge.status', true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param \Leven\Repository\WalletRechargeType $repository
     * @return array
     * @author BS <414606094@qq.com>
     */
    public function rules(WalletRechargeType $repository, CurrencyConfig $config)
    {
        return [
            'type' => 'required|in:'.implode(',', $repository->get()),
            'amount' => 'required|int|min:100|max:'.$config->get()['recharge-max'],
            'extra' => 'array',
        ];
    }

    /**
     * Get the valodation error message that apply to the request.
     *
     * @return array
     * @author BS <414606094@qq.com>
     */
    public function messages()
    {
        return [
            'type.required' => '请选择充值方式',
            'type.in' => '选择的充值方式错误',
            'amount.required' => '请选择需要充值金额',
            'amount.min' => '充值金额不合法',
            'amount.max' => '充值金额超出最大充值限制',
            'extra.array' => '参数错误',
        ];
    }
}
