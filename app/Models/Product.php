<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'product_img',
        'product_name',
        'product_price',
        'product_quantity',
    ];
}
