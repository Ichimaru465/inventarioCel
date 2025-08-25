<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    // Añade esta función a tu nuevo modelo Attribute
    public function categories()
    {
    return $this->belongsToMany(Category::class, 'attribute_category');
    }
}
