<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'code'=> [
                'required',
                'string',
                
                $this->route()->id
                    ? 'unique:users,account_code,'.$this->route()->id
                    : 'unique:users,account_code'
            ],
            'name'=> [
                'required',
                'string',
                
                $this->route()->id
                    ? 'unique:users,account_name,'.$this->route()->id
                    : 'unique:users,account_name'
            ],
            'location.code'=> 'required',
            'location.name'=> 'required',
            'department.code'=> 'required',
            'department.name'=> 'required',
            'company.code'=> 'required',
            'company.name'=> 'required',
            'scope_id'=> 'nullable',
            'type'=> 'required',
            'mobile_no'=> [
                'required',
                'regex:[09]',
                'digits:11',
                $this->route()->id
                    ? 'unique:users,mobile_no,'.$this->route()->id
                    : 'unique:users,mobile_no'
            ],
            'username'=> [
                'required',
                'string',
                $this->route()->id
                    ? 'unique:users,username,'.$this->route()->id
                    : 'unique:users,username'
            ],
          
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // $validator->errors()->add("custom", "STOP!");
            // $validator->errors()->add("custom", $this->route()->id);
        });
    }
}
