<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Auth;
class TestRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        Log::info('authorize');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        Log::info('rules');
        return [
            'group_name' => 'required',
            'all_dialog_flg' => 'in:0,1',
            'library_sheet_type' => 'required',
        ];
    }
    public function response(array $errors) {
        Log::info('response');
        dd(321312312312);
        return \Response::json($errors, 500);
    }


    public function messages()
    {
        Log::info('messages');
        return [
            'title.required' => 'Please enter a title.',
//            'body.required'  => 'A message is required',
        ];
    }
}
