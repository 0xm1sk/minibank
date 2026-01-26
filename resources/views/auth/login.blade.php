<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Error Messages -->
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-900 border border-red-700 text-red-200 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded bg-gray-700 border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-300">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-400 hover:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Demo Accounts Info -->
    <div class="mt-6 p-4 bg-gray-700 border border-gray-600 rounded">
        <h3 class="text-sm font-medium text-gray-300 mb-2">Demo Accounts Available:</h3>
        <div class="text-xs text-gray-400 space-y-1">
            <div><strong>Admin:</strong> admin@minibank.com</div>
            <div><strong>Client:</strong> john@example.com</div>
            <div><strong>Manager:</strong> manager@minibank.com</div>
            <div><strong>Password:</strong> password (for all accounts)</div>
        </div>
    </div>
</x-guest-layout>
