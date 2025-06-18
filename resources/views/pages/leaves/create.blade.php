<x-layouts.app>
    <x-slot name="title">
        {{ __('Create Leave') }}
    </x-slot>

    <x-ui.breadcrumb :items="[['label' => 'Jadwal Izin', 'url' => route('leaves.index')], ['label' => 'Buat Jadwal']]" />

    {{-- Heading Card --}}
    <div class="mb-6 p-5 bg-blue-50 dark:bg-gray-700 border border-blue-200 dark:border-gray-600 rounded-lg shadow-sm">
        <div class="flex items-center gap-4">
            <div>
                <h2 class="text-lg font-semibold  dark:text-white">Buat Jadwal Izin / Cuti</h2>
                <p class="text-sm  dark:text-gray-300">
                    Form ini digunakan untuk mencatat jadwal izin atau cuti sales
                </p>
            </div>
        </div>
    </div>



    <section class="bg-white dark:bg-gray-800  p-6 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
        <form action="{{ route('leaves.store') }}" method="POST" class=" grid md:grid-cols-2 grid-cols-1 gap-4 mb-4">
            @csrf
            <div class="mb-4">
                <label for="user_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sales</label>
                <select id="user_id" name="user_id" required
                    class="tom-select w-full @error('user_id') border-red-500 @enderror">
                    <option value="">Pilih Sales</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>


                @error('user_id')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>


            <div class="mb-4 ">
                <label for="name"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Leave Name') }}</label>
                <input type="text" id="name" name="name" required
                    class="form-input block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror"
                    placeholder="{{ __('Masukkan nama izin') }}" value="{{ old('name') }}">
                @error('name')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="start_date"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Start Date') }}</label>
                <input type="date" id="start_date" name="start_date" required
                    class="form-input block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('start_date') border-red-500 @enderror"
                    value="{{ old('start_date') }}">
                @error('start_date')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="end_date"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('End Date') }}</label>
                <input type="date" id="end_date" name="end_date" required
                    class="form-input block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('end_date') border-red-500 @enderror"
                    value="{{ old('end_date') }}">
                @error('end_date')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="reason"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Reason') }}</label>
                <textarea id="reason" name="reason" rows="3" required
                    class="form-textarea block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('reason') border-red-500 @enderror"
                    placeholder="{{ __('Masukkan alasan cuti / izin') }}">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="description"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Description') }}</label>
                <textarea id="description" name="description" rows="3" required
                    class="form-textarea block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('description') border-red-500 @enderror"
                    placeholder="{{ __('Masukkan deskripsi') }}">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div></div>
            <div class="flex items-center justify-end">
                <button type="submit"
                    class="text-white transition-all focus:scale-95 hover:scale-95 duration-200 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan</button>
                <button type="button" onclick="window.location='{{ route('leaves.index') }}'"
                    class="ml-2 transition-all focus:scale-95 hover:scale-95 duration-200 text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">Batal</button>
            </div>
        </form>
    </section>

    <script>
        console.log('test');
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // date picker
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');

            // Inisialisasi flatpickr untuk end_date
            const endPicker = flatpickr(endInput, {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
                altInputClass: 'form-input block w-full rounded-lg border text-sm placeholder-gray-400',
                onReady: function(_, __, instance) {
                    instance.altInput.placeholder = "Pilih tanggal selesai";
                }
            });

            // Inisialisasi flatpickr untuk start_date
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

            // onChange ke endPicker untuk membatasi start_date maksimal
            endPicker.config.onChange.push(function(selectedDates) {
                if (selectedDates.length > 0) {
                    startPicker.set('maxDate', selectedDates[0]);
                }
            });

        });
    </script>

</x-layouts.app>
