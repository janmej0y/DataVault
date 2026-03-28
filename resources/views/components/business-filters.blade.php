@props([
    'action',
    'filters' => [],
    'filterOptions' => [],
])

<form method="GET" action="{{ $action }}" class="panel p-5">
    <div class="grid gap-4 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <label class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Business Name</label>
            <input
                type="text"
                name="search"
                value="{{ $filters['search'] ?? '' }}"
                class="filter-input mt-2"
                placeholder="Search name, mobile, or address"
            >
        </div>

        <div>
            <label class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">City</label>
            <select name="city" class="filter-input mt-2">
                <option value="">All cities</option>
                @foreach (($filterOptions['cities'] ?? collect()) as $city)
                    <option value="{{ $city }}" @selected(($filters['city'] ?? '') === $city)>{{ $city }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Category</label>
            <select name="category" class="filter-input mt-2">
                <option value="">All categories</option>
                @foreach (($filterOptions['categories'] ?? collect()) as $category)
                    <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Area</label>
            <select name="area" class="filter-input mt-2">
                <option value="">All areas</option>
                @foreach (($filterOptions['areas'] ?? collect()) as $area)
                    <option value="{{ $area }}" @selected(($filters['area'] ?? '') === $area)>{{ $area }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-3">
        <button type="submit" class="btn-primary">Apply Filters</button>
        <a href="{{ $action }}" class="btn-secondary">Reset</a>
    </div>
</form>
