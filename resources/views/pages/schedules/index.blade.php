<x-layouts.app :title="__('Jadwal Sales')">
    <x-ui.breadcrumb :items="[['label' => 'Jadwal Sales']]" />

    {{-- Heading Card --}}
    <div class="mb-6 p-5 bg-blue-50 dark:bg-gray-700 border border-blue-200 dark:border-gray-600 rounded-lg shadow-sm">
        <div class="flex items-center gap-4">
            {{-- icon calender svg --}}
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide sm:flex hidden lucide-calendar-days-icon lucide-calendar-days">
                <path d="M8 2v4" />
                <path d="M16 2v4" />
                <rect width="18" height="18" x="3" y="4" rx="2" />
                <path d="M3 10h18" />
                <path d="M8 14h.01" />
                <path d="M12 14h.01" />
                <path d="M16 14h.01" />
                <path d="M8 18h.01" />
                <path d="M12 18h.01" />
                <path d="M16 18h.01" />
            </svg>
            <div>
                <h2 class="text-lg font-semibold  dark:text-white">Manajemen Jadwal Sales</h2>
                <p class="text-sm  dark:text-gray-300">
                    Halaman ini berisi daftar dan pengaturan jadwal kunjungan sales ke toko.
                    Gunakan filter untuk mencari berdasarkan nama, tanggal, atau waktu kerja.
                </p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto shadow-md sm:rounded-lg bg-white dark:bg-gray-800 p-4">
        <h1 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Daftar Jadwal Sales</h1>

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('schedules.index') }}" class="mb-4 w-full flex flex-wrap gap-4 items-end">
            {{-- Search --}}
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cari
                    Nama</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            {{-- Start Date --}}
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dari
                    Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
            </div>

            {{-- End Date --}}
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sampai
                    Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
            </div>

            {{-- Sort By --}}
            <div>
                <label for="sort_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Urutkan</label>
                <select name="sort_by" id="sort_by"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    <option value="">-- Pilih --</option>
                    <option value="date" @selected(request('sort_by') == 'date')>Tanggal</option>
                    <option value="check_in" @selected(request('sort_by') == 'check_in')>Jam Masuk</option>
                    <option value="check_out" @selected(request('sort_by') == 'check_out')>Jam Keluar</option>
                    <option value="time_tolerance" @selected(request('sort_by') == 'time_tolerance')>Toleransi</option>
                </select>
            </div>

            {{-- Sort Direction --}}
            <div>
                <label for="sort_dir" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Arah</label>
                <select name="sort_dir" id="sort_dir"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    <option value="asc" @selected(request('sort_dir') == 'asc')>Naik</option>
                    <option value="desc" @selected(request('sort_dir') == 'desc')>Turun</option>
                </select>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <button type="submit"
                    class="inline-flex active:scale-95 transition-all duration-200 justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                    Filter
                </button>

                <a href="{{ route('schedules.index') }}"
                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                    Reset
                </a>
            </div>

            {{-- Create Button --}}
            <div class="ml-auto">
                <button type="button"
                    onclick="window.location.href = '{{ route('schedules.export', request()->all()) }}'"
                    class="text-white bg-indigo-700 active:scale-95 transition-all duration-200 hover:bg-indigo-800 focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-indigo-600 dark:hover:bg-indigo-700">
                    {{ __('Export') }}
                </button>
                <button type="button" onclick="window.location.href = '{{ route('schedules.create') }}'"
                    class="text-white bg-emerald-700 active:scale-95 transition-all duration-200 hover:bg-emerald-800 focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-emerald-600 dark:hover:bg-emerald-700">
                    Tambah
                </button>
            </div>
        </form>

        {{-- Tabel Jadwal --}}
        @include('pages.schedules.table')
        <div id="schedule-container">
            <div id="schedule-table">
                {{-- <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400"> --}}
                {{-- </table> --}}
            </div>
        </div>
    </div>

    {{-- Animasi Fade --}}
    <style>
        #schedule-container {
            transition: opacity 0.3s ease;
            opacity: 1;
        }

        #schedule-container.fade-out {
            opacity: 0;
        }
    </style>

    {{-- Script --}}
    <script>
        const container = document.querySelector('#schedule-table');
        const form = document.querySelector('form[method="GET"]');

        function fetchAndUpdate(url) {
            container.classList.add('fade-out');

            setTimeout(() => {
                fetch(url)
                    .then(res => res.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTable = doc.querySelector('#schedule-table');
                        if (newTable) {
                            container.innerHTML = newTable.innerHTML;
                            history.pushState(null, '', url);
                        }
                        container.classList.remove('fade-out');
                    });
            }, 300);
        }

        document.addEventListener('click', function(e) {
            if (e.target.closest('.pagination a')) {
                e.preventDefault();
                const url = e.target.closest('.pagination a').href;
                fetchAndUpdate(url);
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            const url = form.action + '?' + params;
            fetchAndUpdate(url);
        });

        window.addEventListener('popstate', function() {
            fetchAndUpdate(location.href);
        });

        const clearFilter = document.querySelector('a[href="{{ route('schedules.index') }}"]');
        clearFilter.addEventListener('click', function(e) {
            e.preventDefault();
            form.reset();
            fetchAndUpdate(this.href);
        });
    </script>
    <script>
        // date picker
        document.addEventListener('DOMContentLoaded', function() {

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
        // end date picker  
    </script>
</x-layouts.app>
