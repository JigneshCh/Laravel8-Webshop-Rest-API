<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['sku', 'productname', 'price'];

    /**
     * Relationship: orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orders()
    {
        return $this->belongsToMany('App\Models\Order', 'order_items', 'order_id', 'product_id');
    }
}
