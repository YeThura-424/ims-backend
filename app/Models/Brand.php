<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory,
        SoftDeletes;
    protected $fillable = [
        'name', 'code'
    ];

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }
}
