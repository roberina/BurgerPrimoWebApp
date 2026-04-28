<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_courier',
        'courier_online',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function favoriteMenuItems()
{
    return $this->belongsToMany(MenuItem::class, 'user_menu_item_favorites')
        ->withTimestamps();
}

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_courier' => 'boolean',
            'courier_online' => 'boolean',
        ];
    }

    // ADD THESE RELATIONSHIPS:
    
    /**
     * Get all custom burgers for this user
     */
    public function customBurgers(): HasMany
    {
        return $this->hasMany(CustomBurger::class);
    }

    /**
     * Get all orders for this user
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function courierOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'courier_user_id');
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }
}