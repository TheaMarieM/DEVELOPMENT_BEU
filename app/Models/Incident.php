<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'incident_number',
        'incident_date',
        'location',
        'description',
        'reported_by',
        'violation_category_id',
        'violation_clause_id',
        'violation_clause_option_id',
        'custom_violation_description',
        'status',
        'non_student_participant',
        'is_parent_notified',
        'action_taken',
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'is_parent_notified' => 'boolean',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function category()
    {
        return $this->belongsTo(ViolationCategory::class, 'violation_category_id');
    }

    public function clause()
    {
        return $this->belongsTo(ViolationClause::class, 'violation_clause_id');
    }

    public function clauseOption()
    {
        return $this->belongsTo(\App\Models\ViolationClauseOption::class, 'violation_clause_option_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'incident_students')
            ->withPivot('narrative_report', 'narrative_file_path', 'offense_count', 'sanction_id')
            ->withTimestamps();
    }

    public function notifications()
    {
        return $this->hasMany(ParentNotification::class);
    }

    public function approvals()
    {
        return $this->hasMany(IncidentApproval::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($incident) {
            if (!$incident->incident_number) {
                $incident->incident_number = 'INC-' . now()->format('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
