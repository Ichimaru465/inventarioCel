<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    // Añade esta función a tu modelo Category
    public function attributes()
    {
    return $this->belongsToMany(Attribute::class, 'attribute_category');
    }

    use HasFactory;

    protected $guarded = []; // Permite asignación masiva

    /**
     * Una categoría puede tener muchos productos.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
