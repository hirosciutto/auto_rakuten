<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexItemsRequest extends FormRequest
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
            'keyword' => ['nullable', 'string', 'max:256'],
            'or_flag' => ['nullable', 'integer', 'in:0,1'],
            'ng_keyword' => ['nullable', 'string', 'max:256'],
            'genre_id' => ['nullable', 'integer'],
            'tag_id' => ['nullable'], // 整数またはカンマ区切り
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
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
