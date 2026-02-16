<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'original_price',
        'image',
        'is_featured',
        'is_active',
        'sort_order',
        'size',
        'discount_percentage',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'discount_percentage' => 'integer',
    ];

    protected $appends = ['image_url', 'has_discount'];

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_menu_item_favorites')
            ->withTimestamps();
    }

    public function isFavoritedBy($user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->favoritedByUsers()->where('user_id', $user->id)->exists();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_percentage > 0 || ($this->original_price && $this->original_price > $this->price);
    }

    // Add this new accessor for is_favorited
    public function getIsFavoritedAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        return $this->isFavoritedBy(auth()->user());
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}