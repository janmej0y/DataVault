<?php

namespace App\Services;

use App\Imports\BusinessesImport;
use App\Models\Business;
use App\Models\ImportLog;
use App\Repositories\ImportLogRepository;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class BusinessImportService
{
    public function __construct(
        protected ImportLogRepository $importLogRepository,
        protected BusinessNormalizer $normalizer,
        protected DuplicateDetectionService $duplicateDetectionService
    ) {
    }

    public function import(?UploadedFile $upload = null, ?string $googleDriveUrl = null): ImportLog
    {
        [$filePath, $fileName, $sourceType, $sourceReference, $cleanupPath] = $googleDriveUrl
            ? $this->downloadGoogleDriveFile($googleDriveUrl)
            : $this->stageUploadedFile($upload);

        $importLog = $this->importLogRepository->start([
            'file_name' => $fileName,
            'source_type' => $sourceType,
            'source_reference' => $sourceReference,
        ]);

        try {
            $import = new BusinessesImport();
            Excel::import($import, $filePath);

            $rowNumber = 1;
            $importedRows = 0;
            $invalidRows = 0;
            $insertedIds = [];
            $errors = [];

            foreach ($import->rows as $row) {
                $rowNumber++;
                $mapped = $this->mapRow($row->toArray());

                if (collect($mapped)->filter()->isEmpty()) {
                    continue;
                }

                $importedRows++;

                $validator = Validator::make($mapped, [
                    'business_name' => ['nullable', 'string', 'max:255'],
                    'area' => ['nullable', 'string', 'max:255'],
                    'city' => ['nullable', 'string', 'max:255'],
                    'mobile_no' => ['nullable', 'string', 'max:255'],
                    'category' => ['nullable', 'string', 'max:255'],
                    'sub_category' => ['nullable', 'string', 'max:255'],
                    'address' => ['nullable', 'string', 'max:65535'],
                ]);

                if ($validator->fails()) {
                    $invalidRows++;

                    if (count($errors) < 10) {
                        $errors["row_{$rowNumber}"] = $validator->errors()->all();
                    }

                    continue;
                }

                $business = Business::query()->create($this->normalizer->prepareBusinessPayload($mapped));
                $insertedIds[] = $business->id;
            }

            $this->duplicateDetectionService->refreshFlags();

            $duplicateRows = empty($insertedIds)
                ? 0
                : Business::query()
                    ->whereIn('id', $insertedIds)
                    ->where('is_duplicate', true)
                    ->count();

            $this->importLogRepository->complete($importLog, [
                'imported_rows' => $importedRows,
                'inserted_rows' => count($insertedIds),
                'duplicate_rows' => $duplicateRows,
                'invalid_rows' => $invalidRows,
                'notes' => $invalidRows > 0
                    ? 'Import completed with some skipped rows due to validation issues.'
                    : 'Import completed successfully.',
                'meta' => [
                    'sample_errors' => $errors,
                ],
            ]);
        } catch (Throwable $throwable) {
            $this->importLogRepository->fail($importLog, $throwable->getMessage());
            throw $throwable;
        } finally {
            if ($cleanupPath && File::exists($cleanupPath)) {
                File::delete($cleanupPath);
            }
        }

        return $importLog->fresh();
    }

    protected function stageUploadedFile(?UploadedFile $upload): array
    {
        if (! $upload) {
            throw new \InvalidArgumentException('No upload file was provided.');
        }

        $extension = $upload->getClientOriginalExtension() ?: 'csv';
        $targetPath = storage_path('app/imports_' . Str::uuid() . '.' . $extension);
        File::ensureDirectoryExists(dirname($targetPath));
        $upload->move(dirname($targetPath), basename($targetPath));

        return [
            $targetPath,
            $upload->getClientOriginalName(),
            'upload',
            $upload->getClientOriginalName(),
            $targetPath,
        ];
    }

    protected function downloadGoogleDriveFile(string $googleDriveUrl): array
    {
        $fileId = $this->extractGoogleDriveFileId($googleDriveUrl);

        if (! $fileId) {
            throw new \InvalidArgumentException('A valid public Google Drive file URL is required.');
        }

        $downloadUrl = "https://drive.google.com/uc?export=download&id={$fileId}";
        $tempPath = storage_path('app/imports_' . Str::uuid());
        File::ensureDirectoryExists(dirname($tempPath));

        $response = (new Client([
            'timeout' => 120,
            'verify' => true,
        ]))->request('GET', $downloadUrl, [
            'sink' => $tempPath,
            'headers' => [
                'User-Agent' => 'DataVault Importer',
            ],
        ]);

        $resolvedFileName = $this->resolveDownloadedFileName(
            $response->getHeaderLine('Content-Disposition'),
            $response->getHeaderLine('Content-Type'),
            $fileId
        );
        $extension = pathinfo($resolvedFileName, PATHINFO_EXTENSION) ?: 'csv';
        $targetPath = $tempPath . '.' . $extension;
        File::move($tempPath, $targetPath);

        return [
            $targetPath,
            $resolvedFileName,
            'google_drive',
            $googleDriveUrl,
            $targetPath,
        ];
    }

    protected function extractGoogleDriveFileId(string $url): ?string
    {
        if (preg_match('#/d/([a-zA-Z0-9_-]+)#', $url, $matches)) {
            return $matches[1];
        }

        $parts = parse_url($url);

        if (! empty($parts['query'])) {
            parse_str($parts['query'], $query);

            return Arr::get($query, 'id');
        }

        return null;
    }

    protected function resolveDownloadedFileName(string $contentDisposition, string $contentType, string $fileId): string
    {
        if (preg_match('/filename\*?=(?:UTF-8\'\')?"?([^\";]+)/i', $contentDisposition, $matches)) {
            return trim(rawurldecode($matches[1]), '"');
        }

        $extension = match (true) {
            str_contains($contentType, 'spreadsheetml') => 'xlsx',
            str_contains($contentType, 'excel') => 'xls',
            default => 'csv',
        };

        return "google-drive-{$fileId}.{$extension}";
    }

    protected function mapRow(array $row): array
    {
        $normalizedKeys = collect($row)->mapWithKeys(function ($value, $key) {
            $normalizedKey = Str::of((string) $key)
                ->lower()
                ->replace(['-', ' '], '_')
                ->replaceMatches('/[^a-z0-9_]/', '')
                ->value();

            return [$normalizedKey => is_string($value) ? trim($value) : $value];
        });

        return [
            'business_name' => $normalizedKeys->get('business_name') ?? $normalizedKeys->get('name'),
            'area' => $normalizedKeys->get('area'),
            'city' => $normalizedKeys->get('city'),
            'mobile_no' => $normalizedKeys->get('mobile_no') ?? $normalizedKeys->get('mobile'),
            'category' => $normalizedKeys->get('category'),
            'sub_category' => $normalizedKeys->get('sub_category') ?? $normalizedKeys->get('subcategory'),
            'address' => $normalizedKeys->get('address'),
        ];
    }
}
