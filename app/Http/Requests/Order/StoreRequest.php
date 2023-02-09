<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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

        $customer_code = $this->input('customer.code');
        $material_code = $this->input('order.material.code');

        $order_no = $this->get('order_no'); 
        return [
            'order_no'=>[
                'required',
                Rule::unique('order','order_no')
                ->where(function($query)use($customer_code,$material_code){
                    return $query
                    ->where('customer_code',$customer_code)
                    ->where('material_code',$material_code);
                }),
            ],
            // 'dates.date_ordered'=>'required|date',
            'dates.date_needed'=>'nullable',
            'company.id'=>'required',
            'company.name'=>'required',
            'department.id'=>'required',
            'department.name'=>'required',
            'location.id'=>'required',
            'location.name'=>'required',
            'customer.name'=>'required',
            'customer.code'=>[
                'required',
                Rule::unique('order','customer_code')
                ->where(function ($query) use($material_code, $order_no){
                    return $query
                    ->where('order_no', $order_no)
                    ->where('material_code', $material_code);
                }),
            ],
            'order.*.material.code'=>[
                'required',
                Rule::unique('order','material_code')
                ->where(function ($query) use( $customer_code, $order_no){
                    return $query
                    ->where('order_no', $order_no)
                    ->where('customer_code', $customer_code);
                }),
            ],
            'order.*.category.id'=>'required',
            'order.*.category.name'=>'required',
            'order.*.material.name'=>'required',
            'order.*.quantity'=>'required',
            'order.*.remarks'=>'nullable',
            'is_approved'=>'nullable',
        ];
    }
}
