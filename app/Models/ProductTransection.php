<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTransection extends Model
{
    use HasFactory;
    protected $fillable = [
        'import_to_warehouse_id', 'sale_invoice_id', 'product_conversion_id', 'type', 'product_id', 'opening', 'in', 'out', 'closing', 'created_by'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->created_by = auth()->id();
        });
    }
}
