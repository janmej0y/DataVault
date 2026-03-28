@php
    $cityChartConfig = [
        'type' => 'bar',
        'data' => [
            'labels' => $cityCounts->pluck('label'),
            'datasets' => [[
                'label' => 'Records',
                'data' => $cityCounts->pluck('total'),
                'backgroundColor' => '#0ea5e9',
                'borderRadius' => 12,
            ]],
        ],
        'options' => [
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'x' => ['grid' => ['display' => false]],
                'y' => ['beginAtZero' => true],
            ],
        ],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-sky-600">Overview</p>
                <h1 class="page-title">Operational dashboard for import quality and duplicate cleanup.</h1>
                <p class="page-subtitle">
                    Track record growth, spot duplicate-heavy segments, and keep incomplete listings from slipping into reports.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('imports.index') }}" class="btn-primary">Import Data</a>
                <a href="{{ route('reports.index') }}" class="btn-secondary">View Full Reports</a>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="stat-card">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-500">Total Records</p>
            <p class="mt-4 text-4xl font-black text-slate-950">{{ number_format($summary['total_records']) }}</p>
            <p class="mt-2 text-sm text-slate-500">All active listings currently available for review.</p>
        </div>

        <div class="stat-card">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-500">Unique Listings</p>
            <p class="mt-4 text-4xl font-black text-emerald-600">{{ number_format($summary['unique_listings']) }}</p>
            <p class="mt-2 text-sm text-slate-500">Records that are not flagged as duplicates.</p>
        </div>

        <div class="stat-card">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-500">Duplicate Listings</p>
            <p class="mt-4 text-4xl font-black text-amber-500">{{ number_format($summary['duplicate_listings']) }}</p>
            <p class="mt-2 text-sm text-slate-500">Listings that need merge or deletion review.</p>
        </div>

        <div class="stat-card">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-500">Incomplete Listings</p>
            <p class="mt-4 text-4xl font-black text-rose-500">{{ number_format($summary['incomplete_listings']) }}</p>
            <p class="mt-2 text-sm text-slate-500">Entries missing business name, mobile, or category.</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-5">
        <div class="panel p-6 xl:col-span-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-950">City-wise record distribution</h2>
                    <p class="mt-1 text-sm text-slate-500">Quick view of where your listings are concentrated.</p>
                </div>
            </div>

            <div class="mt-6 h-80">
                <canvas
                    data-chart-config='@json($cityChartConfig)'
                ></canvas>
            </div>
        </div>

        <div class="panel p-6 xl:col-span-2">
            <h2 class="text-lg font-bold text-slate-950">Top categories</h2>
            <p class="mt-1 text-sm text-slate-500">Most active segments in the current database.</p>

            <div class="mt-6 space-y-4">
                @forelse ($topCategories as $category)
                    <div class="panel-muted p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $category->category }}</p>
                                <p class="text-xs uppercase tracking-[0.22em] text-slate-500">Category volume</p>
                            </div>
                            <span class="badge-info">{{ number_format($category->total) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="panel-muted p-4 text-sm text-slate-500">No category data available yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="panel p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-950">Recent import history</h2>
                    <p class="mt-1 text-sm text-slate-500">Latest uploads and their validation results.</p>
                </div>
                <a href="{{ route('imports.index') }}" class="btn-secondary">Open Imports</a>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($recentImports as $log)
                    <div class="panel-muted p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $log->file_name }}</p>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ ucfirst(str_replace('_', ' ', $log->source_type)) }} import on {{ optional($log->started_at)->format('d M Y, h:i A') }}
                                </p>
                            </div>
                            <span class="{{ $log->status === 'completed' ? 'badge-success' : ($log->status === 'failed' ? 'badge-danger' : 'badge-warning') }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-slate-600 sm:grid-cols-4">
                            <div><span class="font-semibold text-slate-900">{{ $log->inserted_rows }}</span> inserted</div>
                            <div><span class="font-semibold text-slate-900">{{ $log->duplicate_rows }}</span> duplicates</div>
                            <div><span class="font-semibold text-slate-900">{{ $log->invalid_rows }}</span> invalid</div>
                            <div><span class="font-semibold text-slate-900">{{ $log->imported_rows }}</span> processed</div>
                        </div>
                    </div>
                @empty
                    <div class="panel-muted p-4 text-sm text-slate-500">No imports have been logged yet.</div>
                @endforelse
            </div>
        </div>

        <div class="panel p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-950">Duplicate groups awaiting review</h2>
                    <p class="mt-1 text-sm text-slate-500">The first record in each group is treated as the current primary listing.</p>
                </div>
                <a href="{{ route('merges.index') }}" class="btn-secondary">Merge Records</a>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($duplicateGroups as $group)
                    @php $master = collect($group['records'])->firstWhere('is_duplicate', false) ?? collect($group['records'])->first(); @endphp
                    <div class="panel-muted p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $master?->business_name ?: 'Unnamed business' }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $master?->city ?: 'Unknown city' }} / {{ $master?->area ?: 'Unknown area' }}</p>
                            </div>
                            <span class="badge-warning">{{ $group['total'] }} records</span>
                        </div>
                    </div>
                @empty
                    <div class="panel-muted p-4 text-sm text-slate-500">No duplicate groups are currently flagged.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
