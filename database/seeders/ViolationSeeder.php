<?php

namespace Database\Seeders;

use App\Models\ViolationCategory;
use App\Models\ViolationClause;
use App\Models\Sanction;
use Illuminate\Database\Seeder;

class ViolationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sanction::truncate();
        ViolationClause::truncate();
        ViolationCategory::truncate();

        $categories = [
            $this->kinderMajorConfig(),
            $this->kinderMinorConfig(),
            $this->categoryOneConfig(),
            $this->categoryTwoConfig(),
            $this->categoryThreeConfig(),
            $this->categoryFourConfig(),
        ];

        foreach ($categories as $categoryConfig) {
            $this->seedCategory($categoryConfig);
        }
    }

    protected function kinderMajorConfig(): array
    {
        $legend = [
            1 => 'Oral warning',
            2 => 'Referral to the Student Welfare Chair',
            3 => 'Written warning',
            4 => 'Conference with parents',
            5 => 'Conference with parents and one-step lower than the current deportment grade',
        ];

        return [
            'attributes' => [
                'name' => 'Kinder to Grade 3 - Major Offenses',
                'description' => 'Major offenses for Kinder to Grade 3 based on the official sanctions matrix.',
                'severity' => 'major',
                'requires_parent_notification' => true,
                'sort_order' => 1,
            ],
            'legend' => $legend,
            'violations' => [
                [
                    'code' => 'KM-1',
                    'description' => 'Use of vulgar and indecent language',
                    'sanctions' => [
                        1 => 3,
                        2 => 4,
                        3 => 5,
                    ],
                ],
                [
                    'code' => 'KM-2',
                    'description' => 'Threatening persons in authority or fellow pupils',
                    'sanctions' => [
                        1 => 3,
                        2 => 4,
                        3 => 5,
                    ],
                ],
                [
                    'code' => 'KM-3A',
                    'description' => 'Engaging in fights - oral fight',
                    'sanctions' => [
                        1 => 2,
                        2 => 3,
                        3 => 4,
                        4 => 5,
                    ],
                ],
                [
                    'code' => 'KM-3B',
                    'description' => 'Engaging in fights - provocation of fight',
                    'sanctions' => [
                        1 => 2,
                        2 => 3,
                        3 => 4,
                        4 => 5,
                    ],
                ],
                [
                    'code' => 'KM-3C',
                    'description' => 'Engaging in fights - quarrel with slight physical injury',
                    'sanctions' => [
                        1 => 4,
                        2 => 5,
                    ],
                ],
                [
                    'code' => 'KM-3D',
                    'description' => 'Engaging in fights - quarrel with serious physical injury',
                    'sanctions' => [
                        1 => 4,
                        2 => 5,
                    ],
                ],
                [
                    'code' => 'KM-4',
                    'description' => "Planning or attempting to get others' things without permission",
                    'sanctions' => [
                        1 => 2,
                        2 => 3,
                        3 => 4,
                        4 => 5,
                    ],
                ],
                [
                    'code' => 'KM-5',
                    'description' => 'Stealing',
                    'sanctions' => [
                        1 => 4,
                        2 => 5,
                    ],
                ],
                [
                    'code' => 'KM-6A',
                    'description' => 'Cheating during quarterly exams (70 percent grade)',
                    'sanctions' => [
                        1 => 3,
                        2 => 4,
                        3 => 5,
                    ],
                ],
                [
                    'code' => 'KM-6B',
                    'description' => 'Cheating during quizzes',
                    'sanctions' => [
                        1 => 3,
                        2 => 4,
                        3 => 5,
                    ],
                ],
                [
                    'code' => 'KM-6C',
                    'description' => 'Cheating during re-checking',
                    'sanctions' => [
                        1 => 3,
                        2 => 4,
                        3 => 5,
                    ],
                ],
                [
                    'code' => 'KM-7',
                    'description' => 'Forging of signatures (parents and teachers)',
                    'sanctions' => [
                        1 => 4,
                        2 => 5,
                    ],
                ],
                [
                    'code' => 'KM-8',
                    'description' => 'Possession of harmful weapon',
                    'sanctions' => [
                        1 => 3,
                        2 => 4,
                        3 => 5,
                    ],
                ],
                [
                    'code' => 'KM-9A',
                    'description' => 'Vandalism - minor (chalk writings on walls and similar acts)',
                    'sanctions' => [
                        1 => 3,
                        2 => 4,
                        3 => 5,
                    ],
                ],
                [
                    'code' => 'KM-9B',
                    'description' => 'Vandalism - major (damaging school properties; replacement is part of the penalty)',
                    'sanctions' => [
                        1 => 4,
                        2 => 5,
                    ],
                ],
                [
                    'code' => 'KM-10',
                    'description' => "Damaging or destroying others' personal property",
                    'sanctions' => [
                        1 => 3,
                        2 => 4,
                        3 => 5,
                    ],
                ],
                [
                    'code' => 'KM-11',
                    'description' => 'Violation of the CLAYGO policy',
                    'sanctions' => [
                        1 => 1,
                        2 => 2,
                        3 => 3,
                        4 => 4,
                        5 => 5,
                    ],
                ],
                [
                    'code' => 'KM-12',
                    'description' => 'Climbing or passing over fences, steel bars, and trees',
                    'sanctions' => [
                        1 => 3,
                        2 => 4,
                        3 => 5,
                    ],
                ],
                [
                    'code' => 'KM-13',
                    'description' => 'Humiliating others through words or actions',
                    'sanctions' => [
                        1 => 1,
                        2 => 2,
                        3 => 3,
                        4 => 4,
                        5 => 5,
                    ],
                ],
                [
                    'code' => 'KM-14',
                    'description' => 'Other offenses similar to the listed major offenses',
                    'sanctions' => [
                        1 => 1,
                        2 => 2,
                        3 => 3,
                        4 => 4,
                        5 => 5,
                    ],
                ],
            ],
        ];
    }

    protected function kinderMinorConfig(): array
    {
        $legend = [
            1 => 'Oral warning',
            2 => 'Referral to the Student Welfare Chair',
            3 => 'Written warning',
            4 => 'Conference with parents',
        ];

        $standardSanctions = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        ];

        return [
            'attributes' => [
                'name' => 'Kinder to Grade 3 - Minor Offenses',
                'description' => 'Minor offenses for Kinder to Grade 3 from the sanctions matrix.',
                'severity' => 'minor',
                'requires_parent_notification' => false,
                'sort_order' => 2,
            ],
            'legend' => $legend,
            'violations' => [
                [
                    'code' => 'KMI-1',
                    'description' => 'Tardiness (on quarterly basis)',
                    'sanctions' => $standardSanctions,
                ],
                [
                    'code' => 'KMI-2',
                    'description' => 'Not following classroom rules and instructions',
                    'sanctions' => $standardSanctions,
                ],
                [
                    'code' => 'KMI-3',
                    'description' => 'Misbehaving during classes, assemblies, programs, change of periods, and other related school activities',
                    'sanctions' => $standardSanctions,
                ],
                [
                    'code' => 'KMI-4',
                    'description' => 'Misbehaving during flag raising and flag retreat ceremonies',
                    'sanctions' => $standardSanctions,
                ],
                [
                    'code' => 'KMI-5',
                    'description' => 'Littering',
                    'sanctions' => $standardSanctions,
                ],
                [
                    'code' => 'KMI-6',
                    'description' => 'Misbehaving during other campus ceremonies and religious activities',
                    'sanctions' => $standardSanctions,
                ],
                [
                    'code' => 'KMI-7',
                    'description' => 'Non-procurement of admission slip',
                    'sanctions' => $standardSanctions,
                ],
                [
                    'code' => 'KMI-8',
                    'description' => 'Any offense similar to the listed minor offenses',
                    'sanctions' => $standardSanctions,
                ],
            ],
        ];
    }

    protected function categoryOneConfig(): array
    {
        $legend = [
            1 => 'Oral warning (handled by adviser)',
            2 => 'Call the parents and require a signed written warning submitted to the Student Welfare Chair',
            3 => 'Call the parents, issue a disciplinary violation report, and lower the deportment grade by one step in the quarter',
            4 => 'Call the parents and lower the deportment grade by two steps in the quarter',
            5 => 'Call the parents again and keep the deportment grade two steps lower for the quarter',
        ];

        $sanctions = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        ];

        return [
            'attributes' => [
                'name' => 'Grades 4 to 12 - Category I Offenses',
                'description' => 'Category I offenses for Grades 4 to 12 as stated in the sanctions matrix.',
                'severity' => 'minor',
                'requires_parent_notification' => true,
                'sort_order' => 3,
            ],
            'legend' => $legend,
            'violations' => [
                [
                    'code' => 'C1-1.1',
                    'description' => 'Failure to get an admission slip',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C1-1.2',
                    'description' => 'Failure to bring a letter of excuse duly signed by parent or guardian',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C1-1.3',
                    'description' => 'Climbing or passing over sidewalk railings, school walls, fences, and trees',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C1-1.4',
                    'description' => 'Spitting or littering anywhere on campus',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C1-1.5',
                    'description' => 'Misbehavior during prayer time or flag ceremony',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C1-1.6',
                    'description' => 'Playing video games during class hours (confiscation is part of the penalty)',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C1-1.7',
                    'description' => 'Bringing pets, laser pointers, or other harmful electronic equipment',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C1-1.8',
                    'description' => 'Any offense analogous to the other Category I items',
                    'sanctions' => $sanctions,
                ],
            ],
        ];
    }

    protected function categoryTwoConfig(): array
    {
        $legend = [
            1 => 'Oral warning plus a written warning signed by the parents and returned to the Student Welfare Chair',
            2 => 'Call the parents, issue a disciplinary violation report, and lower the deportment grade by two steps',
            3 => 'Call the parents, keep the deportment grade two steps lower, and impose a one-day suspension',
            4 => 'Call the parents, lower the deportment grade by three points, and impose a two-day suspension',
            5 => 'Call the parents, give a failing deportment mark, and impose a three-day suspension with an MOA',
        ];

        $sanctions = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        ];

        return [
            'attributes' => [
                'name' => 'Grades 4 to 12 - Category II Offenses',
                'description' => 'Category II offenses for Grades 4 to 12 from the sanctions matrix.',
                'severity' => 'moderate',
                'requires_parent_notification' => true,
                'sort_order' => 4,
            ],
            'legend' => $legend,
            'violations' => [
                [
                    'code' => 'C2-2.1',
                    'description' => 'Unauthorized use of school forms or waivers',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C2-2.2',
                    'description' => 'Disobedience',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C2-2.3',
                    'description' => 'Disrespecting the Philippine flag and other national symbols',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C2-2.4',
                    'description' => 'Violation of the CLAYGO policy',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C2-2.5',
                    'description' => 'Using profane and indecent language',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C2-2.6',
                    'description' => 'Public display of affection',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C2-2.7',
                    'description' => 'Accumulated tardiness (3, 5, 8, 12, and 15 instances trigger successive offenses)',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C2-2.8',
                    'description' => 'Accumulated absenteeism (2, 3, 5, 7, and 9 unexcused absences trigger successive offenses)',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C2-2.9',
                    'description' => 'Any offense analogous to the other Category II items',
                    'sanctions' => $sanctions,
                ],
            ],
        ];
    }

    protected function categoryThreeConfig(): array
    {
        $legend = [
            1 => 'Call the parents, issue a disciplinary violation report, drop the deportment grade by two steps, and impose a one-day suspension',
            2 => 'Call the parents, drop the deportment grade by four steps, impose a two-day suspension, and place the student on disciplinary probation',
            3 => 'Call the parents, give a failing deportment mark, and impose a three-day suspension with an MOA',
            4 => 'Call the parents, keep the failing deportment mark, and recommend transfer to another school (RFT)',
        ];

        $sanctions = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        ];

        return [
            'attributes' => [
                'name' => 'Grades 4 to 12 - Category III Offenses',
                'description' => 'Category III offenses for Grades 4 to 12 from the sanctions matrix.',
                'severity' => 'major',
                'requires_parent_notification' => true,
                'sort_order' => 5,
            ],
            'legend' => $legend,
            'violations' => [
                [
                    'code' => 'C3-3.1A',
                    'description' => 'Gross or scandalous misbehavior inside the campus',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.1B',
                    'description' => 'Gross or scandalous misbehavior during off-campus activities',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.1C',
                    'description' => 'Gross or scandalous misbehavior outside the campus while wearing the school uniform',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.2A',
                    'description' => 'Showing disrespect to teachers or persons in authority orally',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.2B',
                    'description' => 'Showing disrespect to teachers or persons in authority in writing',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.2C',
                    'description' => 'Showing disrespect to teachers or persons in authority through malicious gestures',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.3A',
                    'description' => 'Assaulting a fellow student orally',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.3B',
                    'description' => 'Assaulting a fellow student in writing',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.3C',
                    'description' => 'Assaulting a fellow student through malicious gestures',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.4',
                    'description' => 'Sexual advances',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.5',
                    'description' => 'Bullying',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.6',
                    'description' => 'Engaging in quarrel',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.7',
                    'description' => 'Bringing reinforcement for brawls',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.8',
                    'description' => 'Bringing intoxicating drinks such as beer, liquor, wine, or other alcoholic beverages to school',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.9',
                    'description' => 'Coming to school tipsy',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.10',
                    'description' => 'Engaging in drinking alcoholic beverages in school or its immediate vicinity',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.11',
                    'description' => 'Bringing pornographic or indecent materials to school',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.12',
                    'description' => 'Posting embarrassing photographs in social media',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.13',
                    'description' => 'Possession of cigarettes or electronic cigarettes and smoking in school or its immediate vicinity',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.14',
                    'description' => 'Smoking during school-sponsored activities such as retreats and field trips',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.15A',
                    'description' => 'Engaging in immodest acts - fondling',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.15B',
                    'description' => 'Engaging in immodest acts - kissing',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.15C',
                    'description' => 'Engaging in immodest acts - necking and petting',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.16',
                    'description' => 'Cutting classes',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.17',
                    'description' => 'Truancy',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.18',
                    'description' => 'Vandalism of school and/or personal property',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.19',
                    'description' => 'Destruction of school property',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.20',
                    'description' => 'Forging the signature of parents or guardians in school requirements or documents',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.21',
                    'description' => 'Forging the signature of teachers and persons in authority',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.22',
                    'description' => 'Copying of school requirements',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.23',
                    'description' => 'Cheating',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.24',
                    'description' => 'Acting as accomplice to copying or cheating',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.25',
                    'description' => 'Tampering with test scores',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.26',
                    'description' => 'Possession of any gambling paraphernalia',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.27',
                    'description' => 'Any form of gambling in the campus or its immediate vicinity',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.28',
                    'description' => 'Leaving the school without a valid gate pass issued by the Principal or Assistant Principal',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.29',
                    'description' => 'Willful insubordination',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.30',
                    'description' => 'Deception of school authorities',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.31',
                    'description' => 'Withholding information during formal investigation',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C3-3.32',
                    'description' => 'Any offense analogous to the other Category III items',
                    'sanctions' => $sanctions,
                ],
            ],
        ];
    }

    protected function categoryFourConfig(): array
    {
        $legend = [
            1 => 'Call the parents, drop the deportment grade by five steps, and impose a three-day suspension with an MOA',
            2 => 'Call the parents, give a failing deportment grade, and recommend transfer to another school (RFT)',
        ];

        $sanctions = [
            1 => 1,
            2 => 2,
        ];

        return [
            'attributes' => [
                'name' => 'Grades 4 to 12 - Category IV Offenses',
                'description' => 'Category IV offenses for Grades 4 to 12 from the sanctions matrix.',
                'severity' => 'severe',
                'requires_parent_notification' => true,
                'sort_order' => 6,
            ],
            'legend' => $legend,
            'violations' => [
                [
                    'code' => 'C4-4.1A',
                    'description' => 'Assaulting fellow students and/or teachers and staff with physical contact or injury',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.1B',
                    'description' => 'Assaulting fellow students and/or teachers and staff during off-campus activities',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.2',
                    'description' => 'Slanderous actions or remarks toward fellow students or school personnel via any medium',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.3',
                    'description' => 'Rebellious actions or remarks against the school',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.4',
                    'description' => 'Threatening students, teachers, staff, or persons in authority using deadly weapons',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.5',
                    'description' => 'Possession, sale, or use of deadly weapons, ammunitions, or explosives',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.6A',
                    'description' => 'Dangerous drugs - possession',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.6B',
                    'description' => 'Dangerous drugs - coming to school under the influence',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.6C',
                    'description' => 'Dangerous drugs - peddling or pushing',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.7',
                    'description' => 'Enlisting, recruiting, or engaging in activities with pseudo fraternities, sororities, gangs, or similar groups',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.8',
                    'description' => 'Hazing',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.9',
                    'description' => 'Extortion',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.10',
                    'description' => 'Engaging in pre-marital sex',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.11',
                    'description' => 'Elopement',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.12',
                    'description' => 'Desecration of the chapel or any place considered sacred on campus',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.13',
                    'description' => 'Theft',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.14',
                    'description' => 'Illicit relationship with a school personnel',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.15',
                    'description' => 'Unauthorized pageant participation',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.16',
                    'description' => 'Any form of conspiracy',
                    'sanctions' => $sanctions,
                ],
                [
                    'code' => 'C4-4.17',
                    'description' => 'Any offense analogous to the other Category IV items',
                    'sanctions' => $sanctions,
                ],
            ],
        ];
    }

    protected function seedCategory(array $config): void
    {
        $category = ViolationCategory::create($config['attributes']);

        foreach ($config['violations'] as $violation) {
            $clause = ViolationClause::create([
                'violation_category_id' => $category->id,
                'clause_number' => $violation['code'],
                'description' => $violation['description'],
            ]);

            foreach ($violation['sanctions'] as $offense => $sanctionCode) {
                Sanction::create([
                    'violation_clause_id' => $clause->id,
                    'offense_count' => $offense,
                    'sanction_description' => $this->resolveSanctionDescription($sanctionCode, $config['legend']),
                ]);
            }
        }
    }

    protected function resolveSanctionDescription(int|string $code, array $legend): string
    {
        if (is_string($code) && !is_numeric($code)) {
            return $code;
        }

        $key = (int) $code;

        return $legend[$key] ?? 'Refer to the Student Handbook for the specific sanction.';
    }
}
