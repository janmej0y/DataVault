<?php

namespace App\Services;

use App\Models\Business;

class DuplicateDetectionService
{
    public function __construct(
        protected BusinessNormalizer $normalizer
    ) {
    }

    public function refreshFlags(): void
    {
        $businesses = Business::query()
            ->get([
                'id',
                'created_at',
                'normalized_business_name',
                'normalized_area',
                'normalized_city',
                'normalized_address',
            ]);

        $groups = $businesses
            ->filter(function (Business $business) {
                return filled($business->normalized_business_name)
                    && filled($business->normalized_area)
                    && filled($business->normalized_city);
            })
            ->groupBy(function (Business $business) {
                return $this->normalizer->buildDuplicateGroup(
                    $business->normalized_business_name,
                    $business->normalized_area,
                    $business->normalized_city,
                    $business->normalized_address,
                );
            });

        Business::query()->update([
            'is_duplicate' => false,
            'duplicate_group' => null,
        ]);

        foreach ($groups as $groupKey => $records) {
            if (blank($groupKey) || $records->count() < 2) {
                continue;
            }

            $orderedIds = $records
                ->sortBy(fn (Business $business) => sprintf(
                    '%010d-%s',
                    $business->created_at?->getTimestamp() ?? 0,
                    (string) $business->id
                ))
                ->pluck('id')
                ->values();

            $masterId = $orderedIds->first();
            $duplicateIds = $orderedIds->slice(1);

            Business::query()
                ->whereIn('id', $orderedIds)
                ->update([
                    'duplicate_group' => $groupKey,
                    'is_duplicate' => false,
                ]);

            if ($duplicateIds->isNotEmpty()) {
                Business::query()
                    ->whereIn('id', $duplicateIds)
                    ->update(['is_duplicate' => true]);
            }

            Business::query()
                ->whereKey($masterId)
                ->update(['is_duplicate' => false]);
        }
    }
}
