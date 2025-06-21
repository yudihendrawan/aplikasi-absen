<x-layouts.app :title="__('Daftar Toko')">
    <x-ui.breadcrumb :items="[['label' => 'Daftar Toko']]" />

    {{-- Heading Card --}}
    <div class="mb-6 p-5 bg-blue-50 dark:bg-gray-700 border border-blue-200 dark:border-gray-600 rounded-lg shadow-sm">
        <div class="flex items-center gap-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-store-icon lucide-store">
                <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
                <path d="M2 7h20" />
                <path
                    d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7" />
            </svg>
            <div>
                <h2 class="text-lg font-semibold dark:text-white">Manajemen Toko</h2>
                <p class="text-sm dark:text-gray-300">
                    Halaman ini menampilkan daftar toko yang terdaftar. Gunakan filter untuk mencari toko berdasarkan
                    nama atau lokasi.
                </p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto shadow-md sm:rounded-lg bg-white dark:bg-gray-800 p-4">
        <h1 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Daftar Toko</h1>
        <form method="GET" action="{{ route('stores.index') }}" class="mb-4 flex flex-wrap w-full gap-4 items-end">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cari Nama
                    Toko</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div>
                <button type="submit"
                    class="text-white transition-all focus:scale-95 hover:scale-95 duration-200 bg-blue-700 hover:bg-blue-800 focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">
                    Filter
                </button>
            </div>
            <div class="ml-auto">
                <button type="button" onclick="window.location.href = '{{ route('stores.create') }}'"
                    class="text-white transition-all focus:scale-95 hover:scale-95 duration-200 bg-emerald-700 hover:bg-emerald-800 focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-emerald-600 dark:hover:bg-emerald-700">
                    Tambah
                </button>
            </div>
        </form>

        <div id="stores-container">
            <div id="store-table">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    @include('pages.stores.table')
                </table>
            </div>
        </div>
    </div>

    <style>
        #stores-container {
            transition: opacity 0.3s ease;
            opacity: 1;
        }

        #stores-container.fade-out {
            opacity: 0;
        }
    </style>

    <script>
        const container = document.querySelector('#store-table');
        const form = document.querySelector('form[method="GET"]');

        function fetchAndUpdate(url) {
            container.classList.add('fade-out');

            setTimeout(() => {
                fetch(url)
                    .then(res => res.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTable = doc.querySelector('#store-table');
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
</x-layouts.app>
