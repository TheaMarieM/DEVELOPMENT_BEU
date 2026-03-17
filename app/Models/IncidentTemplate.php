<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'violation_category_id',
        'violation_clause_id',
        'default_location',
        'default_description',
        'default_action',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ViolationCategory::class, 'violation_category_id');
    }

    public function clause()
    {
        return $this->belongsTo(ViolationClause::class, 'violation_clause_id');
    }

    /**
     * Increment usage counter
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Scope to get active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by popularity
     */
    public function scopePopular($query)
    {
        return $query->orderByDesc('usage_count');
    }
}
