<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'receipt_number',
        'subtotal',
        'discount_total',
        'total',
        'receipt_path',
        'status',
        'canceled_at',
        'canceled_by',
        'cancellation_reason',
    ];

    protected $casts = [
        'canceled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function canceledBy()
    {
        return $this->belongsTo(User::class, 'canceled_by');
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }
}

