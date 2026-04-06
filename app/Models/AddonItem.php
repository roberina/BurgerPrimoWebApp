<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddonItem extends Model
{
    protected $fillable = [
        'type',
        'name',
        'price',
        'slug',
        'is_available',
        'sort_order',
    ];

    protected $casts = [
        'price'        => 'float',
        'is_available' => 'boolean',
        'sort_order'   => 'integer',
    ];

    // Allowed types
    public const TYPES = ['size', 'drink', 'sauce', 'fries'];

    public const TYPE_LABELS = [
        'size'  => 'Suurus',
        'drink' => 'Jook',
        'sauce' => 'Kaste',
        'fries' => 'Friikartul',
    ];

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
