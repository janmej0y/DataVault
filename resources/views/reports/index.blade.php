@php
    $cityChartConfig = [
        'type' => 'bar',
        'data' => [
            'labels' => $cityCounts->pluck('label'),
            'datasets' => [[
                'label' => 'City records',
                'data' => $cityCounts->pluck('total'),
                'backgroundColor' => '#0f172a',
                'borderRadius' => 10,
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

    $categoryCityChartConfig = [
        'type' => 'doughnut',
        'data' => [
            'labels' => $categoryCityCounts->pluck('label'),
            'datasets' => [[
                'data' => $categoryCityCounts->pluck('total'),
                'backgroundColor' => ['#0ea5e9', '#f59e0b', '#10b981', '#6366f1', '#ef4444', '#14b8a6', '#8b5cf6', '#f97316', '#84cc16', '#ec4899', '#06b6d4', '#64748b', '#a855f7', '#22c55e', '#334155'],
            ]],
        ],
        'options' => [
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['position' => 'bottom']],
        ],
    ];

    $categoryAreaChartConfig = [
        'type' => 'bar',
        'data' => [
            'labels' => $categoryAreaCounts->pluck('label'),
            'datasets' => [[
                'label' => 'Area-category records',
                'data' => $categoryAreaCounts->pluck('total'),
                'backgroundColor' => '#f59e0b',
                'borderRadius' => 10,
            ]],
        ],
        'options' => [
            'indexAxis' => 'y',
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'x' => ['beginAtZero' => true],
                'y' => ['grid' => ['display' => false]],
            ],
        ],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-sky-600">Reporting</p>
                <h1 class="page-title">Detailed record, city, category, and area reporting.</h1>
                <p class="page-subtitle">Use these charts and tables to understand data spread and cleanup priorities.</p>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="stat-card">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-500">Total Records</p>
            <p class="mt-4 text-4xl font-black text-slate-950">{{ number_format($summary['total_records']) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-500">Unique Listings</p>
            <p class="mt-4 text-4xl font-black text-emerald-600">{{ number_format($summary['unique_listings']) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-500">Duplicate Listings</p>
            <p class="mt-4 text-4xl font-black text-amber-500">{{ number_format($summary['duplicate_listings']) }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-500">Incomplete Listings</p>
            <p class="mt-4 text-4xl font-black text-rose-500">{{ number_format($summary['incomplete_listings']) }}</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="panel p-6">
            <h2 class="text-lg font-bold text-slate-950">City-wise data count</h2>
            <div class="mt-6 h-80">
                <canvas
                    data-chart-config='@json($cityChartConfig)'
                ></canvas>
            </div>
        </div>

        <div class="panel p-6">
            <h2 class="text-lg font-bold text-slate-950">Category + city-wise data</h2>
            <div class="mt-6 h-80">
                <canvas
                    data-chart-config='@json($categoryCityChartConfig)'
                ></canvas>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="panel p-6">
            <h2 class="text-lg font-bold text-slate-950">Category + area-wise data</h2>
            <div class="mt-6 h-80">
                <canvas
                    data-chart-config='@json($categoryAreaChartConfig)'
                ></canvas>
            </div>
        </div>

        <div class="panel p-6">
            <h2 class="text-lg font-bold text-slate-950">Top categories table</h2>
            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200">
                <table class="table-shell">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($topCategories as $category)
                            <tr>
                                <td>{{ $category->category }}</td>
                                <td>{{ $category->total }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-sm text-slate-500">No category data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
