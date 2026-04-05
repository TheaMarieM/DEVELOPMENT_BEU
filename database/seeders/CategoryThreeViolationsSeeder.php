<?php

namespace Database\Seeders;

use App\Models\ViolationCategory;
use App\Models\ViolationClause;
use App\Models\ViolationClauseOption;
use Illuminate\Database\Seeder;

class CategoryThreeViolationsSeeder extends Seeder
{
    public function run(): void
    {
        $category = ViolationCategory::firstOrCreate(
            ['name' => 'Category 3 Violations'],
            [
                'description' => 'Category 3 violations for Grades 4 to 12.',
                'severity' => 'major',
                'requires_parent_notification' => true,
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        $clauses = [
            ['clause_number' => '01', 'description' => 'Gross or scandalous misbehavior', 'options' => [
                ['label' => 'a', 'description' => 'Inside the campus', 'sort_order' => 1],
                ['label' => 'b', 'description' => 'During off-campus activities', 'sort_order' => 2],
                ['label' => 'c', 'description' => 'Outside the campus while still wearing the school uniform', 'sort_order' => 3],
            ]],
            ['clause_number' => '02', 'description' => 'Showing disrespect to teacher or persons in authority in and out of the campus', 'options' => [
                ['label' => 'a', 'description' => 'Orally', 'sort_order' => 1],
                ['label' => 'b', 'description' => 'In writing', 'sort_order' => 2],
                ['label' => 'c', 'description' => 'Through malicious gestures', 'sort_order' => 3],
            ]],
            ['clause_number' => '03', 'description' => 'Assaulting a fellow student', 'options' => [
                ['label' => 'a', 'description' => 'Orally', 'sort_order' => 1],
                ['label' => 'b', 'description' => 'In writing', 'sort_order' => 2],
                ['label' => 'c', 'description' => 'Through malicious gestures', 'sort_order' => 3],
            ]],
            ['clause_number' => '04', 'description' => 'Sexual Advances'],
            ['clause_number' => '05', 'description' => 'Bullying'],
            ['clause_number' => '06', 'description' => 'Engaging in quarrel'],
            ['clause_number' => '07', 'description' => 'Bringing reinforcement for brawls'],
            ['clause_number' => '08', 'description' => 'Bringing to school intoxicating drinks such as beer, liquor, wine and/or any alcoholic beverages'],
            ['clause_number' => '09', 'description' => 'Coming to school tipsy'],
            ['clause_number' => '10', 'description' => 'Engaging in drinking alcoholic beverages in school and/or in its immediate vicinity'],
            ['clause_number' => '11', 'description' => 'Bringing to school pornographic and other indecent materials/posting embarrassing photographs in the social media'],
            ['clause_number' => '12', 'description' => 'Bringing to school pornographic and other indecent materials'],
            ['clause_number' => '13', 'description' => 'Possession of cigarettes or electronic cigarettes and smoking in school and/or in its immediate vicinity'],
            ['clause_number' => '14', 'description' => 'Smoking during school-sponsored activities such as retreats and field trips'],
            ['clause_number' => '15', 'description' => 'Engaging in immodest acts such as', 'options' => [
                ['label' => 'a', 'description' => 'Fondling', 'sort_order' => 1],
                ['label' => 'b', 'description' => 'Kissing', 'sort_order' => 2],
                ['label' => 'c', 'description' => 'Necking and Petting', 'sort_order' => 3],
            ]],
            ['clause_number' => '16', 'description' => 'Cutting classes'],
            ['clause_number' => '17', 'description' => 'Truancy'],
            ['clause_number' => '18', 'description' => 'Vandalism of school and/or personal property'],
            ['clause_number' => '19', 'description' => 'Destruction of school property'],
            ['clause_number' => '20', 'description' => 'Forging the signature of parents or guardian in school requirements or documents'],
            ['clause_number' => '21', 'description' => 'Forging the signature of teachers and persons in authority'],
            ['clause_number' => '22', 'description' => 'Copying of school requirements'],
            ['clause_number' => '23', 'description' => 'Cheating', 'options' => [
                ['label' => 'a', 'description' => 'During quizzes and long tests', 'sort_order' => 1],
                ['label' => 'b', 'description' => 'During standardized tests', 'sort_order' => 2],
                ['label' => 'c', 'description' => 'During quarterly examinations', 'sort_order' => 3],
            ]],
            ['clause_number' => '24', 'description' => 'Acting as accomplice to copying or cheating'],
            ['clause_number' => '25', 'description' => 'Tampering with test scores'],
            ['clause_number' => '26', 'description' => 'Possession of any gambling paraphernalia'],
            ['clause_number' => '27', 'description' => 'Any form of gambling in the campus and/or its immediate vicinity'],
            ['clause_number' => '28', 'description' => 'Leaving the school without a valid gate pass issued by the Principal or Assistant principal (note: clinic pass is not a valid gate pass. pupil/student has to secure a valid gate pass from the principal or assistant principal)'],
            ['clause_number' => '29', 'description' => 'Willful insubordination'],
            ['clause_number' => '30', 'description' => 'Deception of school authorities'],
            ['clause_number' => '31', 'description' => 'Withholding information during formal investigation'],
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
