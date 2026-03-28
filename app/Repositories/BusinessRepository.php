<?php

namespace App\Repositories;

use App\Models\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator as PaginationLengthAwarePaginator;
use Illuminate\Support\Collection;

class BusinessRepository
{
    public function filteredQuery(array $filters = []): Builder
    {
        return Business::query()
            ->filter($filters)
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function incompletePaginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->incomplete()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function duplicateGroups(array $filters = [], int $perPage = 8): LengthAwarePaginator
    {
        $groups = $this->filteredQuery($filters)
            ->whereNotNull('duplicate_group')
            ->get()
            ->groupBy('duplicate_group')
            ->filter(fn (Collection $records) => $records->count() > 1)
            ->map(function (Collection $records, string $groupKey) {
                $orderedRecords = $records
                    ->sortBy(fn (Business $business) => sprintf(
                        '%s-%s-%s',
                        $business->is_duplicate ? '1' : '0',
                        $business->created_at?->format('YmdHisv') ?? '0000000000000',
                        (string) $business->id
                    ))
                    ->values();

                return [
                    'group_key' => $groupKey,
                    'total' => $orderedRecords->count(),
                    'first_seen_at' => optional($orderedRecords->first()?->created_at)?->timestamp ?? 0,
                    'records' => $orderedRecords,
                ];
            })
            ->sortByDesc('first_seen_at')
            ->values();

        return $this->paginateCollection($groups, $perPage);
    }

    public function duplicateExportQuery(array $filters = []): Builder
    {
        return $this->filteredQuery($filters)
            ->whereNotNull('duplicate_group')
            ->orderBy('duplicate_group')
            ->orderBy('is_duplicate')
            ->orderBy('id');
    }

    public function filterOptions(): array
    {
        return [
            'cities' => $this->pluckDistinct('city'),
            'categories' => $this->pluckDistinct('category'),
            'areas' => $this->pluckDistinct('area'),
        ];
    }

    protected function pluckDistinct(string $column): Collection
    {
        return Business::query()
            ->get([$column])
            ->pluck($column)
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    protected function paginateCollection(Collection $items, int $perPage): LengthAwarePaginator
    {
        $page = PaginationLengthAwarePaginator::resolveCurrentPage();
        $results = $items->forPage($page, $perPage)->values();

        return new PaginationLengthAwarePaginator(
            $results,
            $items->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }
}
