<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductConversionResource extends JsonResource
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
            'conversion_id' => $this->id,
            'source_product_id' => $this->source_product_id,
            'source_product' => new ProductResource(Product::find($this->source_product_id)),
            'to_convert_qty' => $this->to_convert_qty,
            'destination_product_id' => $this->destination_product_id,
            'destination_product' => new ProductResource(Product::find($this->destination_product_id)),
            'converted_qty' => $this->converted_qty,
            'created_at' => $this->created_at->format('d-m-Y'),
            'updated_at' => $this->updated_at->format('d-m-Y H:i'),
        ];
    }
}
