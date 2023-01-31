<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
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
                "id"=> $this->company_code,
                "name"=> $this->company,
            ],
            "department"=>[
                "id"=> $this->department_code,
                "name"=> $this->department,
            ],
            "location"=>[
                "id"=> $this->location_code,
                "name"=> $this->location,
            ],
            "scope"=> $this->scope,
            "username"=> $this->username,
            "token"=>$this->token
          
        ];

    }
}
