<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ShowItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'access_code' => ['required', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('access_code')) {
                $exists = \App\Models\Site::where('access_code', $this->input('access_code'))->exists();
                if (! $exists) {
                    $validator->errors()->add('access_code', '指定された access_code に紐づくサイトが存在しません。');
                }
            }
        });
    }
}
