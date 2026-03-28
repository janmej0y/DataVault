<?php

namespace App\Repositories;

use App\Models\ImportLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ImportLogRepository
{
    public function start(array $attributes): ImportLog
    {
        return ImportLog::query()->create([
            ...$attributes,
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    public function complete(ImportLog $importLog, array $attributes): ImportLog
    {
        $importLog->update([
            ...$attributes,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return $importLog->refresh();
    }

    public function fail(ImportLog $importLog, string $message, array $meta = []): ImportLog
    {
        $importLog->update([
            'status' => 'failed',
            'notes' => $message,
            'meta' => $meta,
            'completed_at' => now(),
        ]);

        return $importLog->refresh();
    }

    public function latest(int $limit = 5): Collection
    {
        return ImportLog::query()
            ->latest('started_at')
            ->limit($limit)
            ->get();
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return ImportLog::query()
            ->latest('started_at')
            ->paginate($perPage);
    }
}
