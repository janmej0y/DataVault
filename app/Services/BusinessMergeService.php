<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Validation\ValidationException;

class BusinessMergeService
{
    public function __construct(
        protected BusinessNormalizer $normalizer,
        protected DuplicateDetectionService $duplicateDetectionService
    ) {
    }

    /**
     * @param  array<int, string>  $businessIds
     */
    public function merge(array $businessIds, string $masterId): Business
    {
        $records = Business::query()
            ->whereIn('id', $businessIds)
            ->get();

        if ($records->count() < 2) {
            throw ValidationException::withMessages([
                'business_ids' => 'Select at least two records to merge.',
            ]);
        }

        /** @var Business|null $master */
        $master = $records->firstWhere('id', $masterId);

        if (! $master) {
            throw ValidationException::withMessages([
                'master_id' => 'The selected master record must be part of the merge set.',
            ]);
        }

        $master->fill($this->normalizer->prepareBusinessPayload([
            'business_name' => $this->normalizer->firstFilled($records->pluck('business_name')->all()),
            'area' => $this->normalizer->firstFilled($records->pluck('area')->all()),
            'city' => $this->normalizer->firstFilled($records->pluck('city')->all()),
            'mobile_no' => $this->normalizer->mergeDistinctValues($records->pluck('mobile_no')->all()),
            'category' => $this->normalizer->mergeDistinctValues($records->pluck('category')->all()),
            'sub_category' => $this->normalizer->mergeDistinctValues($records->pluck('sub_category')->all()),
            'address' => $this->normalizer->mergeAddresses($records->pluck('address')),
            'is_duplicate' => false,
            'merged_into' => null,
        ]));
        $master->save();

        $records
            ->reject(fn (Business $business) => $business->id === $master->id)
            ->each(function (Business $business) use ($master) {
                $business->merged_into = $master->id;
                $business->save();
                $business->delete();
            });

        $this->duplicateDetectionService->refreshFlags();

        return $master->fresh();
    }
}
