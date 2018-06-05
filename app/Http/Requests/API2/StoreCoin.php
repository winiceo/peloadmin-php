<?php

declare(strict_types=1);



namespace Leven\Http\Requests\API2;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCoin extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:20|unique:coins',
            'symbol' => 'required|max:20|unique:coins'

        ];
    }

    /**
     * Get the validation message that apply to the request.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function messages(): array
    {
        return [
            'name.required' => '币种名称必填',
            'name.max' => '币种名称过长',
            'name.unique' => '币种已经存在'

        ];
    }
}
