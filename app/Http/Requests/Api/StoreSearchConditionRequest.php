<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSearchConditionRequest extends FormRequest
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
            'frequency' => ['required', 'string', Rule::in(['once', 'daily', 'weekly', 'monthly'])],
            'total_hits' => ['nullable', 'integer', 'min:1', 'max:3000'],
            'keyword' => ['nullable', 'string', 'max:256'],
            'or_flag' => ['nullable', 'integer', 'in:0,1'],
            'ng_keyword' => ['nullable', 'string', 'max:256'],
            'shop_code' => ['nullable', 'string', 'max:64'],
            'item_code' => ['nullable', 'string', 'max:64'],
            'genre_id' => ['nullable', 'integer'],
            'tag_id' => ['nullable', 'string', 'max:128'],
            'min_price' => ['nullable', 'integer', 'min:0', 'max:999999998'],
            'max_price' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'availability' => ['nullable', 'integer', 'in:0,1'],
            'purchase_type' => ['nullable', 'integer', 'in:0,1,2'],
            'overwrite' => ['nullable', 'integer', 'in:0,1'],
            'is_active' => ['nullable', 'integer', 'in:0,1'],
        ];
    }

    /**
     * バリデーション後、access_code でサイトが存在するか確認する。
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('access_code') && $this->access_code !== '') {
                $exists = \App\Models\Site::where('access_code', $this->input('access_code'))->exists();
                if (! $exists) {
                    $validator->errors()->add('access_code', '指定された access_code に紐づくサイトが存在しません。');
                }
            }
        });
    }
}
