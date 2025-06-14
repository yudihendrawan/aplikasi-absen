<x-layouts.app :title="__('Izin Karyawan')">
    <div class="overflow-x-auto shadow-md sm:rounded-lg bg-white dark:bg-gray-800 p-4">
        <h1 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Daftar Izin Karyawan</h1>
        <form method="GET" action="{{ route('leaves.index') }}" class="mb-4 flex flex-wrap gap-4 items-end">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cari Nama
                    Karyawan</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dari
                    Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sampai
                    Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
            </div>

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

            <div>
                <label for="sort_dir" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Arah</label>
                <select name="sort_dir" id="sort_dir"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    <option value="asc" @selected(request('sort_dir') == 'asc')>Naik</option>
                    <option value="desc" @selected(request('sort_dir') == 'desc')>Turun</option>
                </select>
            </div>

            <div>
                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">
                    Filter
                </button>
            </div>
            <div>
                <button type="button" onclick="window.location.href = '{{ route('leaves.create') }}'"
                    class="text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-emerald-600 dark:hover:bg-emerald-700">
                    {{ __('Create') }}
                </button>
            </div>
        </form>

        <div id="leaves-container">
            <div id="leave-table">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    @include('pages.leaves.table')
                </table>


            </div>
        </div>

    </div>
    <style>
        #leaves-container {
            transition: opacity 0.3s ease;
            opacity: 1;
        }

        #leaves-container.fade-out {
            opacity: 0;
        }
    </style>

    <script>
        const container = document.querySelector('#leave-table');
        const form = document.querySelector('form[method="GET"]');

        function fetchAndUpdate(url) {
            container.classList.add('fade-out');

            setTimeout(() => {
                fetch(url)
                    .then(res => res.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTable = doc.querySelector('#leave-table');
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
