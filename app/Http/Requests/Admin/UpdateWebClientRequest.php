<?php

declare(strict_types=1);



namespace Leven\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWebClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function rules(): array
    {
        return [
            'web' => 'array',
            'web.url' => 'nullable|string|url',
            'web.open' => 'nullable|boolean',
            'spa' => 'array',
            'spa.url' => 'nullable|string|url',
            'spa.open' => 'nullable|boolean',
        ];
    }
}
