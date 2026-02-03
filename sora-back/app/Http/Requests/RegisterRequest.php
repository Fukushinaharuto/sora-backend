<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    // バリデーションエラーメッセージ
    public function messages(): array
    {
        return [
            'name.required' => 'ユーザー名は必須です',
            'name.string' => 'ユーザー名は文字列で入力してください',
            'name.max' => 'ユーザー名は50文字以内で入力してください',

            'email.required' => 'メールアドレスは必須です',
            'email.email' => '正しいメールアドレス形式で入力してください',
            'email.unique' => 'このメールアドレスは既に登録されています',

            'password.required' => 'パスワードは必須です',
            'password.string' => 'パスワードは文字列で入力してください',
            'password.min' => 'パスワードは6文字以上で入力してください',
            'password.confirmed' => 'パスワード確認が一致しません',
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
