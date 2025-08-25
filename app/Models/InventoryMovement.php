<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryMovement extends Model
{
     use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'reason',
    ];

    /**
     * Obtiene el producto asociado con este movimiento de inventario.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Obtiene el usuario que realizó este movimiento de inventario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
