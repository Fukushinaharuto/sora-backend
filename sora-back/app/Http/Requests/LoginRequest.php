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
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ];
    }

    // バリデーションエラーメッセージ
    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスは必須です',
            'email.email' => '正しいメールアドレス形式で入力してください',

            'password.required' => 'パスワードは必須です',
            'password.string' => 'パスワードは文字列で入力してください',
            'password.min' => 'パスワードは6文字以上で入力してください',
        ];
    }

    // バリデーション失敗時のレスポンス形式統一
    protected function failedValidation(Validator $validator)
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
