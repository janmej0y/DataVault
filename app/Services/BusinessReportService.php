<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Support\Collection;

class BusinessReportService
{
    public function summary(): array
    {
        return [
            'total_records' => Business::query()->count(),
            'unique_listings' => Business::query()->where('is_duplicate', false)->count(),
            'duplicate_listings' => Business::query()->where('is_duplicate', true)->count(),
            'incomplete_listings' => Business::query()->incomplete()->count(),
        ];
    }

    public function cityCounts(int $limit = 10): Collection
    {
        return $this->countByLabels(
            Business::query()->get(['city'])->map(fn (Business $business) => $business->city ?: 'Unknown City'),
            $limit
        );
    }

    public function categoryCityCounts(int $limit = 10): Collection
    {
        return $this->countByLabels(
            Business::query()->get(['category', 'city'])->map(function (Business $business) {
                return ($business->category ?: 'Unknown Category') . ' / ' . ($business->city ?: 'Unknown City');
            }),
            $limit
        );
    }

    public function categoryAreaCounts(int $limit = 10): Collection
    {
        return $this->countByLabels(
            Business::query()->get(['category', 'area'])->map(function (Business $business) {
                return ($business->category ?: 'Unknown Category') . ' / ' . ($business->area ?: 'Unknown Area');
            }),
            $limit
        );
    }

    public function topCategories(int $limit = 8): Collection
    {
        return $this->countByLabels(
            Business::query()->get(['category'])->map(fn (Business $business) => $business->category ?: 'Unknown Category'),
            $limit,
            'category'
        );
    }

    protected function countByLabels(Collection $labels, int $limit, string $field = 'label'): Collection
    {
        return $labels
            ->countBy()
            ->map(fn (int $total, string $label) => (object) [$field => $label, 'label' => $label, 'total' => $total])
            ->sortByDesc('total')
            ->take($limit)
            ->values();
    }
}
