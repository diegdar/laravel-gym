<div class="flex flex-col gap-6">
    <x-auth-header title="Iniciar sesion en tu cuenta" description="Introduce tu email y contraseña" />
    {{-- Font Awesome: icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">    

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email')"
            type="email"
            name="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model="password"
                :label="__('Contraseña')"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Password"
            />

            @if (Route::has('password.request'))
                <flux:link class="absolute right-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('Has olvidado tu contraseña?') }}
                </flux:link>
            @endif
        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="__('Recuerdame')" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Iniciar sesion') }}</flux:button>
        </div>
    </form>
    <!-- Github login -->
    <div class="flex items-center justify-center">
        <a href="{{ route('auth.github') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-md transition duration-150 ease-in-out dark:bg-gray-700 dark:hover:bg-gray-600">
            {{ __('Iniciar sesión con GitHub') }}
            <i class="fa-brands fa-github fa-lg ml-3" style="color: #eef6ff;"></i>
        </a>
    </div>
    <!-- Google login -->
    <div class="flex items-center justify-center">
        <a href="{{ route('auth.google') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-md transition duration-150 ease-in-out dark:bg-gray-700 dark:hover:bg-gray-600">
            {{ __('Iniciar sesión con Google') }}
            <i class="fa-brands fa-google fa-lg ml-3" style="color: red;"></i>
        </a>
    </div>

    @if (Route::has('register'))
      <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
          No tienes una cuenta?
          <flux:link :href="route('register')" wire:navigate> Registrarse</flux:link>
      </div>
    @endif
</div>
