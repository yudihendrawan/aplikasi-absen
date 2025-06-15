<x-layouts.app>
    <x-slot name="title">
        {{ __('Create User') }}
    </x-slot>


    <div class="mt-6 space-x-2   items-center justify-center">
        <div class=" ">



            <form action="{{ route('users.store') }}" method="POST"
                class="bg-white grid md:grid-cols-2 grid-cols-1 gap-4 dark:bg-gray-800 shadow-md rounded px-8 mx-8 pt-6 pb-8 mb-4">
                @csrf



                <div class="mb-4">
                    <label for="name"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Nama Karyawan') }}</label>
                    <input type="text" id="name" name="name" required
                        class="form-input block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror"
                        placeholder="{{ __('Masukkan nama karyawan') }}" value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Email Karyawan') }}</label>
                    <input type="email" id="email" name="email" required
                        class="form-input block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('email') border-red-500 @enderror"
                        placeholder="{{ __('Masukkan email karyawan') }}" value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="phone"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('No. Handphone Karyawan') }}</label>
                    <input type="tel" id="phone" name="phone" required pattern="[0-9]{10,14}"
                        inputmode="numeric"
                        class="form-input block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('phone') border-red-500 @enderror"
                        placeholder="{{ __('Contoh: 081234567890') }}" value="{{ old('phone') }}">
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Password') }}</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="form-input block w-full pr-10 rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('password') border-red-500 @enderror"
                            value="{{ old('password') }}" placeholder="{{ __('Buat password karyawan') }}">

                        {{-- Toggle Eye Icon --}}
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 flex items-center px-3">
                            <svg id="eye" xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5 text-gray-500 dark:text-gray-300 hidden" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>

                            <svg id="eye-slash" xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5 text-gray-500 dark:text-gray-300 " fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.954 9.954 0 012.293-3.95M6.267 6.267A9.952 9.952 0 0112 5c4.477 0 8.267 2.943 9.542 7a9.973 9.973 0 01-4.21 5.568M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="role"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Role') }}</label>
                    <select id="role" name="role" required
                        class="form-select block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        @foreach ($roles as $role)
                            <option class="capitalize" value="{{ $role->value }}"
                                {{ old('role') === $role->value ? 'selected' : '' }}>
                                {{ $role->value }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div></div>
                <div></div>
                <div class="flex items-center justify-end">
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{ __('Submit') }}</button>
                    <button type="button" onclick="window.location='{{ route('leaves.index') }}'"
                        class="ml-2 text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">{{ __('Cancel') }}</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye');
            const eyeSlashIcon = document.getElementById('eye-slash');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function() {
                // Hapus semua karakter non-digit
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>

</x-layouts.app>
