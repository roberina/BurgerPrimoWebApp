<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title', 'message', 'type',
        'bg_color', 'text_color',
        'is_active', 'starts_at', 'ends_at', 'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'starts_at'  => 'datetime',
        'ends_at'    => 'datetime',
        'sort_order' => 'integer',
    ];

    /** Only return announcements that are active and within their date window. */
    public function scopeVisible($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }
}