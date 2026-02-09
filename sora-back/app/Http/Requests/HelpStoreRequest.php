<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class HelpStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => ['max:1000'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'address' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.max' => 'メッセージは1000文字以内で入力してください。',

            'city_id.required' => '都市を選択してください。',
            'city_id.exists' => '選択した都市は存在しません。',

            'address.required' => '住所は必須です。',
            'address.max' => '住所は255文字以内で入力してください。',

            'latitude.required' => '緯度は必須です。',
            'latitude.numeric' => '緯度は数値で入力してください。',
            'latitude.between' => '緯度は -90 〜 90 の範囲で入力してください。',

            'longitude.required' => '経度は必須です。',
            'longitude.numeric' => '経度は数値で入力してください。',
            'longitude.between' => '経度は -180 〜 180 の範囲で入力してください。',
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
