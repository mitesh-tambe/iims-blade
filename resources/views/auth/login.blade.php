<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <x-input-label for="email" :value="__('Email Address')"
                class="text-sm font-semibold text-gray-700 dark:text-gray-300" />

            <x-text-input id="email"
                class="block w-full h-14 px-4 rounded-xl border-gray-300 dark:border-gray-700 
               bg-white dark:bg-gray-900
               text-gray-900 dark:text-gray-100
               placeholder-gray-400 dark:placeholder-gray-500
               shadow-sm
               focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20
               transition duration-200"
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                placeholder="Enter your email address" />

            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="mt-5 space-y-2">
            <x-input-label for="password" :value="__('Password')"
                class="text-sm font-semibold text-gray-700 dark:text-gray-300" />

            <x-text-input id="password"
                class="block w-full h-14 px-4 rounded-xl border-gray-300 dark:border-gray-700
               bg-white dark:bg-gray-900
               text-gray-900 dark:text-gray-100
               placeholder-gray-400 dark:placeholder-gray-500
               shadow-sm
               focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20
               transition duration-200"
                type="password" name="password" required autocomplete="current-password"
                placeholder="Enter your password" />

            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        {{-- <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div> --}}

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>

            {{-- register --}}
            @if (Route::has('register'))
                <a class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-3"
                    href="{{ route('register') }}">
                    {{ __('Register') }}
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>
