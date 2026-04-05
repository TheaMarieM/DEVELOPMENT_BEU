<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolationClause extends Model
{
    use HasFactory;

    protected $fillable = [
        'violation_category_id',
        'clause_number',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ViolationCategory::class, 'violation_category_id');
    }

    public function sanctions()
    {
        return $this->hasMany(Sanction::class);
    }

    public function options()
    {
        return $this->hasMany(ViolationClauseOption::class, 'violation_clause_id');
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
