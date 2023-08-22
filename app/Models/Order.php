<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['customer', 'paid', 'total_amount', 'discount_amount', 'payable_amount', 'paid_amount'];

    /**
     * Relationship: customers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer', 'email');
    }

    /**
     * Relationship: order_items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Relationship: products
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'order_items', 'order_id', 'product_id')->withPivot('quantity');
    }

    /**
     * Model deleting events to delete related items
     * Delete OrderItems on delete order
     */
    public static function boot()
    {
        parent::boot();
        self::deleting(function ($order) {
            $order->items()->each(function ($item) {
                $item->delete();
            });
        });
    }

    /**
     * Reset order net price
     * Calculate net price from all items/products and quantity    
     */
    public function updateCart()
    {
        $total_amount = 0;
        $payable_amount = 0;
        foreach ($this->items as $item) {
            $total_amount = $total_amount + ($item->price * $item->quantity);
        }
        $this->total_amount = round($total_amount, 2);
        $this->payable_amount = round($total_amount, 2);
        $this->save();
    }
}
