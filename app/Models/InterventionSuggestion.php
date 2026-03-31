<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterventionSuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_level',
        'section',
        'incident_type',
        'incident_count',
        'analysis_period_start',
        'analysis_period_end',
        'suggestion',
        'assigned_to',
        'assignment_due_at',
        'status',
        'decided_by',
        'decided_at',
        'decision_remarks',
    ];

    protected $casts = [
        'analysis_period_start' => 'date',
        'analysis_period_end' => 'date',
        'decided_at' => 'datetime',
        'assignment_due_at' => 'date',
    ];

    public function decisionMaker()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
