<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Keyword' => 'nullable|string|max:255',
            'gender' => 'nullable|string',
            'category_id' => 'nullable|integer|exists:categories,id',
            'date' => 'nullable|date_format:Y-m-d',
        ];
    }


    public function messages(): array
    {
        return [
            'category_id.exists' => '正しいお問い合わせの種類を選択してください。',
            'date.date_format' => '正しい日付の形式で入力してください。',
        ];
    }
}
