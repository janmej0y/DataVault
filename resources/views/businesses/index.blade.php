<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-sky-600">All Records</p>
                <h1 class="page-title">Browse and manage the full business directory.</h1>
                <p class="page-subtitle">Search, filter, export, and bulk delete records from the live dataset.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('businesses.export', request()->query()) }}" class="btn-secondary">Export CSV</a>
                <a href="{{ route('imports.index') }}" class="btn-primary">Import More Data</a>
            </div>
        </div>
    </x-slot>

    <x-business-filters :action="route('businesses.index')" :filters="$filters" :filter-options="$filterOptions" />

    <form method="POST" action="{{ route('businesses.bulk-delete') }}" class="panel mt-6 overflow-hidden">
        @csrf
        @method('DELETE')

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-5">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Business records</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $businesses->total() }} records found for the current filter set.</p>
            </div>

            <button type="submit" class="btn-danger" onclick="return confirm('Delete the selected records? This will also refresh duplicate flags.');">
                Bulk Delete
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="table-shell">
                <thead>
                    <tr>
                        <th class="w-12">
                            <input type="checkbox" onclick="document.querySelectorAll('.row-selector').forEach((checkbox) => checkbox.checked = this.checked)">
                        </th>
                        <th>Business</th>
                        <th>Location</th>
                        <th>Category</th>
                        <th>Mobile</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($businesses as $business)
                        <tr>
                            <td>
                                <input class="row-selector rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500" type="checkbox" name="ids[]" value="{{ $business->id }}">
                            </td>
                            <td>
                                <p class="font-semibold text-slate-900">{{ $business->business_name ?: 'Unnamed business' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $business->address ?: 'No address available' }}</p>
                            </td>
                            <td>
                                <p>{{ $business->city ?: 'Unknown city' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $business->area ?: 'Unknown area' }}</p>
                            </td>
                            <td>
                                <p>{{ $business->category ?: 'Uncategorized' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $business->sub_category ?: 'No sub-category' }}</p>
                            </td>
                            <td>{{ $business->mobile_no ?: 'Missing mobile' }}</td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    @if ($business->duplicate_group && ! $business->is_duplicate)
                                        <span class="badge-info">Primary duplicate record</span>
                                    @endif
                                    @if ($business->is_duplicate)
                                        <span class="badge-warning">Duplicate</span>
                                    @endif
                                    @if ($business->is_incomplete)
                                        <span class="badge-danger">Incomplete</span>
                                    @endif
                                    @if (! $business->is_duplicate && ! $business->is_incomplete)
                                        <span class="badge-success">Healthy</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">No business records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $businesses->links() }}
        </div>
    </form>
</x-app-layout>
