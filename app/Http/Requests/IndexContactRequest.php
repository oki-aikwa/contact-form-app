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
            'gender' => 'nullable|string', // 'all', '1', '2', '3' などが入るためstring
            'category_id' => 'nullable|integer|exists:categories,id', // 存在するカテゴリIDかチェック
            'date' => 'nullable|date_format:Y-m-d', // 日付の形式をチェック
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
