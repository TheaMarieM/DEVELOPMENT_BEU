<?php

namespace Database\Seeders;

use App\Models\IncidentTemplate;
use App\Models\ViolationCategory;
use App\Models\ViolationClause;
use Illuminate\Database\Seeder;

class IncidentTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get violation categories
        $tardiness = ViolationCategory::where('name', 'like', '%Tardiness%')->first();
        $absences = ViolationCategory::where('name', 'like', '%Absences%')->first();
        $disrespect = ViolationCategory::where('name', 'like', '%Disrespect%')->first();
        $cheating = ViolationCategory::where('name', 'like', '%Academic%')->orWhere('name', 'like', '%Cheating%')->first();

        // Common incident templates
        $templates = [
            [
                'name' => 'Late Arrival - Morning',
                'description' => 'Student arrived late for morning classes',
                'violation_category_id' => $tardiness?->id,
                'default_location' => 'School Gate / Classroom',
                'default_description' => 'Student arrived late to school/class without valid excuse. Student was reminded of attendance policy.',
                'default_action' => 'Verbal warning issued. Student reminded of attendance requirements.',
            ],
            [
                'name' => 'Unexcused Absence',
                'description' => 'Student absent without prior notice or valid excuse',
                'violation_category_id' => $absences?->id,
                'default_location' => 'Classroom',
                'default_description' => 'Student was absent from class without prior notification or valid excuse letter.',
                'default_action' => 'Parent/guardian to be notified. Student required to submit excuse letter.',
            ],
            [
                'name' => 'Disrespectful Behavior',
                'description' => 'Student showed disrespect to teacher or staff',
                'violation_category_id' => $disrespect?->id,
                'default_location' => 'Classroom',
                'default_description' => 'Student exhibited disrespectful behavior towards school personnel.',
                'default_action' => 'Counseling session scheduled. Parent conference required.',
            ],
            [
                'name' => 'Academic Dishonesty - Copying',
                'description' => 'Student caught copying during examination',
                'violation_category_id' => $cheating?->id,
                'default_location' => 'Examination Room',
                'default_description' => 'Student was observed copying from another student\'s paper during examination.',
                'default_action' => 'Examination paper confiscated. Zero grade given for the examination.',
            ],
            [
                'name' => 'Classroom Disruption',
                'description' => 'Student disrupted class activities',
                'violation_category_id' => null,
                'default_location' => 'Classroom',
                'default_description' => 'Student disrupted classroom activities, affecting the learning environment.',
                'default_action' => 'Verbal warning issued. Student reminded of classroom rules.',
            ],
            [
                'name' => 'Uniform Violation',
                'description' => 'Student not in proper uniform',
                'violation_category_id' => null,
                'default_location' => 'School Premises',
                'default_description' => 'Student was found not wearing the prescribed school uniform.',
                'default_action' => 'Student reminded of uniform policy. Parent notified if repeat offense.',
            ],
            [
                'name' => 'Phone Use in Class',
                'description' => 'Unauthorized phone use during class',
                'violation_category_id' => null,
                'default_location' => 'Classroom',
                'default_description' => 'Student was observed using mobile phone during class without permission.',
                'default_action' => 'Phone confiscated until end of day. Verbal warning issued.',
            ],
            [
                'name' => 'Cutting Class',
                'description' => 'Student left class without permission',
                'violation_category_id' => $absences?->id,
                'default_location' => 'School Premises',
                'default_description' => 'Student left classroom/school premises without proper authorization.',
                'default_action' => 'Parent notification. Counseling session scheduled.',
            ],
        ];

        foreach ($templates as $template) {
            IncidentTemplate::firstOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}
