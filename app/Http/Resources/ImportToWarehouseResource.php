<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use App\Models\User;
use App\Http\Resources\UserResource;

class ImportToWarehouseResource extends JsonResource
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
            'import_id' => $this->id,
            'import_date' => $this->date,
            'import_invoice_date' => $this->invoicedate,
            'import_code' => $this->importcode,
            'import_vendorid' => $this->vendor_id,
            'import_vendor' => new VendorResource(Vendor::find($this->vendor_id)),
            'import_invoiceno' => $this->invoiceno,
            'import_photo' => $baseurl . '/' . $this->photo,
            'import_total_amount' => $this->totalamount,
            'import_remark' => $this->remark,
            'import_product' => $this->products,
            'created_by' => $this->created_by,
            'created_user' => new UserResource(User::find($this->created_by)),
            'updated_by' => $this->updated_by,
            'updated_user' => new UserResource(User::find($this->updated_by)),
            'created_at' => $this->created_at->format('d-m-Y , H:i'),
            'updated_at' => $this->updated_at->format('d-m-Y , H:i'),

        ];
    }
}
