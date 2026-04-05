<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Incident;
use App\Models\ViolationCategory;
use App\Models\User;
use App\Models\AttendanceRecord;

class AnalyticsTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing students or create new ones
        $students = Student::limit(10)->get();
        
        if ($students->isEmpty()) {
            return;
        }

        $categories = ViolationCategory::all();
        if ($categories->isEmpty()) {
            return;
        }

        $disciplineUser = User::where('email', 'discipline@spup.edu.ph')->first() ?? User::first();
        if (!$disciplineUser) {
            return;
        }

        // Create incidents for March 2026 (current month)
        $now = now();
        $marchStart = $now->copy()->startOfMonth();
        $marchEnd = $now->copy()->endOfMonth();

        $statuses = ['reported', 'under_review', 'pending_approval', 'approved', 'closed'];
        $severities = ['minor', 'moderate', 'major', 'critical'];

        $incidentCount = 0;

        foreach ($students as $studentIndex => $student) {
            // Create 2-5 incidents per student in this month
            $numIncidents = rand(2, 5);

            for ($i = 0; $i < $numIncidents; $i++) {
                $incidentDate = $marchStart->copy()->addDays(rand(0, $marchEnd->diffInDays($marchStart)));
                $category = $categories->random();
                $status = $statuses[array_rand($statuses)];
                
                $incidentNumber = 'INC-' . $now->format('Y') . '-' . str_pad($incidentCount + 1, 5, '0', STR_PAD_LEFT);
                
                $incident = Incident::firstOrCreate(
                    ['incident_number' => $incidentNumber],
                    [
                        'incident_date' => $incidentDate,
                        'location' => 'Classroom',
                        'violation_category_id' => $category->id,
                        'description' => 'Test incident for analytics: ' . $this->getRandomDescription(),
                        'reported_by' => $disciplineUser->id,
                        'status' => $status,
                    ]
                );

                // Link incident to student
                if ($incident && !$incident->students()->where('students.id', $student->id)->exists()) {
                    $incident->students()->attach($student->id);
                }

                $incidentCount++;
            }

            // Create attendance records in March
            $this->createMarchAttendance($student);
        }

        $this->command->info("Created {$incidentCount} analytics test incidents for March 2026.");
    }

    private function createMarchAttendance($student): void
    {
        $marchStart = now()->copy()->startOfMonth();
        $marchEnd = now()->copy()->endOfMonth();

        for ($day = $marchStart->day; $day <= $marchEnd->day; $day++) {
            $date = $marchStart->copy()->day($day);

            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }

            $statuses = ['present', 'present', 'present', 'present', 'absent', 'tardy'];
            $status = $statuses[array_rand($statuses)];

            AttendanceRecord::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'date' => $date->toDateString()
                ],
                [
                    'status' => $status,
                    'recorded_by' => 1
                ]
            );
        }
    }

    private function getRandomDescription(): string
    {
        $descriptions = [
            'Disruptive behavior in classroom',
            'Late submission of assignment',
            'Incomplete/missing uniform',
            'Unauthorized absence',
            'Violating classroom conduct rules',
            'Using mobile phone during class',
            'Talking back to teacher',
            'Bullying incident',
            'Cheating on examination',
            'Vandalism of school property',
            'Fighting with classmate',
            'Using profanity',
        ];

        return $descriptions[array_rand($descriptions)];
    }
}
