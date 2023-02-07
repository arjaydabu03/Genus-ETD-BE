<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"=>$this->id,
            "order_no"=> $this->order_no,
            "dates"=>[
                "date_ordered"=> $this->date_ordered,
                "date_needed"=> $this->date_needed,
                "date_approved"=> $this->date_approved,
            ],
            "company"=>[
                "id"=> $this->company_id,
                "name"=> $this->company_name,
            ],
            "department"=>[
                "id"=> $this->department_id,
                "name"=> $this->department_name,
            ],
            "location"=>[
                "id"=> $this->location_id,
                "name"=> $this->location_name,
            ],
            "customer"=>[
                "code"=> $this->customer_code,
                "name"=> $this->customer_name,
            ],
            "order"=>[
                "material"=>[
                    "code"=> $this->material_code,
                    "name"=> $this->material_name,
                ],
                "category"=>[
                    "id"=> $this->category_id,
                    "name"=> $this->category_name,
                ],
            ],

            "quantity"=>$this->quantity,
            "remarks"=>$this->remarks,
            "is_approved"=>$this->is_approved,
        ];
    }
}
