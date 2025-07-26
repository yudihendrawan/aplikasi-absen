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
            background-color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .camera-button-inner {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #4f46e5;
        }

        @media (max-width: 640px) {
            .tab-container {
                flex-direction: column;
            }

            .tab-button {
                width: 100%;
                text-align: center;
                padding: 0.75rem 0;
            }
        }
    </style>

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-lg shadow-lg">
        <h1 class="text-xl font-bold mb-6 text-gray-800 dark:text-white text-center">Absen Kunjungan Toko</h1>

        <!-- Tab Navigation -->
        <div class="flex tab-container border-b mb-6 overflow-x-auto">
            <button id="tabMasuk" class="tab-button active px-4 py-2 text-sm sm:text-base">Absen Masuk</button>
            <button id="tabPulang" class="tab-button px-4 py-2 text-sm sm:text-base">Absen Pulang</button>
        </div>

        <!-- Form Absen Masuk -->
        <form method="POST" action="{{ route('attendances.store') }}" enctype="multipart/form-data" id="absenMasukForm"
            class="tab-content active">
            @csrf

            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Foto Selfie Masuk</label>
                <div class="camera-preview" id="cameraPreviewMasuk">
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
                <input type="file" name="image" id="fotoMasukInput" accept="image/*" capture="user" class="hidden">
                <input type="hidden" name="image" id="fotoMasukData">
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Foto wajah Anda saat absen masuk</p>
            </div>

            {{-- Map Preview --}}
            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi Saat Ini</label>
                <div id="mapPreviewMasuk" class="w-full h-48 sm:h-64 rounded-lg border border-gray-300"></div>
                <p id="locationStatusMasuk" class="mt-2 text-sm text-gray-600 dark:text-gray-300"></p>
            </div>

            {{-- Hidden Input --}}
            <input type="hidden" name="storeVisitId" id="storeVisitIdMasuk" value="{{ $storeVisit->id }}">
            <input type="hidden" name="type" id="typeMasuk" value="checkIn">
            <input type="hidden" name="latitude" id="latitudeMasuk">
            <input type="hidden" name="longitude" id="longitudeMasuk">
            <input type="hidden" name="location_hash" id="locationHashMasuk">
            <input type="hidden" name="accuracy" id="accuracyMasuk">

            <button type="submit" id="submitMasukBtn"
                class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                Absen Masuk
            </button>
        </form>

        <!-- Form Absen Pulang -->
        <form method="POST" action="{{ route('attendances.store') }}" enctype="multipart/form-data"
            id="absenPulangForm" class="tab-content">
            @csrf

            <div class="mb-6">
                <label for="nominal_invoice"
                    class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Nominal Invoice</label>
                <input type="number" name="nominal_invoice" id="nominal_invoice"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Masukkan nominal invoice" required>
            </div>

            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Foto Bukti
                    Invoice</label>
                <input type="file" name="bukti_invoice" id="buktiInvoice" accept="image/*" capture="environment"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                    file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Foto bukti invoice penjualan</p>
            </div>

            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Foto Selfie
                    Pulang</label>
                <div class="camera-preview" id="cameraPreviewPulang">
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
                <input type="file" name="foto_pulang" id="fotoPulangInput" accept="image/*" capture="user"
                    class="hidden">
                <input type="hidden" name="foto_pulang_data" id="fotoPulangData">
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Foto wajah Anda saat absen pulang</p>
            </div>

            {{-- Map Preview --}}
            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi Saat Ini</label>
                <div id="mapPreviewPulang" class="w-full h-48 sm:h-64 rounded-lg border border-gray-300"></div>
                <p id="locationStatusPulang" class="mt-2 text-sm text-gray-600 dark:text-gray-300"></p>
            </div>

            {{-- Hidden Input --}}
            <input type="hidden" name="latitude" id="latitudePulang">
            <input type="hidden" name="longitude" id="longitudePulang">
            <input type="hidden" name="location_hash" id="locationHashPulang">
            <input type="hidden" name="accuracy" id="accuracyPulang">

            <button type="submit" id="submitPulangBtn"
                class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                Absen Pulang
            </button>
        </form>

        <!-- Panduan GPS -->
        <div id="gpsInstructions"
            class="mt-6 p-3 bg-blue-50 text-blue-800 rounded-lg text-sm dark:bg-blue-900 dark:text-blue-200">
            <strong>Tips untuk presisi GPS lebih baik:</strong>
            <ul class="list-disc pl-5 mt-1 space-y-1">
                <li>Pastikan GPS perangkat Anda aktif</li>
                <li>Buka area terbuka, hindari gedung tinggi</li>
                <li>Hindari penggunaan VPN atau proxy</li>
                <li>Tunggu beberapa detik sampai akurasi meningkat</li>
                <li>Jika menggunakan WiFi, pastikan terhubung ke jaringan</li>
            </ul>
            <div id="accuracyInfo" class="mt-2 font-medium"></div>
        </div>
    </div>

    <!-- Tambahkan JS Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Di bagian atas script, tambahkan flag development
        const isDevelopment = {{ app()->environment('local') ? 'true' : 'false' }};

        // Fungsi getPositionSafe yang dimodifikasi
        function getPositionSafe(type = 'Masuk') {
            return new Promise((resolve, reject) => {
                // Jika development, gunakan lokasi default atau dari sensor dev tools
                if (isDevelopment) {
                    console.warn(`[${type}] Development mode - menggunakan lokasi default atau simulasi`);

                    // Coba dapatkan lokasi dari dev tools sensor jika ada
                    if (navigator.geolocation && navigator.geolocation.getCurrentPosition) {
                        navigator.geolocation.getCurrentPosition(
                            position => resolve(position),
                            error => {
                                // Fallback ke lokasi default jika gagal
                                const fallbackPosition = {
                                    coords: {
                                        latitude: -6.200000, // Jakarta
                                        longitude: 106.816666,
                                        accuracy: 15, // Akurasi tinggi untuk development
                                        timestamp: Date.now()
                                    }
                                };
                                resolve(fallbackPosition);
                            }, {
                                enableHighAccuracy: true,
                                timeout: 1000,
                                maximumAge: 0
                            }
                        );
                    } else {
                        // Jika tidak ada geolocation API sama sekali
                        const fallbackPosition = {
                            coords: {
                                latitude: -6.200000,
                                longitude: 106.816666,
                                accuracy: 15,
                                timestamp: Date.now()
                            }
                        };
                        resolve(fallbackPosition);
                    }
                    return;
                }

                // Kode production asli...
                const options = {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0,
                };

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const accuracy = position.coords.accuracy ?? 999;
                        if (accuracy <= 30) {
                            console.log(`[${type}] Akurasi bagus (${accuracy}m), diterima.`);
                            return resolve(position);
                        }
                        console.log(accuracy)

                        const watchId = navigator.geolocation.watchPosition(
                            (newPos) => {
                                if (newPos.coords.accuracy <= 30) {
                                    navigator.geolocation.clearWatch(watchId);
                                    console.log(
                                        `[${type}] Watch akurasi bagus (${newPos.coords.accuracy}m).`);
                                    return resolve(newPos);
                                }
                            },
                            (err) => {
                                navigator.geolocation.clearWatch(watchId);
                                console.error(`[${type}] Watch error:`, err.message);
                                return reject(err);
                            },
                            options
                        );

                        setTimeout(() => {
                            navigator.geolocation.clearWatch(watchId);
                            console.warn(`[${type}] Timeout mencari lokasi akurat`);
                            return reject(new Error('Tidak mendapat lokasi akurat.'));
                        }, 15000);
                    },
                    (error) => {
                        console.error(`[${type}] Gagal getCurrentPosition:`, error.message);
                        return reject(error);
                    },
                    options
                );
            });
        }
    </script>


    @php
        $env = app()->environment();

    @endphp

    {{-- @if ($env === 'local')
        @dd('local');
    @endif --}}
    <script>
        // Konfigurasi
        const tokoLat = {{ $storeVisit->store->latitude }};
        const tokoLng = {{ $storeVisit->store->longitude }};
        const maxRadiusMeter = 100;

        // Variabel global
        let mapMasuk, mapPulang;
        let userMarkerMasuk, userMarkerPulang;
        let watchIdMasuk, watchIdPulang;
        let bestPositionMasuk = null,
            bestPositionPulang = null;

        // Fungsi umum
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

        function hashLocation(lat, lng) {
            const salt = "{{ config('app.key') }}";
            const timestamp = Math.floor(Date.now() / (1000 * 60));
            return btoa(`${lat}:${lng}:${salt}:${timestamp}`);
        }

        function initMap(elementId, lat, lng) {
            const map = L.map(elementId).setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Tambahkan marker toko
            L.marker([tokoLat, tokoLng]).addTo(map)
                .bindPopup('Lokasi Toko')
                .openPopup();

            // Tambahkan lingkaran radius
            L.circle([tokoLat, tokoLng], {
                radius: maxRadiusMeter,
                color: 'blue',
                fillColor: '#cce5ff',
                fillOpacity: 0.2
            }).addTo(map);

            return map;
        }

        function updateUI(position, type) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const accuracy = position.coords.accuracy;

            // Update hidden inputs
            document.getElementById(`latitude${type}`).value = lat;
            document.getElementById(`longitude${type}`).value = lng;
            document.getElementById(`locationHash${type}`).value = hashLocation(lat, lng);
            document.getElementById(`accuracy${type}`).value = accuracy;

            // Update marker
            console.log(`[${type}] Lokasi: ${lat}, ${lng} (Akurasi: ${accuracy}m)`);
            if (type === 'Masuk') {
                if (!userMarkerMasuk) {
                    userMarkerMasuk = L.marker([lat, lng]).addTo(mapMasuk)
                        .bindPopup('Lokasi Anda')
                        .openPopup();
                } else {
                    userMarkerMasuk.setLatLng([lat, lng]);
                }
            } else {
                if (!userMarkerPulang) {
                    userMarkerPulang = L.marker([lat, lng]).addTo(mapPulang)
                        .bindPopup('Lokasi Anda')
                        .openPopup();
                } else {
                    userMarkerPulang.setLatLng([lat, lng]);
                }
            }

            // Hitung jarak dan update status
            const distance = getDistance(lat, lng, tokoLat, tokoLng);
            const locationStatus = document.getElementById(`locationStatus${type}`);
            const submitBtn = document.getElementById(`submit${type}Btn`);
            // Update status
            if (accuracy > 5000) {
                locationStatus.textContent =
                    `Akurasi GPS rendah (±${Math.round(accuracy)}m). Mohon tunggu atau cari lokasi dengan sinyal lebih baik.`;
                locationStatus.className = 'mt-2 text-sm text-red-600 dark:text-red-300';
                submitBtn.disabled = true;
            } else if (distance <= maxRadiusMeter) {
                locationStatus.textContent = `Anda berada dalam radius toko (${Math.round(distance)}m)`;
                locationStatus.className = 'mt-2 text-sm text-green-600 dark:text-green-300';
                submitBtn.disabled = false;
            } else {
                locationStatus.textContent =
                    `Anda berada di luar radius toko (${Math.round(distance)}m). Absen tidak dapat dilakukan.`;
                locationStatus.className = 'mt-2 text-sm text-red-600 dark:text-red-300';
                submitBtn.disabled = true;
            }
        }


        function handleCamera(previewId, inputId, dataId) {
            const preview = document.getElementById(previewId);
            const input = document.getElementById(inputId);
            const dataInput = document.getElementById(dataId);
            const cameraIcon = preview.querySelector('.camera-icon');

            input.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        preview.innerHTML = `
                            <img src="${event.target.result}" class="captured-image" alt="Captured Photo">
                            <div class="camera-button" onclick="document.getElementById('${inputId}').click()">
                                <div class="camera-button-inner"></div>
                            </div>
                        `;
                        dataInput.value = event.target.result;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Trigger click on camera button
            preview.addEventListener('click', function(e) {
                if (e.target.closest('.camera-button')) {
                    input.click();
                }
            });
        }

        // Tab Navigation
        document.getElementById('tabMasuk').addEventListener('click', function() {
            document.getElementById('tabMasuk').classList.add('active');
            document.getElementById('tabPulang').classList.remove('active');
            document.getElementById('absenMasukForm').classList.add('active');
            document.getElementById('absenPulangForm').classList.remove('active');
        });

        document.getElementById('tabPulang').addEventListener('click', function() {
            document.getElementById('tabPulang').classList.add('active');
            document.getElementById('tabMasuk').classList.remove('active');
            document.getElementById('absenPulangForm').classList.add('active');
            document.getElementById('absenMasukForm').classList.remove('active');
        });

        // Inisialisasi peta
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi kamera
            handleCamera('cameraPreviewMasuk', 'fotoMasukInput', 'fotoMasukData');
            handleCamera('cameraPreviewPulang', 'fotoPulangInput', 'fotoPulangData');

            // Inisialisasi peta
            mapMasuk = initMap('mapPreviewMasuk', tokoLat, tokoLng);
            mapPulang = initMap('mapPreviewPulang', tokoLat, tokoLng);

            // Mulai tracking lokasi untuk masing-masing tab
            if ("geolocation" in navigator) {
                // Untuk tab masuk
                getPositionSafe('Masuk')
                    .then(position => {
                        bestPositionMasuk = position;
                        updateUI(position, 'Masuk');

                        watchIdMasuk = navigator.geolocation.watchPosition(
                            newPosition => {
                                if (!bestPositionMasuk || newPosition.coords.accuracy < bestPositionMasuk
                                    .coords.accuracy) {
                                    bestPositionMasuk = newPosition;
                                    updateUI(newPosition, 'Masuk');
                                }
                            },
                            error => console.error('Error watching position:', error), {
                                enableHighAccuracy: true,
                                maximumAge: 10000
                            }
                        );
                    })
                    .catch(error => {
                        console.error('Error getting location:', error);
                        document.getElementById('locationStatusMasuk').textContent =
                            'Gagal mendapatkan lokasi. Pastikan GPS aktif dan izin lokasi diberikan.';
                    });


                // Untuk tab pulang
                getPositionSafe('Pulang')
                    .then(position => {
                        bestPositionPulang = position;
                        updateUI(position, 'Pulang');

                        watchIdPulang = navigator.geolocation.watchPosition(
                            newPosition => {
                                if (!bestPositionPulang || newPosition.coords.accuracy < bestPositionPulang
                                    .coords.accuracy) {
                                    bestPositionPulang = newPosition;
                                    updateUI(newPosition, 'Pulang');
                                }
                            },
                            error => console.error('Error watching position:', error), {
                                enableHighAccuracy: true,
                                maximumAge: 10000
                            }
                        );
                    })
                    .catch(error => {
                        console.error('Error getting location:', error);
                        document.getElementById('locationStatusPulang').textContent =
                            'Gagal mendapatkan lokasi. Pastikan GPS aktif dan izin lokasi diberikan.';
                    });

            } else {
                document.getElementById('locationStatusMasuk').textContent = 'Browser tidak mendukung geolocation.';
                document.getElementById('locationStatusPulang').textContent =
                    'Browser tidak mendukung geolocation.';
            }
        });

        // Validasi form
        document.getElementById('absenMasukForm').addEventListener('submit', function(e) {
            if (!bestPositionMasuk) {
                e.preventDefault();
                alert('Sistem masih mendeteksi lokasi Anda. Mohon tunggu sebentar.');
                return;
            }

            const isValid = validateAbsen({
                type: 'Masuk',
                bestPosition: bestPositionMasuk,
                fotoDataId: 'fotoMasukData'
            });

            if (!isValid) {
                e.preventDefault();
            }

            if (watchIdMasuk) {
                navigator.geolocation.clearWatch(watchIdMasuk);
            }
        });


        document.getElementById('absenPulangForm').addEventListener('submit', function(e) {
            if (!bestPositionPulang) {
                e.preventDefault();
                alert('Sistem masih mendeteksi lokasi Anda. Mohon tunggu sebentar.');
                return;
            }

            const isValid = validateAbsen({
                type: 'Pulang',
                bestPosition: bestPositionPulang,
                fotoDataId: 'fotoPulangData',
                invoiceId: 'nominal_invoice',
                invoiceProofId: 'buktiInvoice'
            });

            if (!isValid) {
                e.preventDefault();
            }

            if (watchIdPulang) {
                navigator.geolocation.clearWatch(watchIdPulang);
            }
        });


        async function addWatermarkToImage(imageData, lat, lng, accuracy) {
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');

                    ctx.drawImage(img, 0, 0);

                    ctx.font = '16px Arial';
                    ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
                    ctx.strokeStyle = 'rgba(0, 0, 0, 0.7)';
                    ctx.lineWidth = 2;

                    const text =
                        `Lokasi: ${lat.toFixed(6)}, ${lng.toFixed(6)} | Akurasi: ${Math.round(accuracy)}m`;
                    const textWidth = ctx.measureText(text).width;
                    const x = img.width - textWidth - 20;
                    const y = img.height - 20;

                    ctx.strokeText(text, x, y);
                    ctx.fillText(text, x, y);

                    resolve(canvas.toDataURL('image/jpeg', 0.9));
                };
                img.src = imageData;
            });
        }


        function validateAbsen({
            type,
            bestPosition,
            fotoDataId,
            invoiceId = null,
            invoiceProofId = null
        }) {
            const latitude = parseFloat(document.getElementById(`latitude${type}`).value);
            const longitude = parseFloat(document.getElementById(`longitude${type}`).value);
            const submittedHash = document.getElementById(`locationHash${type}`).value;
            const accuracy = parseFloat(document.getElementById(`accuracy${type}`).value);
            const foto = document.getElementById(fotoDataId)?.value;

            // Jika production, lakukan validasi ketat
            if (isProduction) {
                const expectedHash = hashLocation(latitude, longitude);
                if (submittedHash !== expectedHash) {
                    alert('Data lokasi tidak valid. Silahkan refresh halaman dan coba lagi.');
                    return false;
                }

                if (accuracy > 50) {
                    alert('Akurasi GPS terlalu rendah. Mohon cari lokasi dengan sinyal lebih baik.');
                    return false;
                }
            }

            if (!foto) {
                alert(`Anda harus mengambil foto selfie untuk absen ${type.toLowerCase()}.`);
                return false;
            }

            // Khusus pulang: validasi invoice
            if (type === 'Pulang') {
                const nominal = document.getElementById(invoiceId)?.value;
                const bukti = document.getElementById(invoiceProofId)?.files[0];

                if (!nominal || isNaN(nominal) || nominal <= 0) {
                    alert('Masukkan nominal invoice yang valid.');
                    return false;
                }

                if (!bukti) {
                    alert('Anda harus mengupload bukti invoice.');
                    return false;
                }
            }

            return true;
        }
    </script>
</x-layouts.app>
