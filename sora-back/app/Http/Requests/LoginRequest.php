<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
            ],
            'city_id' => [
                'required',
                'integer',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスは必須です',
            'email.string' => 'メールアドレスは文字列で入力してください',
            'email.email' => '正しいメールアドレス形式で入力してください',
            'email.max' => 'メールアドレスは255文字以内で入力してください',

            'password.required' => 'パスワードは必須です',
            'password.string' => 'パスワードは文字列で入力してください',

            'city_id.required' => '市町村区を選択されていません',
            'city_id.integer' => '市町村区を選択されていません',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'messages' => collect($validator->errors()->messages())
                    ->flatten()
                    ->toArray()
            ], 422)
        );
    }
}