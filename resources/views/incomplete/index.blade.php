<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-sky-600">Incomplete Listings</p>
                <h1 class="page-title">Review records missing critical business data.</h1>
                <p class="page-subtitle">Incomplete records are flagged when the business name, mobile number, or category is missing.</p>
            </div>
        </div>
    </x-slot>

    <x-business-filters :action="route('incomplete.index')" :filters="$filters" :filter-options="$filterOptions" />

    <div class="panel mt-6 overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-lg font-bold text-slate-950">Incomplete records</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $businesses->total() }} records require additional data.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="table-shell">
                <thead>
                    <tr>
                        <th>Business</th>
                        <th>Location</th>
                        <th>Category</th>
                        <th>Mobile</th>
                        <th>Missing Fields</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($businesses as $business)
                        <tr>
                            <td>
                                <p class="font-semibold text-slate-900">{{ $business->business_name ?: 'Missing business name' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $business->address ?: 'No address available' }}</p>
                            </td>
                            <td>{{ ($business->city ?: 'Unknown city') . ' / ' . ($business->area ?: 'Unknown area') }}</td>
                            <td>{{ $business->category ?: 'Missing category' }}</td>
                            <td>{{ $business->mobile_no ?: 'Missing mobile number' }}</td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    @if (blank($business->business_name))
                                        <span class="badge-danger">Business name</span>
                                    @endif
                                    @if (blank($business->mobile_no))
                                        <span class="badge-danger">Mobile number</span>
                                    @endif
                                    @if (blank($business->category))
                                        <span class="badge-danger">Category</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No incomplete records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $businesses->links() }}
        </div>
    </div>
</x-app-layout>
