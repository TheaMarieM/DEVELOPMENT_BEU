<?php

namespace Database\Seeders;

use App\Models\ViolationCategory;
use App\Models\ViolationClause;
use App\Models\ViolationClauseOption;
use Illuminate\Database\Seeder;

class CategoryFourViolationsSeeder extends Seeder
{
    public function run(): void
    {
        $category = ViolationCategory::firstOrCreate(
            ['name' => 'Category 4 Violations'],
            [
                'description' => 'Category 4 violations for Grades 4 to 12.',
                'severity' => 'severe',
                'requires_parent_notification' => true,
                'sort_order' => 4,
                'is_active' => true,
            ]
        );

        $clauses = [
            ['clause_number' => '01', 'description' => 'Assaulting fellow students and/or teachers and staff in and out of the campus', 'options' => [
                ['label' => 'a', 'description' => 'With physical contact or physical injury', 'sort_order' => 1],
                ['label' => 'b', 'description' => 'During off-campus activities', 'sort_order' => 2],
            ]],
            ['clause_number' => '02', 'description' => 'Slanderous actions/remarks to fellow students and/or teachers and staff via print and broadcast media, internet, and other medium of communication'],
            ['clause_number' => '03', 'description' => 'Rebellious actions/remarks against the school'],
            ['clause_number' => '04', 'description' => 'Threatening fellow students, teachers, and staff, and/or persons in authority using deadly weapons'],
            ['clause_number' => '05', 'description' => 'Possession, sale or use of deadly weapons and ammunitions and all forms of explosive'],
            ['clause_number' => '06', 'description' => 'On dangerous drugs', 'options' => [
                ['label' => 'a', 'description' => 'Possession', 'sort_order' => 1],
                ['label' => 'b', 'description' => 'Coming to school under the influence', 'sort_order' => 2],
                ['label' => 'c', 'description' => 'Peddling or pushing', 'sort_order' => 3],
            ]],
            ['clause_number' => '07', 'description' => 'Enlisting, recruiting, engaging in activities with pseudo fraternities and sororities, gangs and similar groups'],
            ['clause_number' => '08', 'description' => 'Hazing'],
            ['clause_number' => '09', 'description' => 'Extortion'],
            ['clause_number' => '10', 'description' => 'Engaging in pre-marital sex'],
            ['clause_number' => '11', 'description' => 'Elopement'],
            ['clause_number' => '12', 'description' => 'Desecration of the chapel or any place considered scared in the campus'],
            ['clause_number' => '13', 'description' => 'Theft'],
            ['clause_number' => '14', 'description' => 'Illicit relationship with a school personnel'],
            ['clause_number' => '15', 'description' => 'Unauthorized pageant participation'],
            ['clause_number' => '16', 'description' => 'Any form of conspiracy'],
        ];

        $validClauseNumbers = array_column($clauses, 'clause_number');
        ViolationClause::where('violation_category_id', $category->id)
            ->whereNotIn('clause_number', $validClauseNumbers)
            ->delete();

        foreach ($clauses as $clauseData) {
            $options = $clauseData['options'] ?? [];
            unset($clauseData['options']);

            $clause = ViolationClause::updateOrCreate(
                [
                    'violation_category_id' => $category->id,
                    'clause_number' => $clauseData['clause_number'],
                ],
                [
                    'description' => $clauseData['description'],
                    'is_active' => true,
                ]
            );

            if (!empty($options)) {
                $validOptionLabels = array_column($options, 'label');
                ViolationClauseOption::where('violation_clause_id', $clause->id)
                    ->whereNotIn('label', $validOptionLabels)
                    ->delete();

                foreach ($options as $optionData) {
                    ViolationClauseOption::updateOrCreate(
                        [
                            'violation_clause_id' => $clause->id,
                            'label' => $optionData['label'],
                        ],
                        [
                            'description' => $optionData['description'],
                            'sort_order' => $optionData['sort_order'],
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
