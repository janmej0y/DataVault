<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BusinessNormalizer
{
    public function normalize(?string $value): ?string
    {
        $normalized = Str::of((string) $value)
            ->trim()
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9\s]+/', ' ')
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->value();

        return $normalized !== '' ? $normalized : null;
    }

    public function normalizeMobile(?string $value): ?string
    {
        $numbers = collect(preg_split('/[,;|\/]+/', (string) $value))
            ->map(fn (?string $item) => preg_replace('/\D+/', '', (string) $item))
            ->filter()
            ->unique()
            ->values();

        return $numbers->isNotEmpty() ? $numbers->implode(', ') : null;
    }

    public function buildDuplicateGroupFromPayload(array $payload): ?string
    {
        return $this->buildDuplicateGroup(
            $payload['normalized_business_name'] ?? null,
            $payload['normalized_area'] ?? null,
            $payload['normalized_city'] ?? null,
            $payload['normalized_address'] ?? null,
        );
    }

    public function buildDuplicateGroup(
        ?string $normalizedBusinessName,
        ?string $normalizedArea,
        ?string $normalizedCity,
        ?string $normalizedAddress,
    ): ?string {
        if (blank($normalizedBusinessName) || blank($normalizedArea) || blank($normalizedCity)) {
            return null;
        }

        return sha1(implode('|', [
            $normalizedBusinessName,
            $normalizedArea,
            $normalizedCity,
            $normalizedAddress ?? '',
        ]));
    }

    public function prepareBusinessPayload(array $attributes): array
    {
        $payload = [
            'business_name' => $this->cleanText(Arr::get($attributes, 'business_name')),
            'area' => $this->cleanText(Arr::get($attributes, 'area')),
            'city' => $this->cleanText(Arr::get($attributes, 'city')),
            'mobile_no' => $this->normalizeMobile(Arr::get($attributes, 'mobile_no')),
            'category' => $this->cleanText(Arr::get($attributes, 'category')),
            'sub_category' => $this->cleanText(Arr::get($attributes, 'sub_category')),
            'address' => $this->cleanText(Arr::get($attributes, 'address')),
        ];

        $payload['normalized_business_name'] = $this->normalize($payload['business_name']);
        $payload['normalized_area'] = $this->normalize($payload['area']);
        $payload['normalized_city'] = $this->normalize($payload['city']);
        $payload['normalized_address'] = $this->normalize($payload['address']);
        $payload['duplicate_group'] = $this->buildDuplicateGroupFromPayload($payload);
        $payload['is_duplicate'] = (bool) ($attributes['is_duplicate'] ?? false);
        $payload['merged_into'] = $attributes['merged_into'] ?? null;

        return $payload;
    }

    public function mergeDistinctValues(array $values): ?string
    {
        $merged = collect($values)
            ->flatMap(function ($value) {
                return preg_split('/[,;|]+/', (string) $value);
            })
            ->map(fn (?string $value) => $this->cleanText($value))
            ->filter()
            ->unique(fn (string $value) => Str::lower($value))
            ->values();

        return $merged->isNotEmpty() ? $merged->implode(', ') : null;
    }

    public function firstFilled(array $values): ?string
    {
        return collect($values)
            ->map(fn ($value) => $this->cleanText($value))
            ->first(fn (?string $value) => filled($value));
    }

    public function mergeAddresses(Collection $addresses): ?string
    {
        $uniqueAddresses = $addresses
            ->map(fn ($address) => $this->cleanText($address))
            ->filter()
            ->unique(fn (string $address) => Str::lower($address))
            ->values();

        if ($uniqueAddresses->isEmpty()) {
            return null;
        }

        return $uniqueAddresses->implode(' | ');
    }

    protected function cleanText(?string $value): ?string
    {
        $cleaned = trim((string) $value);

        return $cleaned !== '' ? $cleaned : null;
    }
}
