<x-guest-layout>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <!-- Logo de la tienda -->
        <div class="flex justify-center mb-6">
            <img src="{{ asset('img/Logo-Colo.png') }}" alt="Logo Tienda de Abarrotes" class="h-16">
            <!-- Si no tienes un logo, puedes quitar esta sección o reemplazarla con un título -->
        </div>

        <h2 class="text-2xl font-bold text-center text-green-600 dark:text-green-400 mb-6">Bienvenido a Tu Tienda de Abarrotes</h2>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

     <!-- Número de Empleado (ID)-->
            <div>
                <x-input-label for="id" :value="__('Número de Empleado')" class="text-gray-700 dark:text-gray-300" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <!-- Reutilizamos el mismo icono de “usuario” o “tarjeta” -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <!-- Aquí puedes reemplazar el SVG si prefieres un icono “#” o “ID” -->
                            <path fill-rule="evenodd" d="M3 3h14v2H3V3zm0 4h14v2H3V7zm0 4h14v2H3v-2zm0 4h14v2H3v-2z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <x-text-input
                        id="id"
                        class="block mt-1 w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 focus:border-green-500 dark:focus:border-green-500 focus:ring-green-500 dark:focus:ring-green-500"
                        type="text"
                        name="id"
                        :value="old('id')"
                        required
                        autofocus
                        autocomplete="off"
                        placeholder="Ingresa tu ID"
                    />
                </div>
                <x-input-error :messages="$errors->get('id')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Contraseña')" class="text-gray-700 dark:text-gray-300" />
                <div class="re        lative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <x-text-input id="password" class="block mt-1 w-full pl-10 pr-10 rounded-lg border-gray-300 dark:border-gray-600 focus:border-green-500 dark:focus:border-green-500 focus:ring-green-500 dark:focus:ring-green-500" type="password" name="password" required autocomplete="current-password" />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button type="button" id="togglePassword" class="text-gray-500 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 toggle-password-icon-hide" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 toggle-password-icon-show hidden" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-green-600 shadow-sm focus:ring-green-500 dark:focus:ring-green-500 dark:focus:ring-offset-gray-800" name="remember">
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Recordarme') }}</span>
                </label>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between mt-4 gap-4">
                @if (Route::has('password.request'))
                    <a class="text-sm text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif

                <x-primary-button class="w-full sm:w-auto justify-center bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-800 border-green-600">
                    {{ __('Iniciar Sesión') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <!-- Script para mostrar/ocultar contraseña -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const showIcon = document.querySelector('.toggle-password-icon-show');
            const hideIcon = document.querySelector('.toggle-password-icon-hide');

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                showIcon.classList.toggle('hidden');
                hideIcon.classList.toggle('hidden');
            });
        });
    </script>
</x-guest-layout>
