<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'category_id' => 'required|exists:categories,id',
            'message' => 'required|string|max:1000',
            'imageFiles'   => 'sometimes|array',
            'imageFiles.*' => 'image|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'latitude.required' => '緯度は必須です。',
            'latitude.numeric' => '緯度は数値で入力してください。',
            'latitude.between'  => '緯度は -90 〜 90 の範囲で入力してください。',

            'longitude.required' => '経度は必須です。',
            'longitude.numeric'  => '経度は数値で入力してください。',
            'longitude.between'  => '経度は -180 〜 180 の範囲で入力してください。',
            'category_id.required' => 'カテゴリを選択してください。',
            'category_id.exists'   => '存在しないカテゴリです。',
            'message.required'     => 'メッセージは必須です。',
            'message.max'          => 'メッセージは1000文字以内で入力してください。',
            'imageFiles.array' => '画像は配列形式で送信してください。',
            'imageFiles.*.image'   => 'アップロードできるのは画像ファイルのみです。',
            'imageFiles.*.max'     => '画像ファイルは2MB以内でアップロードしてください。',
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
