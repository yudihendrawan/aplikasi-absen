<x-layouts.app>
    <x-slot name="title">
        {{ __('Edit Leave') }}
    </x-slot>

    <x-slot name="header">
        <h1>Edit Leave Page</h1>
    </x-slot>

    <div class="mt-6 space-x-2 items-center justify-center">
        <div>
            <form action="{{ route('leaves.update', $leave->id) }}" method="POST"
                class="bg-white grid md:grid-cols-2 grid-cols-1 gap-4 dark:bg-gray-800 shadow-md rounded px-8 mx-8 pt-6 pb-8 mb-4">
                @csrf
                @method('PUT')

                <div class="mb-4 md:col-span-2">
                    <label for="user_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('User') }}
                    </label>
                    <select id="user_id" name="user_id" required
                        class="tom-select @error('user_id') border-red-500 @enderror">
                        <option value="" disabled>{{ __('Pilih pegawai') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ old('user_id', $leave->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4 md:col-span-2">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('Leave Name') }}
                    </label>
                    <input type="text" id="name" name="name" required
                        value="{{ old('name', $leave->name) }}"
                        class="form-input block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 @error('name') border-red-500 @enderror"
                        placeholder="{{ __('Masukkan nama izin') }}">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="start_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('Start Date') }}
                    </label>
                    <input type="date" id="start_date" name="start_date" required
                        value="{{ old('start_date', $leave->start_date) }}"
                        class="form-input block w-full rounded-lg border text-gray-900 text-sm dark:bg-gray-700 dark:border-gray-600 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="end_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('End Date') }}
                    </label>
                    <input type="date" id="end_date" name="end_date" required
                        value="{{ old('end_date', $leave->end_date) }}"
                        class="form-input block w-full rounded-lg border text-gray-900 text-sm dark:bg-gray-700 dark:border-gray-600 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="reason" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('Reason') }}
                    </label>
                    <textarea id="reason" name="reason" rows="3" required
                        class="form-textarea block w-full rounded-lg border text-sm text-gray-900 dark:bg-gray-700 dark:border-gray-600 @error('reason') border-red-500 @enderror"
                        placeholder="{{ __('Masukkan alasan cuti / izin') }}">{{ old('reason', $leave->reason) }}</textarea>
                    @error('reason')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('Description') }}
                    </label>
                    <textarea id="description" name="description" rows="3"
                        class="form-textarea block w-full rounded-lg border text-sm text-gray-900 dark:bg-gray-700 dark:border-gray-600 @error('description') border-red-500 @enderror"
                        placeholder="{{ __('Masukkan deskripsi') }}">{{ old('description', $leave->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div></div>
                <div class="flex items-center justify-end">
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">
                        {{ __('Update') }}
                    </button>
                    <a href="{{ route('leaves.index') }}"
                        class="ml-2 text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>


    {{-- Flatpickr script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');

            const endPicker = flatpickr(endInput, {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
                altInputClass: 'form-input block w-full rounded-lg border text-sm placeholder-gray-400',
                onReady: function(_, __, instance) {
                    instance.altInput.placeholder = "Pilih tanggal selesai";
                }
            });

            const startPicker = flatpickr(startInput, {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
                altInputClass: 'form-input block w-full rounded-lg border text-sm placeholder-gray-400',
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        endPicker.set('minDate', selectedDates[0]);
                    }
                },
                onReady: function(_, __, instance) {
                    instance.altInput.placeholder = "Pilih tanggal mulai";
                }
            });

            endPicker.config.onChange.push(function(selectedDates) {
                if (selectedDates.length > 0) {
                    startPicker.set('maxDate', selectedDates[0]);
                }
            });
        });
    </script>
</x-layouts.app>
