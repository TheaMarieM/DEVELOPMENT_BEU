<?php

namespace Database\Seeders;

use App\Models\ViolationCategory;
use App\Models\ViolationClause;
use Illuminate\Database\Seeder;

class CategoryTwoViolationsSeeder extends Seeder
{
    public function run(): void
    {
        $category = ViolationCategory::firstOrCreate(
            ['name' => 'Category 2 Violations'],
            [
                'description' => 'Category 2 violations for Grades 4 to 12.',
                'severity' => 'moderate',
                'requires_parent_notification' => true,
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $clauses = [
            ['clause_number' => '01', 'description' => 'Unauthorized use of school forms/waivers'],
            ['clause_number' => '02', 'description' => 'Disobedience'],
            ['clause_number' => '03', 'description' => 'Using cellular phones and other electronic gadgets inside the school premises.'],
            ['clause_number' => '04', 'description' => 'Borrowing and/or lending of ID/library card'],
            ['clause_number' => '05', 'description' => 'Disrespecting the Philippine flag and other national symbols'],
            ['clause_number' => '06', 'description' => 'Staying in the classroom after the curfew time/recess time/lunch break, and in areas designated as off limits.'],
            ['clause_number' => '07', 'description' => 'Eating inside the classroom during class hours.'],
            ['clause_number' => '08', 'description' => 'Violation of CLAYGO policy'],
            ['clause_number' => '09', 'description' => 'Using profane and indecent language'],
            ['clause_number' => '10', 'description' => 'Public Display of Affection'],
            ['clause_number' => '11', 'description' => 'Tardiness (accumulated)'],
            ['clause_number' => '12', 'description' => 'Absenteeism (accumulated unexcused absences)'],
        ];

        $validClauseNumbers = array_column($clauses, 'clause_number');
        ViolationClause::where('violation_category_id', $category->id)
            ->whereNotIn('clause_number', $validClauseNumbers)
            ->delete();

        foreach ($clauses as $clause) {
            ViolationClause::updateOrCreate(
                [
                    'violation_category_id' => $category->id,
                    'clause_number' => $clause['clause_number'],
                ],
                [
                    'description' => $clause['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
