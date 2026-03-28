<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-sky-600">Duplicate Review</p>
                <h1 class="page-title">Inspect grouped duplicate listings before merge decisions.</h1>
                <p class="page-subtitle">Records are grouped using normalized business name, area, city, and address.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('duplicates.export', request()->query()) }}" class="btn-secondary">Download Duplicates</a>
                <a href="{{ route('merges.index', request()->query()) }}" class="btn-primary">Open Merge Workspace</a>
            </div>
        </div>
    </x-slot>

    <x-business-filters :action="route('duplicates.index')" :filters="$filters" :filter-options="$filterOptions" />

    <div class="mt-6 space-y-6">
        @forelse ($duplicateGroups as $group)
            @php
                $records = collect($group['records']);
                $master = $records->firstWhere('is_duplicate', false) ?? $records->first();
            @endphp

            <div class="panel p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.26em] text-slate-500">Duplicate group</p>
                        <h2 class="mt-2 text-xl font-bold text-slate-950">{{ $master?->business_name ?: 'Unnamed business' }}</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $master?->city ?: 'Unknown city' }} / {{ $master?->area ?: 'Unknown area' }}
                            @if ($master?->address)
                                &bull; {{ $master->address }}
                            @endif
                        </p>
                    </div>

                    <span class="badge-warning">{{ $group['total'] }} matching records</span>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-3">
                    @foreach ($records as $record)
                        <div class="panel-muted p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $record->business_name ?: 'Unnamed business' }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $record->city ?: 'Unknown city' }} / {{ $record->area ?: 'Unknown area' }}</p>
                                </div>

                                <span class="{{ $record->is_duplicate ? 'badge-warning' : 'badge-info' }}">
                                    {{ $record->is_duplicate ? 'Duplicate' : 'Primary' }}
                                </span>
                            </div>

                            <dl class="mt-4 space-y-2 text-sm text-slate-600">
                                <div>
                                    <dt class="font-semibold text-slate-900">Mobile</dt>
                                    <dd>{{ $record->mobile_no ?: 'Missing mobile number' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-slate-900">Category</dt>
                                    <dd>{{ $record->category ?: 'Missing category' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-slate-900">Address</dt>
                                    <dd>{{ $record->address ?: 'No address available' }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="panel p-10 text-center text-sm text-slate-500">
                No duplicate groups match the current filters.
            </div>
        @endforelse
    </div>

    @if ($duplicateGroups->hasPages())
        <div class="panel mt-6 px-6 py-4">
            {{ $duplicateGroups->links() }}
        </div>
    @endif
</x-app-layout>
