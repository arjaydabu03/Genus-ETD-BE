<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'order_no'=>'required',
            'dates.date_ordered'=>'required|date',
            'dates.date_needed'=>'required|date',
            'company.id'=>'required',
            'company.name'=>'required',
            'department.id'=>'required',
            'department.name'=>'required',
            'location.id'=>'required',
            'location.name'=>'required',
            'customer.code'=>'required',
            'customer.name'=>'required',
            'material.code'=>'required',
            'material.name'=>'required',
            'category.id'=>'required',
            'category.name'=>'required',
            'quantity'=>'required',
            'remarks'=>'nullable',
            'is_approved'=>'nullable',
        ];
    }
}
