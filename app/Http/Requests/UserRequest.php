<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'code'=> 'required|string',
            'name'=> 'required|string',
            'location.code'=> 'required',
            'location.name'=> 'required',
            'department.code'=> 'required',
            'department.name'=> 'required',
            'company.code'=> 'required',
            'company.name'=> 'required',
            'scope_id'=> 'nullable',
            'type'=> 'required',
            'mobile_no'=> 'required|string',
            'username'=> 'required|string|unique:users'
        ];
    }
}
