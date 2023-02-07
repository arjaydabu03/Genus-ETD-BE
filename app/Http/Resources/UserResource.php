<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "type"=> $this->type,
            "mobile_no"=> $this->mobile_no,
            "account"=>[
                "code"=> $this->account_code,
                "name"=> $this->account_name,
            ],
            "company"=>[
                "code"=> $this->company_code,
                "name"=> $this->company,
            ],
            "department"=>[
                "code"=> $this->department_code,
                "name"=> $this->department,
            ],
            "location"=>[
                "code"=> $this->location_code,
                "name"=> $this->location,
            ],
            "username"=> $this->username,
            "scope"=> $this->scope,
            "updated_at"=> $this->updated_at
        ];

       
        
        
    }
}
