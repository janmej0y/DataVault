@php
    $links = [
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Import Data', 'route' => 'imports.index'],
        ['label' => 'All Records', 'route' => 'businesses.index'],
        ['label' => 'Duplicate Records', 'route' => 'duplicates.index'],
        ['label' => 'Merge Records', 'route' => 'merges.index'],
        ['label' => 'Incomplete Records', 'route' => 'incomplete.index'],
        ['label' => 'Reports', 'route' => 'reports.index'],
    ];
@endphp

<div
    x-cloak
    x-show="sidebarOpen"
    class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden"
    @click="sidebarOpen = false"
></div>

<aside
    class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-white/10 bg-slate-950 text-white transition duration-200 lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen }"
>
    <div class="flex items-center justify-between border-b border-white/10 px-6 py-6">
        <a href="{{ route('dashboard') }}">
            <x-application-logo />
        </a>

        <button type="button" class="text-sm text-slate-300 lg:hidden" @click="sidebarOpen = false">Close</button>
    </div>

    <div class="px-4 py-6">
        <p class="px-3 text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">Workspace</p>
        <nav class="mt-4 space-y-1.5">
            @foreach ($links as $link)
                @php $active = request()->routeIs($link['route']); @endphp
                <a
                    href="{{ route($link['route']) }}"
                    class="{{ $active ? 'bg-white text-slate-950 shadow-lg shadow-sky-500/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center rounded-2xl px-4 py-3 text-sm font-semibold transition"
                >
                    <span class="mr-3 inline-flex h-8 w-8 items-center justify-center rounded-xl {{ $active ? 'bg-slate-950 text-white' : 'bg-white/5 text-sky-300' }}">
                        {{ substr($link['label'], 0, 1) }}
                    </span>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    <div class="mt-auto space-y-4 border-t border-white/10 px-4 py-6">
        <a href="{{ route('profile.edit') }}" class="flex items-center rounded-2xl px-4 py-3 text-sm font-semibold text-slate-300 transition hover:bg-white/10 hover:text-white">
            <span class="mr-3 inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/5 text-sky-300">P</span>
            Profile
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center rounded-2xl px-4 py-3 text-sm font-semibold text-rose-200 transition hover:bg-rose-500/10 hover:text-rose-100">
                <span class="mr-3 inline-flex h-8 w-8 items-center justify-center rounded-xl bg-rose-500/10 text-rose-300">L</span>
                Log Out
            </button>
        </form>
    </div>
</aside>
