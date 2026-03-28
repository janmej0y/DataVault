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
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen px-4 py-10 sm:px-6">
            <div class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-6xl items-center justify-center">
                <div class="grid w-full overflow-hidden rounded-[2rem] border border-white/70 bg-white/80 shadow-[0_25px_80px_-40px_rgba(15,23,42,0.65)] backdrop-blur lg:grid-cols-[1.1fr_0.9fr]">
                    <div class="hidden bg-slate-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                        <div>
                            <x-application-logo />
                            <p class="mt-8 text-4xl font-black leading-tight">
                                Clean, merge, and report on business listings with confidence.
                            </p>
                            <p class="mt-4 max-w-md text-sm leading-7 text-slate-300">
                                DataVault helps admin teams import large datasets, catch duplicates fast, and turn raw business records into a reliable reporting source.
                            </p>
                        </div>

                        <div class="grid gap-4">
                            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                                <p class="text-xs uppercase tracking-[0.3em] text-sky-300">Included</p>
                                <p class="mt-2 text-lg font-semibold">Bulk import, duplicate detection, merge workflows, and report dashboards.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center p-6 sm:p-10">
                        <div class="w-full max-w-md">
                            <div class="mb-8 lg:hidden">
                                <x-application-logo />
                            </div>

                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
