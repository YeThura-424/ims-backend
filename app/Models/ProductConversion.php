<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductConversion extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'source_product_id', 'to_convert_qty', 'destination_product_id', 'converted_qty'
    ];


    public function sourceProduct()
    {
        return $this->belongsTo('App\Models\Product', 'source_product_id');
    }

    public function destinationProduct()
    {
        return $this->belongsTo('App\Models\Product', 'destination_product_id');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\ProductTransection');
    }
}
