<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DataVault') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">
            @include('layouts.navigation')

            <div class="lg:pl-72">
                <div class="sticky top-0 z-20 border-b border-white/60 bg-white/70 backdrop-blur">
                    <div class="flex items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                        <button
                            type="button"
                            @click="sidebarOpen = true"
                            class="btn-secondary lg:hidden"
                        >
                            Menu
                        </button>

                        <div class="hidden text-sm text-slate-500 lg:block">
                            Monitor imports, clean duplicates, and track listing quality from one place.
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="hidden text-right sm:block">
                                <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-sky-100 text-sm font-bold text-sky-700">
                                {{ collect(explode(' ', auth()->user()->name))->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') }}
                            </div>
                        </div>
                    </div>
                </div>

                <main class="px-4 pb-10 pt-6 sm:px-6 lg:px-8">
                    @if (isset($header))
                        <header class="mb-6">
                            {{ $header }}
                        </header>
                    @endif

                    @if (session('status'))
                        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            <p class="font-semibold">Please review the highlighted issue before continuing.</p>
                            <ul class="mt-2 list-disc ps-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
