<?php

namespace App\Http\Requests;

//use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class TestRequest extends FormRequest
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
            'title' => 'required',
            'name' => 'required'
        ];
    }

    public function response(array $errors) {
        dd(3123123123);
        return \Response::json($errors, 500);
    }


    public function messages()
    {
        dd('7978979');
        return [
            'title.required' => 'Please enter a title.',
//            'body.required'  => 'A message is required',
        ];
    }
}
