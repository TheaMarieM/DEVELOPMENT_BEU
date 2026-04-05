<?php

namespace Database\Seeders;

use App\Models\ViolationCategory;
use App\Models\ViolationClause;
use Illuminate\Database\Seeder;

class CategoryOneViolationsSeeder extends Seeder
{
    public function run(): void
    {
        $category = ViolationCategory::firstOrCreate(
            ['name' => 'Category I - Grades 4 to 12'],
            [
                'description' => 'Category I offenses for Grades 4 to 12.',
                'severity' => 'minor',
                'requires_parent_notification' => true,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $clauses = [
            ['clause_number' => '01', 'description' => 'Failure to get an admission slip'],
            ['clause_number' => '02', 'description' => 'Failure to bring a letter of excuse duly signed by parent or guardian'],
            ['clause_number' => '03', 'description' => 'Climbing, passing over the sidewalk railings, school wall, fence and trees'],
            ['clause_number' => '04', 'description' => 'Spitting/Littering anywhere on campus'],
            ['clause_number' => '05', 'description' => 'Roaming around the campus during class hours.'],
            ['clause_number' => '06', 'description' => 'Borrowing books/notes/equipment during class hours.'],
            ['clause_number' => '07', 'description' => 'Misbehavior during prayer time/flag ceremony'],
            ['clause_number' => '08', 'description' => 'Failure to perform assignment tasks (e.g. cleaning of classroom)'],
            ['clause_number' => '09', 'description' => 'Playing of video games during class hours (confiscation is part of the penalty)'],
            ['clause_number' => '10', 'description' => 'Bringing of pets, laser pointers, and other harmful electronic equipment'],
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
