<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
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
                $this->route()->material
                    ? 'unique:materials,code,'.$this->route()->material
                    : 'unique:materials,code'
            ],
            'name'=>'required',
            'category_id'=>'required|exists:categories,id'
        ];
    }

    public function attributes()
    {
       return [
            "category_id" => "category"

       ];
    }

    public function messages()
    {
        return [
          
            "exists"  =>  ":Attribute is not registered.",
        ];
    }
}
