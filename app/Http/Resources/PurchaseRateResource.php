<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseRateResource extends JsonResource
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
            'warehouse' => $this->warehouse,
            'product_code' => $this->code,
            'product_name' => $this->name,
            'closing_qty' => $this->closing_qty,
            'purchase_rate' => $this->purchase_rate,
            'created_at' => $this->created_at,
        ];
    }
}
