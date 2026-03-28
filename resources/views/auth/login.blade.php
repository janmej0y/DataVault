<x-guest-layout>
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.3em] text-sky-600">Admin Login</p>
        <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-950">Sign in to DataVault</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">
            Use the seeded admin account to access imports, duplicate review, and reports.
        </p>
    </div>

    <div class="mt-6 rounded-2xl border border-sky-100 bg-sky-50 px-4 py-3 text-sm text-sky-700">
        Demo credentials: <span class="font-semibold">admin@datavault.test</span> / <span class="font-semibold">password</span>
    </div>

    <x-auth-session-status class="mb-4 mt-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="filter-input mt-2" type="email" name="email" :value="old('email', 'admin@datavault.test')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-sky-700 transition hover:text-sky-800" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="filter-input mt-2" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="inline-flex items-center gap-3 text-sm text-slate-600">
            <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500" name="remember">
            <span>Keep me signed in</span>
        </label>

        <button type="submit" class="btn-primary w-full">
            Log in
        </button>
    </form>
</x-guest-layout>
