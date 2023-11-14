<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleInvoice extends Model
{
    use
        HasFactory,
        SoftDeletes;

    protected $fillable = [
        'saledate', 'orderno', 'totalamount', 'remark', 'created_by', 'updated_by'
    ];


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\ProductTransection');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'saledetails', 'saleinvoice_id', 'product_id')
            ->withPivot('qty', 'rate', 'productamount')
            ->withTimestamps();
    }
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->created_by = auth()->id();
        });

        self::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }
}
