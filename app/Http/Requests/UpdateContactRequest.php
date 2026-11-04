<?php

namespace App\Http\Requests;

// すでに作成済みの StoreContactRequest をインポート
use App\Http\Requests\StoreContactRequest;

class UpdateContactRequest extends StoreContactRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = parent::rules();

        foreach ($rules as $key => $rule) {
            if (is_array($rule)) {
                array_unshift($rules[$key], 'sometimes');
            } else if (is_string($rule)) {
                $rules[$key] = 'sometimes|' . $rule;
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return parent::messages();
    }
}
