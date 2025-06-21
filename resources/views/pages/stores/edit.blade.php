<x-layouts.app>
    <x-slot name="title">
        {{ __('Edit Toko') }}
    </x-slot>

    <x-ui.breadcrumb :items="[['label' => 'Daftar Toko', 'url' => route('stores.index')], ['label' => 'Edit Toko']]" />

    <div class="mb-6 p-5 bg-blue-50 dark:bg-gray-700 border border-blue-200 dark:border-gray-600 rounded-lg shadow-sm">
        <div class="flex items-center gap-4">
            <div>
                <h2 class="text-lg font-semibold dark:text-white">Edit Toko</h2>
                <p class="text-sm dark:text-gray-300">
                    Perbarui informasi toko beserta lokasi di peta. Geser marker atau ubah koordinat secara manual jika
                    diperlukan.
                </p>
            </div>
        </div>
    </div>

    <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
        <form action="{{ route('stores.update', $store->id) }}" method="POST"
            class="grid md:grid-cols-2 grid-cols-1 gap-4 mb-4">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nama Toko <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required value="{{ old('name', $store->name) }}"
                    class="form-input block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Telepon
                </label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $store->phone) }}"
                    pattern="^(\+62|62|0)8[1-9][0-9]{6,9}$|^(\+62|62|0)[2-9][0-9]{7,11}$"
                    title="Nomor telepon harus valid di Indonesia"
                    class="form-input block w-full rounded-lg border text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('phone') border-red-500 @enderror">
                @error('phone')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4 col-span-2">
                <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Alamat <span class="text-red-500">*</span>
                </label>
                <textarea id="address" name="address" rows="2" required
                    class="form-textarea block w-full rounded-lg border text-gray-900 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('address') border-red-500 @enderror">{{ old('address', $store->address) }}</textarea>
                @error('address')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4 col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Peta Lokasi <span
                        class="text-red-500">*</span></label>
                <div id="map" class="w-full h-64 rounded-lg border"></div>
                <button type="button" id="locate-btn" class="mt-2 text-sm text-blue-600 hover:underline">Gunakan lokasi
                    saat ini</button>
                <div class="mt-2 flex gap-2">
                    <div class="flex-1">
                        <label class="block mb-1 text-xs text-gray-700 dark:text-gray-300">Latitude</label>
                        <input type="text" id="latitude" name="latitude"
                            value="{{ old('latitude', $store->latitude) }}"
                            class="form-input block w-full rounded-lg border text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('latitude') border-red-500 @enderror">
                    </div>
                    <div class="flex-1">
                        <label class="block mb-1 text-xs text-gray-700 dark:text-gray-300">Longitude</label>
                        <input type="text" id="longitude" name="longitude"
                            value="{{ old('longitude', $store->longitude) }}"
                            class="form-input block w-full rounded-lg border text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('longitude') border-red-500 @enderror">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end col-span-2">
                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Update</button>
                <a href="{{ route('stores.index') }}"
                    class="ml-2 text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">Batal</a>
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
            const defaultLat = latInput.value || -6.200000;
            const defaultLng = lngInput.value || 106.816666;

            const map = L.map('map', {
                fullscreenControl: true
            }).setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            let marker = L.marker([defaultLat, defaultLng], {
                draggable: true
            }).addTo(map);

            // Update inputs saat marker dipindah
            marker.on('dragend', () => {
                const {
                    lat,
                    lng
                } = marker.getLatLng();
                latInput.value = lat.toFixed(8);
                lngInput.value = lng.toFixed(8);
                map.setView([lat, lng]);
            });

            // Jika klik peta, pindahkan marker
            map.on('click', e => {
                marker.setLatLng(e.latlng);
                latInput.value = e.latlng.lat.toFixed(8);
                lngInput.value = e.latlng.lng.toFixed(8);
            });

            // Geocode search
            const geocoder = L.Control.geocoder({
                defaultMarkGeocode: false
            }).addTo(map);

            geocoder.on('markgeocode', e => {
                const center = e.geocode.center;
                marker.setLatLng(center);
                map.setView(center, 15);
                latInput.value = center.lat.toFixed(8);
                lngInput.value = center.lng.toFixed(8);
            });

            // Bind search input
            const searchInput = document.getElementById('geocoder');
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    geocoder.markGeocode(L.Control.Geocoder.nominatim().geocode(searchInput.value,
                        results => {
                            if (results && results.length) {
                                geocoder.fire('markgeocode', {
                                    geocode: results[0]
                                });
                            }
                        }));
                }
            });

            // Geolocation
            document.getElementById('locate-btn').addEventListener('click', () => {
                if (!navigator.geolocation) {
                    alert('Geolokasi tidak didukung browser Anda');
                } else {
                    navigator.geolocation.getCurrentPosition(pos => {
                        const {
                            latitude,
                            longitude
                        } = pos.coords;
                        marker.setLatLng([latitude, longitude]);
                        map.setView([latitude, longitude], 15);
                        latInput.value = latitude.toFixed(8);
                        lngInput.value = longitude.toFixed(8);
                    }, () => alert('Tidak dapat mengambil lokasi'));
                }
            });

            // Update marker saat input manual berubah
            [latInput, lngInput].forEach(input => {
                input.addEventListener('change', () => {
                    const lat = parseFloat(latInput.value) || defaultLat;
                    const lng = parseFloat(lngInput.value) || defaultLng;
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng]);
                });
            });
        });
    </script>
</x-layouts.app>
