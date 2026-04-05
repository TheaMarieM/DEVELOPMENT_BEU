<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolationClauseOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'violation_clause_id',
        'label',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function clause()
    {
        return $this->belongsTo(ViolationClause::class, 'violation_clause_id');
    }
}
