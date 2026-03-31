<?php

namespace App\Services;

use App\Models\InterventionSuggestion;
use Illuminate\Database\Eloquent\Builder;

class InterventionSuggestionService
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Hydrate intervention suggestions for dashboard consumption.
     */
    public function getDashboardInsights(int $limit = 3, array $filters = []): array
    {
        $pendingQuery = $this->applyFilters(InterventionSuggestion::query(), $filters);

        $pendingSuggestions = (clone $pendingQuery)
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();

        $suggestionsSource = 'manual';

        if ($pendingSuggestions->isEmpty()) {
            $generated = collect($this->analyticsService->generateInterventionInsights($limit))
                ->map(fn ($item) => (object) $item);

            if ($generated->isNotEmpty()) {
                $pendingSuggestions = $generated;
                $suggestionsSource = 'analytics';
            } else {
                $suggestionsSource = 'none';
            }
        }

        $recentPlans = $this->applyFilters(InterventionSuggestion::query(), $filters)
            ->whereIn('status', ['implemented', 'approved'])
            ->with('decisionMaker')
            ->orderByDesc('decided_at')
            ->take($limit)
            ->get();

        return [
            'suggestions' => $pendingSuggestions,
            'suggestionsSource' => $suggestionsSource,
            'recentPlans' => $recentPlans,
        ];
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when(!empty($filters['grade_level']), function (Builder $nested) use ($filters) {
                $grade = $filters['grade_level'];
                $nested->where(function (Builder $inner) use ($grade) {
                    $inner->whereNull('grade_level')
                        ->orWhere('grade_level', $grade);
                });
            })
            ->when(!empty($filters['section']), function (Builder $nested) use ($filters) {
                $section = $filters['section'];
                $nested->where(function (Builder $inner) use ($section) {
                    $inner->whereNull('section')
                        ->orWhere('section', $section);
                });
            });
    }
}
