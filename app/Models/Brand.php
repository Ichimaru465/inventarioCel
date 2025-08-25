<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;

    protected $guarded = []; // Permite asignación masiva

    /**
     * Una marca puede tener muchos productos.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
