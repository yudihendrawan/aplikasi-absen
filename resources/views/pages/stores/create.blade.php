<x-layouts.app>
    <x-slot name="title">
        {{ __('Tambah Toko') }}
    </x-slot>

    <x-ui.breadcrumb :items="[['label' => 'Daftar Toko', 'url' => route('stores.index')], ['label' => 'Tambah Toko']]" />

    {{-- Heading Card --}}
    <div class="mb-6 p-5 bg-blue-50 dark:bg-gray-700 border border-blue-200 dark:border-gray-600 rounded-lg shadow-sm">
        <div class="flex items-center gap-4">
            <div>
                <h2 class="text-lg font-semibold dark:text-white">Tambah Toko</h2>
                <p class="text-sm dark:text-gray-300">
                    Gunakan form ini untuk menambah data toko baru beserta alamat dan lokasi di peta. Cari tempat, seret
                    marker, atau masukkan koordinat secara manual.
                </p>
            </div>
        </div>
    </div>

    <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
        <form action="{{ route('stores.store') }}" method="POST" class="grid md:grid-cols-2 grid-cols-1 gap-4 mb-4">
            @csrf

            {{-- Nama Toko --}}
            <div class="mb-4">
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nama Toko <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required
                    class="form-input block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror"
                    placeholder="Masukkan nama toko" value="{{ old('name') }}">
                @error('name')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>


            {{-- Telepon Toko --}}
            <div class="mb-4">
                <label for="phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Telepon
                </label>
                <input type="tel" id="phone" name="phone"
                    pattern="^(\+62|62|0)8[1-9][0-9]{6,9}$|^(\+62|62|0)[2-9][0-9]{7,11}$"
                    title="Nomor telepon harus dimulai dengan +62, 62, atau 0 dan sesuai format nomor HP atau kantor di Indonesia"
                    class="form-input block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('phone') border-red-500 @enderror"
                    placeholder="Contoh: 08123456789 atau 0218765432" value="{{ old('phone') }}">
                @error('phone')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Nomor HP (contoh: <code>08123456789</code>) atau telepon kantor (contoh: <code>0218765432</code>)
                </p>
            </div>


            {{-- Alamat Toko --}}
            <div class="mb-4 col-span-2">
                <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Alamat <span class="text-red-500">*</span>
                </label>
                <textarea id="address" name="address" rows="2" required
                    class="form-textarea block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('address') border-red-500 @enderror"
                    placeholder="Masukkan alamat toko">{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>



            {{-- Peta Lokasi --}}
            <div class="mb-4 col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Peta Lokasi <span class="text-red-500">*</span>
                </label>
                <div id="map" class="w-full h-64 rounded-lg border"></div>
                <button type="button" id="locate-btn"
                    class="mt-2 active:scale-95 transition-all text-sm text-blue-600 hover:underline">Gunakan lokasi
                    saat ini</button>
                <div class="mt-2 flex gap-2">
                    <div class="flex-1">
                        <label title="Salin koordinat dari Google Maps dan tempel di sini"
                            class="block mb-1 text-xs text-gray-700 dark:text-gray-300">Latitude</label>
                        <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}"
                            class="form-input block w-full rounded-lg border text-gray-900 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('latitude') border-red-500 @enderror">
                        @error('latitude')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Kamu bisa mencari tempat di <a href="https://www.google.com/maps" target="_blank"
                                class="text-blue-600 underline">Google Maps</a>,
                            klik kanan pada titik lokasi &rarr; pilih "Salin Koordinat", lalu tempel di sini.
                        </p>
                    </div>

                    <div class="flex-1">
                        <label title="Salin koordinat dari Google Maps dan tempel di sini"
                            class="block mb-1 text-xs text-gray-700 dark:text-gray-300">Longitude</label>
                        <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}"
                            class="form-input block w-full rounded-lg border text-gray-900 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('longitude') border-red-500 @enderror">
                        @error('longitude')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Contoh: <code>-6.200000, 106.816666</code> — hasil dari "Salin Koordinat" di Google Maps.
                        </p>
                    </div>

                </div>
            </div>


            <div></div>
            <div class="flex items-center justify-end col-span-2">
                <button type="submit"
                    class="text- active:scale-95 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 transition-all dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan</button>
                <button type="button" onclick="window.location='{{ route('stores.index') }}'"
                    class="ml-2 text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 transition-all active:scale-95  dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">Batal</button>
            </div>
        </form>
    </section>

    {{-- Leaflet & Geocoder CSS/JS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    {{-- Fullscreen plugin Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet.fullscreen/Control.FullScreen.css" />
    <script src="https://unpkg.com/leaflet.fullscreen/Control.FullScreen.js"></script>

    <style>
        #map:-webkit-full-screen,
        #map:fullscreen {
            width: 100% !important;
            height: 100% !important;
        }
    </style>

    {{-- Script Map Picker with Search & Geolocation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            let defaultLat = parseFloat(latInput.value) || -6.200000;
            let defaultLng = parseFloat(lngInput.value) || 106.816666;

            const map = L.map('map', {
                fullscreenControl: true
            }).setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            let marker = L.marker([defaultLat, defaultLng], {
                draggable: true
            }).addTo(map);

            // Fungsi untuk update marker dan input
            const updateMarker = (lat, lng) => {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 15);
                latInput.value = lat.toFixed(8);
                lngInput.value = lng.toFixed(8);
            };

            // Ambil lokasi awal secara otomatis (presisi tinggi)
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        const {
                            latitude,
                            longitude
                        } = pos.coords;
                        console.log('Posisi awal:', latitude, longitude);
                        updateMarker(latitude, longitude);
                    },
                    err => {
                        console.warn('Gagal ambil posisi awal:', err.message);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }

            // Drag marker → update input
            marker.on('dragend', () => {
                const {
                    lat,
                    lng
                } = marker.getLatLng();
                updateMarker(lat, lng);
            });

            // Klik peta → pindah marker
            map.on('click', e => {
                updateMarker(e.latlng.lat, e.latlng.lng);
            });

            // Geocoder
            const geocoder = L.Control.geocoder({
                defaultMarkGeocode: false
            }).addTo(map);

            geocoder.on('markgeocode', e => {
                const center = e.geocode.center;
                updateMarker(center.lat, center.lng);
            });

            // Tombol "Gunakan Lokasi Saat Ini"
            document.getElementById('locate-btn').addEventListener('click', () => {
                if (!navigator.geolocation) {
                    alert('Geolokasi tidak didukung browser Anda');
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    pos => {
                        const {
                            latitude,
                            longitude
                        } = pos.coords;
                        console.log('Lokasi terkini:', latitude, longitude);
                        updateMarker(latitude, longitude);
                    },
                    err => {
                        alert('Tidak dapat mengambil lokasi: ' + err.message);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            });

            // Update marker saat input manual berubah
            [latInput, lngInput].forEach(input => {
                input.addEventListener('change', () => {
                    const lat = parseFloat(latInput.value) || defaultLat;
                    const lng = parseFloat(lngInput.value) || defaultLng;
                    updateMarker(lat, lng);
                });
            });
        });
    </script>

</x-layouts.app>
