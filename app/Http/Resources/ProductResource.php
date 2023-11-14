<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Models\Category;

class ProductResource extends JsonResource
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


        $baseurl = URL('/');

        return [
            'product_id' => $this->id,
            'product_name' => $this->name,
            'product_code' => $this->code,
            'product_uom' => $this->uom,
            'product_sku' => $this->sku,
            'product_qty' => $this->qty,
            'product_buying_price' => $this->buyingprice,
            'product_selling_price' => $this->sellingprice,
            'product_photo' => $this->photo ? $baseurl . '/' . $this->photo : $baseurl . '/images/no_image/noimage.png',
            'product_categoryid' => $this->category_id,
            'category' => new CategoryResource(Category::find($this->category_id)),
            'product_brandid' => $this->brand_id,
            'brand' => new BrandResource(Brand::find($this->brand_id)),
            'created_at' => $this->created_at->format('d-m-Y H:i'),
            'updated_at' => $this->updated_at->format('d-m-Y H:i'),
        ];
    }
}
