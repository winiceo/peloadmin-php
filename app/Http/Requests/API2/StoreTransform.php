<?php

declare(strict_types=1);



namespace Leven\Http\Requests\API2;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransform extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @author BS <414606094@qq.com>
     */
    public function rules(): array
    {
        $currency = $this->user()->newWallet()->firstOrCreate([], ['balance' => 0, 'total_income' => 0, 'total_expenses' => 0]);

        return [
            'amount' => 'required|int|min:100|max:'.$currency->balance,
        ];
    }

    /**
     * Get the valodation error message that apply to the request.
     *
     * @return array
     * @author BS <414606094@qq.com>
     */
    public function messages(): array
    {
        return [
            'amount.required' => '请选择需要转换的余额',
            'amount.min' => '余额参数不合法',
            'amount.max' => '账户余额不足',
        ];
    }
}
