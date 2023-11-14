<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductTransectionResource extends JsonResource
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
            'inventory_date' => $this->created_at->format('d-m-Y'),
            'type' => $this->type,
            'product' => new ProductResource(Product::find($this->product_id)),
            'opening' => $this->opening,
            'in' => $this->in,
            'out' => $this->out,
            'closing' => $this->closing,
        ];
    }
}
