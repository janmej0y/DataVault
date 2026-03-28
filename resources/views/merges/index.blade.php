<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-sky-600">Merge Workspace</p>
                <h1 class="page-title">Choose a master record and merge duplicates safely.</h1>
                <p class="page-subtitle">All checked non-master records will be soft deleted after their values are merged into the selected master.</p>
            </div>
        </div>
    </x-slot>

    <x-business-filters :action="route('merges.index')" :filters="$filters" :filter-options="$filterOptions" />

    <div class="mt-6 space-y-6">
        @forelse ($duplicateGroups as $group)
            @php $records = collect($group['records']); @endphp

            <form method="POST" action="{{ route('merges.store') }}" class="panel p-6">
                @csrf

                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.26em] text-slate-500">Merge group</p>
                        <h2 class="mt-2 text-xl font-bold text-slate-950">{{ $records->first()?->business_name ?: 'Unnamed business' }}</h2>
                        <p class="mt-1 text-sm text-slate-500">Select a master and keep only the records that should participate in this merge.</p>
                    </div>

                    <button type="submit" class="btn-primary">Merge Selected Records</button>
                </div>

                <div class="mt-6 space-y-4">
                    @foreach ($records as $record)
                        <div class="panel-muted p-4">
                            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                <div class="flex items-start gap-4">
                                    <input
                                        type="checkbox"
                                        name="business_ids[]"
                                        value="{{ $record->id }}"
                                        class="mt-1 rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500"
                                        checked
                                    >

                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-semibold text-slate-900">{{ $record->business_name ?: 'Unnamed business' }}</p>
                                            <span class="{{ $record->is_duplicate ? 'badge-warning' : 'badge-info' }}">
                                                {{ $record->is_duplicate ? 'Duplicate' : 'Current primary' }}
                                            </span>
                                        </div>

                                        <p class="mt-1 text-sm text-slate-500">{{ $record->city ?: 'Unknown city' }} / {{ $record->area ?: 'Unknown area' }}</p>

                                        <div class="mt-3 grid gap-2 text-sm text-slate-600 md:grid-cols-3">
                                            <div><span class="font-semibold text-slate-900">Mobile:</span> {{ $record->mobile_no ?: 'Missing' }}</div>
                                            <div><span class="font-semibold text-slate-900">Category:</span> {{ $record->category ?: 'Missing' }}</div>
                                            <div><span class="font-semibold text-slate-900">Address:</span> {{ $record->address ?: 'Missing' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <label class="inline-flex items-center gap-3 rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-700">
                                    <input
                                        type="radio"
                                        name="master_id"
                                        value="{{ $record->id }}"
                                        class="border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500"
                                        @checked(! $record->is_duplicate || $loop->first)
                                    >
                                    Set as master
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>
        @empty
            <div class="panel p-10 text-center text-sm text-slate-500">
                There are no duplicate groups available to merge right now.
            </div>
        @endforelse
    </div>

    @if ($duplicateGroups->hasPages())
        <div class="panel mt-6 px-6 py-4">
            {{ $duplicateGroups->links() }}
        </div>
    @endif
</x-app-layout>
