<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'code',
        'photo',
        'uom',
        'sku',
        'qty',
        'buyingprice',
        'sellingprice',
        'category_id',
        'brand_id'
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }
    public function brand()
    {
        return $this->belongsTo('App\Models\Brand');
    }

    public function importtowarehouses()
    {
        return $this->belongsToMany('App\Models\ImportToWarehouse', 'importdetails', 'importtowarehouse_id', 'product_id')
            ->withPivot('qty', 'rate', 'productamount')
            ->withTimestamps();
    }

    public function saleinvoices()
    {
        return $this->belongsToMany('App\Models\SaleInvoice', 'saledetails', 'saleinvoice_id', 'product_id')
            ->withPivot('qty', 'rate', 'productamount')
            ->withTimestamps();
    }
}
