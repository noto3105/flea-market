<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'postcode'=>['required', 'digits:7'],
            'address'=>['required', 'string', 'max:255'],
            'img_url'=>['nullable', 'mimes:jpeg,png']
        ];
    }

    public function message()
    {
        return [
            'postcode.required'=>'郵便番号を入力してください',
            'postcode.digits'=>'郵便番号は7桁で入力してください',
            'address.required'=>'住所を入力してください',
            'address.string'=>'住所は文字で入力してください',
            'address.max'=>'住所は255文字以内で入力してください',
            'img_url.mimes'=>'画像ファイルはjpegもしくはpng形式で入力してください',
        ];
    }
}
