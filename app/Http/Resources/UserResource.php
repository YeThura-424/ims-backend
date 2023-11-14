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
        // return parent::toArray($request);
        return [
            'user_id' => $this->id,
            'user_profile' => $this->profile,
            'user_firstname' => $this->firstname,
            'user_lastname' => $this->lastname,
            'user_email' => $this->email,
            'user_username' => $this->username,
            'user_phone' => $this->phone,
            'user_address' => $this->address,
            'user_status' => $this->status,
            'user_education' => $this->education,
            'created_at' => $this->created_at->format('d-m-Y , H:i'),
            'updated_at' => $this->updated_at->format('d-m-Y , H:i'),
        ];
    }
}
