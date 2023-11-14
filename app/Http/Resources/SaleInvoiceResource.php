<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Http\Resources\UserResource;

class SaleInvoiceResource extends JsonResource
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
            'sale_id' => $this->id,
            'sale_date' => $this->saledate,
            'sale_code' => $this->orderno,
            'sale_total_amount' => $this->totalamount,
            'sale_remark' => $this->remark,
            'sale_product' => $this->products,
            // 'import_pivot' => $this->products->pivot,
            'created_by' => $this->created_by,
            'created_user' => new UserResource(User::find($this->created_by)),
            'updated_by' => $this->updated_by,
            'updated_user' => new UserResource(User::find($this->updated_by)),
            'created_at' => $this->created_at->format('d-m-Y , H:i'),
            'updated_at' => $this->updated_at->format('d-m-Y , H:i'),
        ];
    }
}
