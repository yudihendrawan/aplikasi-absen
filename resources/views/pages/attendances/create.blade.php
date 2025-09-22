<x-layouts.app :title="__('Absen Hari Ini')">
    <!-- Tambahkan CSS Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .tab-button {
            transition: all 0.3s ease;
        }

        .tab-button.active {
            border-bottom: 3px solid #4f46e5;
            color: #4f46e5;
            font-weight: 600;
        }

        .camera-preview {
            width: 100%;
            height: 250px;
            background-color: #f3f4f6;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .camera-icon {
            font-size: 3rem;
            color: #9ca3af;
        }

        .captured-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .camera-button {
            position: absolute;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
            z-index: 10;
        }

        .camera-button-inner {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #4f46e5;
        }

        @media (max-width:640px) {
            .tab-container {
                flex-direction: column;
            }

            .tab-button {
                width: 100%;
                text-align: center;
                padding: .75rem 0;
            }
        }
    </style>

    @php
        $showMasuk = !$hasAttendanceIn;
        $showPulang = $hasAttendanceIn && !$hasAttendanceOut;
        $initialTab = $showMasuk ? 'Masuk' : ($showPulang ? 'Pulang' : null);
    @endphp

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-lg shadow-lg">
        <h1 class="text-xl font-bold mb-6 text-gray-800 dark:text-white text-center">Absen Kunjungan Toko</h1>

        <!-- Tab Navigation -->
        <div class="flex tab-container border-b mb-6 overflow-x-auto">
            @if ($showMasuk)
                <button id="tabMasuk"
                    class="tab-button px-4 py-2 text-sm sm:text-base {{ $initialTab === 'Masuk' ? 'active' : '' }}">Absen
                    Masuk</button>
            @endif

            @if ($showPulang)
                <button id="tabPulang"
                    class="tab-button px-4 py-2 text-sm sm:text-base {{ $initialTab === 'Pulang' ? 'active' : '' }}">Absen
                    Pulang</button>
            @endif
        </div>

        {{-- FORM MASUK --}}
        @if ($showMasuk)
            <form method="POST" action="{{ route('attendances.store') }}" enctype="multipart/form-data"
                id="absenMasukForm" class="tab-content {{ $initialTab === 'Masuk' ? 'active' : '' }}">
                @csrf

                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Foto Selfie
                        Masuk</label>
                    <div class="camera-preview" id="cameraPreviewMasuk" title="Klik untuk pilih foto">
                        <div class="camera-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="camera-button" id="captureButtonMasuk">
                            <div class="camera-button-inner"></div>
                        </div>
                    </div>

                    <input type="file" name="image_masuk" id="fotoMasukInput" accept="image/*" capture="user"
                        class="hidden">
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Klik area di atas untuk memilih foto
                        (kamera/galeri).</p>
                </div>

                {{-- Map Preview --}}
                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi Saat
                        Ini</label>
                    <div id="mapPreviewMasuk" class="w-full h-48 sm:h-64 rounded-lg border border-gray-300"></div>
                    <p id="locationStatusMasuk" class="mt-2 text-sm text-gray-600 dark:text-gray-300"></p>
                </div>

                {{-- Hidden --}}
                <input type="hidden" name="storeVisitId" id="storeVisitIdMasuk" value="{{ $storeVisit->id }}">
                <input type="hidden" name="type" id="typeMasuk" value="checkIn">
                <input type="hidden" name="latitude" id="latitudeMasuk">
                <input type="hidden" name="longitude" id="longitudeMasuk">
                <input type="hidden" name="accuracy" id="accuracyMasuk">

                <button type="submit" id="submitMasukBtn"
                    class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                    Absen Masuk
                </button>
            </form>
        @endif

        {{-- FORM PULANG --}}
        @if ($showPulang)
            <form method="POST" action="{{ route('attendances.create-checkout') }}" enctype="multipart/form-data"
                id="absenPulangForm" class="tab-content {{ $initialTab === 'Pulang' ? 'active' : '' }}">
                @csrf

                <div class="mb-6">
                    <label for="nominal_invoice"
                        class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Nominal Invoice</label>
                    <input type="number" name="nominal_invoice" id="nominal_invoice"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                        placeholder="Masukkan nominal invoice">
                </div>
                <div class="mb-6">
                    <label for="note"
                        class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
                    <input type="text" name="note" id="note"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                        placeholder="Masukkan catatan (optional)">
                </div>

                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Foto Bukti
                        Invoice</label>
                    <input type="file" name="bukti_invoice" id="buktiInvoice" accept="image/*" capture="environment"
                        class="block w-full text-sm text-gray-500">
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Upload bukti invoice.</p>
                </div>

                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Foto Selfie
                        Pulang</label>
                    <div class="camera-preview" id="cameraPreviewPulang" title="Klik untuk pilih foto">
                        <div class="camera-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="camera-button" id="captureButtonPulang">
                            <div class="camera-button-inner"></div>
                        </div>
                    </div>

                    <input type="file" name="image_pulang" id="fotoPulangInput" accept="image/*" capture="user"
                        class="hidden">
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Klik area di atas untuk memilih foto
                        (kamera/galeri).</p>
                </div>

                {{-- Map Preview --}}
                <div class="mb-6">
                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi Saat
                        Ini</label>
                    <div id="mapPreviewPulang" class="w-full h-48 sm:h-64 rounded-lg border border-gray-300"></div>
                    <p id="locationStatusPulang" class="mt-2 text-sm text-gray-600 dark:text-gray-300"></p>
                </div>

                {{-- Hidden --}}
                <input type="hidden" name="storeVisitId" id="storeVisitIdPulang" value="{{ $storeVisit->id }}">
                <input type="hidden" name="type" id="typePulang" value="checkOut">
                <input type="hidden" name="latitude" id="latitudePulang">
                <input type="hidden" name="longitude" id="longitudePulang">
                <input type="hidden" name="accuracy" id="accuracyPulang">

                <button type="submit" id="submitPulangBtn"
                    class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                    Absen Pulang
                </button>
            </form>
        @endif

        <!-- Panduan GPS -->
        <div id="gpsInstructions"
            class="mt-6 p-3 bg-blue-50 text-blue-800 rounded-lg text-sm dark:bg-blue-900 dark:text-blue-200">
            <strong>Tips untuk presisi GPS lebih baik:</strong>
            <ul class="list-disc pl-5 mt-1 space-y-1">
                <li>Pastikan GPS perangkat Anda aktif</li>
                <li>Buka area terbuka, hindari gedung tinggi</li>
                <li>Hindari penggunaan VPN atau proxy</li>
                <li>Tunggu beberapa detik sampai akurasi meningkat</li>
            </ul>
        </div>
    </div>

    <!-- Tambahkan JS Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        /* --- Sederhana: validasi hanya berdasarkan radius --- */
        const tokoLat = {!! json_encode($storeVisit->store->latitude) !!};
        const tokoLng = {!! json_encode($storeVisit->store->longitude) !!};
        const initialTab = {!! json_encode($initialTab) !!}; // 'Masuk' | 'Pulang' | null
        const maxRadiusMeter = 100; // sesuaikan kalau mau longgar (mis: 150)

        let mapMasuk = null,
            mapPulang = null;
        let watchIdMasuk = null,
            watchIdPulang = null;
        let bestPositionMasuk = null,
            bestPositionPulang = null;

        function getDistance(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function initMapSafe(elementId, lat, lng) {
            const el = document.getElementById(elementId);
            if (!el) return null;

            try {
                lat = (typeof lat === 'string') ? parseFloat(lat) : lat;
                lng = (typeof lng === 'string') ? parseFloat(lng) : lng;
                if (!isFinite(lat) || !isFinite(lng)) return null;

                const map = L.map(el).setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                L.marker([lat, lng]).addTo(map).bindPopup('Lokasi Toko');
                L.circle([lat, lng], {
                    radius: maxRadiusMeter,
                    color: 'blue',
                    fillColor: '#cce5ff',
                    fillOpacity: 0.2
                }).addTo(map);

                return map;
            } catch (err) {
                console.error('initMapSafe error', err);
                return null;
            }
        }

        // Simple position getter (no complex watch fallback)
        function getCurrentPositionPromise() {
            return new Promise((resolve, reject) => {
                if (!('geolocation' in navigator)) {
                    return reject(new Error('Browser tidak mendukung geolocation.'));
                }
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            });
        }

        // Setup file preview (klik area -> buka file picker)
        function setupFilePreview(previewId, inputId) {
            const preview = document.getElementById(previewId);
            const input = document.getElementById(inputId);
            if (!preview || !input) return;

            preview.addEventListener('click', () => input.click());

            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        preview.innerHTML = `
                            <img src="${evt.target.result}" class="captured-image" alt="Selected Photo">
                            <div class="camera-button" onclick="document.getElementById('${inputId}').click();"><div class="camera-button-inner"></div></div>
                        `;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        function updateLocationUI(position, type) {
            if (!position || !position.coords) return;
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const accuracy = position.coords.accuracy;

            const latEl = document.getElementById(`latitude${type}`);
            const lngEl = document.getElementById(`longitude${type}`);
            const accEl = document.getElementById(`accuracy${type}`);
            const locStatus = document.getElementById(`locationStatus${type}`);
            const submitBtn = document.getElementById(`submit${type}Btn`);

            if (latEl) latEl.value = lat;
            if (lngEl) lngEl.value = lng;
            if (accEl) accEl.value = accuracy;

            // update map marker if exists
            const map = (type === 'Masuk') ? mapMasuk : mapPulang;
            if (map) {
                if (!map._userMarker) {
                    map._userMarker = L.marker([lat, lng]).addTo(map).bindPopup('Lokasi Anda');
                } else {
                    map._userMarker.setLatLng([lat, lng]);
                }
            }

            const dist = getDistance(lat, lng, tokoLat, tokoLng);
            if (!locStatus) return;

            if (dist <= maxRadiusMeter) {
                locStatus.textContent = `Anda berada dalam radius toko (${Math.round(dist)}m) — absen boleh dilakukan.`;
                locStatus.className = 'mt-2 text-sm text-green-600 dark:text-green-300';
                if (submitBtn) submitBtn.disabled = false;
            } else {
                locStatus.textContent =
                    `Anda berada di luar radius toko (${Math.round(dist)}m). Absen tidak dapat dilakukan.`;
                locStatus.className = 'mt-2 text-sm text-red-600 dark:text-red-300';
                if (submitBtn) submitBtn.disabled = true;
            }
        }

        /* === TAB SWITCHER (global) === */
        function showTab(name) {
            const tabMasukBtn = document.getElementById('tabMasuk');
            const tabPulangBtn = document.getElementById('tabPulang');
            const absenMasukForm = document.getElementById('absenMasukForm');
            const absenPulangForm = document.getElementById('absenPulangForm');

            if (tabMasukBtn) tabMasukBtn.classList.toggle('active', name === 'Masuk');
            if (tabPulangBtn) tabPulangBtn.classList.toggle('active', name === 'Pulang');

            if (absenMasukForm) absenMasukForm.classList.toggle('active', name === 'Masuk');
            if (absenPulangForm) absenPulangForm.classList.toggle('active', name === 'Pulang');

            // invalidate maps after switching
            setTimeout(() => {
                try {
                    if (mapMasuk) mapMasuk.invalidateSize();
                } catch (e) {}
                try {
                    if (mapPulang) mapPulang.invalidateSize();
                } catch (e) {}
            }, 200);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // setup previews
            setupFilePreview('cameraPreviewMasuk', 'fotoMasukInput');
            setupFilePreview('cameraPreviewPulang', 'fotoPulangInput');

            // init maps (safe)
            mapMasuk = initMapSafe('mapPreviewMasuk', tokoLat, tokoLng);
            mapPulang = initMapSafe('mapPreviewPulang', tokoLat, tokoLng);

            // attach tab click events if buttons exist
            const tabMasukBtn = document.getElementById('tabMasuk');
            const tabPulangBtn = document.getElementById('tabPulang');
            if (tabMasukBtn) tabMasukBtn.addEventListener('click', () => showTab('Masuk'));
            if (tabPulangBtn) tabPulangBtn.addEventListener('click', () => showTab('Pulang'));

            // ensure there's one active form shown (use initialTab if available)
            const anyActive = document.querySelector('.tab-content.active');
            if (!anyActive) {
                if (initialTab === 'Masuk' && tabMasukBtn) showTab('Masuk');
                else if (initialTab === 'Pulang' && tabPulangBtn) showTab('Pulang');
                else if (tabMasukBtn) showTab('Masuk');
                else if (tabPulangBtn) showTab('Pulang');
            }

            // start geolocation + watchers only for forms that exist
            if (document.getElementById('absenMasukForm')) {
                getCurrentPositionPromise().then(pos => {
                    bestPositionMasuk = pos;
                    updateLocationUI(pos, 'Masuk');

                    watchIdMasuk = navigator.geolocation.watchPosition(newPos => {
                        if (!bestPositionMasuk || newPos.coords.accuracy < bestPositionMasuk.coords
                            .accuracy) {
                            bestPositionMasuk = newPos;
                            updateLocationUI(newPos, 'Masuk');
                        }
                    }, err => console.warn('watchPosition Masuk error', err), {
                        enableHighAccuracy: true,
                        maximumAge: 10000
                    });
                }).catch(err => {
                    const el = document.getElementById('locationStatusMasuk');
                    if (el) el.textContent =
                        'Gagal mendapatkan lokasi. Pastikan GPS aktif dan izinkan lokasi.';
                    console.warn('getCurrentPosition Masuk error', err);
                });
            }

            if (document.getElementById('absenPulangForm')) {
                getCurrentPositionPromise().then(pos => {
                    bestPositionPulang = pos;
                    updateLocationUI(pos, 'Pulang');

                    watchIdPulang = navigator.geolocation.watchPosition(newPos => {
                        if (!bestPositionPulang || newPos.coords.accuracy < bestPositionPulang
                            .coords.accuracy) {
                            bestPositionPulang = newPos;
                            updateLocationUI(newPos, 'Pulang');
                        }
                    }, err => console.warn('watchPosition Pulang error', err), {
                        enableHighAccuracy: true,
                        maximumAge: 10000
                    });
                }).catch(err => {
                    const el = document.getElementById('locationStatusPulang');
                    if (el) el.textContent =
                        'Gagal mendapatkan lokasi. Pastikan GPS aktif dan izinkan lokasi.';
                    console.warn('getCurrentPosition Pulang error', err);
                });
            }

            // form submit guards
            const formMasuk = document.getElementById('absenMasukForm');
            if (formMasuk) {
                formMasuk.addEventListener('submit', function(e) {
                    const lat = parseFloat(document.getElementById('latitudeMasuk')?.value || '0');
                    const lng = parseFloat(document.getElementById('longitudeMasuk')?.value || '0');
                    if (!lat || !lng) {
                        e.preventDefault();
                        alert(
                            'Lokasi belum tersedia. Pastikan GPS aktif dan izinkan lokasi sebelum absen.'
                        );
                        return;
                    }
                    const dist = getDistance(lat, lng, tokoLat, tokoLng);
                    if (dist > maxRadiusMeter) {
                        e.preventDefault();
                        alert(
                            `Anda berada ${Math.round(dist)}m dari toko (melebihi ${maxRadiusMeter}m). Absen tidak diizinkan.`
                        );
                        return;
                    }
                    const f = document.getElementById('fotoMasukInput');
                    if (!f || !(f.files && f.files[0])) {
                        e.preventDefault();
                        alert('Silakan pilih foto selfie untuk absen masuk.');
                        return;
                    }
                    if (watchIdMasuk) navigator.geolocation.clearWatch(watchIdMasuk);
                });
            }

            const formPulang = document.getElementById('absenPulangForm');
            if (formPulang) {
                formPulang.addEventListener('submit', function(e) {
                    const lat = parseFloat(document.getElementById('latitudePulang')?.value || '0');
                    const lng = parseFloat(document.getElementById('longitudePulang')?.value || '0');
                    if (!lat || !lng) {
                        e.preventDefault();
                        alert(
                            'Lokasi belum tersedia. Pastikan GPS aktif dan izinkan lokasi sebelum absen pulang.'
                        );
                        return;
                    }
                    const dist = getDistance(lat, lng, tokoLat, tokoLng);
                    if (dist > maxRadiusMeter) {
                        e.preventDefault();
                        alert(
                            `Anda berada ${Math.round(dist)}m dari toko (melebihi ${maxRadiusMeter}m). Absen tidak diizinkan.`
                        );
                        return;
                    }
                    const f = document.getElementById('fotoPulangInput');
                    const bukti = document.getElementById('buktiInvoice');
                    const nominal = document.getElementById('nominal_invoice');
                    if (!f || !(f.files && f.files[0])) {
                        e.preventDefault();
                        alert('Silakan pilih foto selfie untuk absen pulang.');
                        return;
                    }
                    if (!bukti || !(bukti.files && bukti.files[0])) {
                        e.preventDefault();
                        alert('Silakan upload bukti invoice.');
                        return;
                    }
                    if (!nominal || !nominal.value || isNaN(nominal.value) || Number(nominal.value) <= 0) {
                        e.preventDefault();
                        alert('Masukkan nominal invoice yang valid.');
                        return;
                    }
                    if (watchIdPulang) navigator.geolocation.clearWatch(watchIdPulang);
                });
            }
        });
    </script>
</x-layouts.app>
