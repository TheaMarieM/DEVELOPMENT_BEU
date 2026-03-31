<?php

namespace Database\Seeders;

use App\Models\InterventionSuggestion;
use App\Services\AnalyticsService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ConvertedInsightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var AnalyticsService $analytics */
        $analytics = app(AnalyticsService::class);
        $insight = collect($analytics->generateInterventionInsights(1))->first();

        if (!$insight) {
            $this->command?->warn('No analytics insights available to convert.');
            return;
        }

        InterventionSuggestion::create([
            'grade_level' => $insight['grade_level'] ?? null,
            'section' => $insight['section'] ?? null,
            'incident_type' => $insight['incident_type'] ?? 'Behavioral Trend',
            'incident_count' => $insight['incident_count'] ?? 0,
            'analysis_period_start' => $insight['analysis_period_start'] ?? Carbon::now()->subDays(45),
            'analysis_period_end' => $insight['analysis_period_end'] ?? Carbon::now(),
            'suggestion' => $insight['suggestion'] ?? 'Monitor behavioral trend and coordinate advisories.',
            'assigned_to' => 'Discipline Chair',
            'assignment_due_at' => Carbon::now()->addDays(7),
            'status' => 'pending',
        ]);

        $this->command?->info('Analytics insight converted into a manual plan.');
    }
}
