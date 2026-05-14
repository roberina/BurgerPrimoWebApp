<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_en',
        'category',
        'price',
        'image',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function customBurgers(): BelongsToMany
    {
        return $this->belongsToMany(CustomBurger::class, 'custom_burger_ingredient')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}