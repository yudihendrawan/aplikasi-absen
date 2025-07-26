<x-layouts.app :title="__('Absen Hari Ini')">
    <!-- Tambahkan CSS Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
        <h1 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Absen Kunjungan</h1>

        <form method="POST" action="{{ route('attendances.store') }}" enctype="multipart/form-data" id="absenForm">
            @csrf

            <!-- Tab Navigation -->
            <div class="flex border-b mb-6">
                <button type="button" id="tabMasuk" class="tab-button active px-4 py-2">Absen Masuk</button>
                <button type="button" id="tabPulang" class="tab-button px-4 py-2">Absen Pulang</button>
            </div>

            <!-- Form Absen Masuk -->
            <div id="formMasuk" class="tab-content active">
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Foto Selfie
                        Masuk</label>
                    <div class="camera-preview" id="cameraPreview">
                        <div class="camera-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <input type="file" id="fotoInput" accept="image/*" capture="user" class="hidden">
                    </div>
                    <input type="hidden" name="foto_masuk" id="fotoData">
                </div>

                <!-- Untuk Absen Pulang -->
                <div id="formPulangExtra" class="hidden">
                    <div class="mb-4">
                        <label for="nominal_invoice"
                            class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Nominal
                            Invoice</label>
                        <input type="number" name="nominal_invoice" id="nominal_invoice"
                            class="w-full border-gray-300 rounded shadow-sm sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Foto Bukti
                            Invoice</label>
                        <input type="file" name="bukti_invoice" id="buktiInvoice" accept="image/*"
                            capture="environment" class="w-full">
                    </div>
                </div>
            </div>

            <!-- Map Preview -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi Saat Ini</label>
                <div id="mapPreview" class="w-full h-64 rounded border"></div>
                <p id="locationStatus" class="text-sm mt-2 text-gray-600 dark:text-gray-300"></p>
                <p id="developerModeWarning" class="text-sm mt-2 text-red-600 dark:text-red-300 hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Perangkat dalam mode developer atau menggunakan Fake GPS. Absen tidak dapat dilakukan.
                </p>
            </div>

            <!-- Hidden Input -->
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="location_hash" id="location_hash">
            <input type="hidden" name="accuracy" id="accuracy">
            <input type="hidden" name="type" id="absenType" value="masuk">

            <button type="submit" id="submitBtn"
                class="w-full py-2 px-4 bg-indigo-600 text-white rounded disabled:bg-gray-400" disabled>
                Absen Sekarang
            </button>
        </form>
    </div>

    <!-- Tambahkan JS Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const tokoLat = {{ $storeVisit->store->latitude }};
        const tokoLng = {{ $storeVisit->store->longitude }};
        const maxRadiusMeter = 100;
        let map;
        let isDeveloperMode = false;

        // Fungsi untuk menghitung jarak
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

        // Fungsi untuk membuat hash lokasi
        function hashLocation(lat, lng) {
            const salt = "{{ config('app.key') }}";
            const timestamp = Math.floor(Date.now() / (1000 * 60));
            return btoa(`${lat}:${lng}:${salt}:${timestamp}`);
        }

        // Fungsi untuk mendeteksi developer mode
        async function detectDeveloperMode() {
            return new Promise((resolve) => {
                // Deteksi untuk Android
                if (navigator.userAgent.match(/Android/i)) {
                    const start = performance.now();
                    for (let i = 0; i < 1000000; i++) {}
                    const duration = performance.now() - start;

                    if (duration < 10) {
                        resolve(true);
                        return;
                    }

                    try {
                        if (typeof navigator.geolocation.getMockLocations !== 'undefined' ||
                            navigator.geolocation.__proto__.getMockLocations) {
                            resolve(true);
                            return;
                        }

                        if (navigator.userAgent.toLowerCase().indexOf('mock') !== -1 ||
                            navigator.userAgent.toLowerCase().indexOf('debug') !== -1) {
                            resolve(true);
                            return;
                        }
                    } catch (e) {
                        console.log("Mock location check failed:", e);
                    }
                }

                resolve(false);
            });
        }

        // Fungsi untuk memeriksa mock location
        function checkMockLocation(position) {
            if (position.coords.accuracy > 5000) {
                return true;
            }

            if (position.coords.speed !== null && position.coords.speed > 100) {
                return true;
            }

            if (window.lastPosition) {
                const distance = getDistance(
                    window.lastPosition.coords.latitude,
                    window.lastPosition.coords.longitude,
                    position.coords.latitude,
                    position.coords.longitude
                );
                const timeDiff = (position.timestamp - window.lastPosition.timestamp) / 1000;

                if (timeDiff > 0 && distance / timeDiff > 300) {
                    return true;
                }
            }
            window.lastPosition = position;

            return false;
        }

        // Fungsi untuk menambahkan watermark
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

        // Inisialisasi peta
        function initMap(userLat, userLng) {
            map = L.map('mapPreview').setView([userLat, userLng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map);

            L.marker([userLat, userLng]).addTo(map).bindPopup('Lokasi Anda').openPopup();
            L.marker([tokoLat, tokoLng]).addTo(map).bindPopup('Lokasi Toko').openPopup();

            L.circle([tokoLat, tokoLng], {
                radius: maxRadiusMeter,
                color: 'blue',
                fillColor: '#cce5ff',
                fillOpacity: 0.2
            }).addTo(map);
        }

        // Update UI
        async function updateUI(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            const accuracy = position.coords.accuracy;

            document.getElementById('latitude').value = userLat;
            document.getElementById('longitude').value = userLng;
            document.getElementById('location_hash').value = hashLocation(userLat, userLng);
            document.getElementById('accuracy').value = accuracy;

            const distance = getDistance(userLat, userLng, tokoLat, tokoLng);
            const locationStatus = document.getElementById('locationStatus');
            const submitBtn = document.getElementById('submitBtn');
            const warningElement = document.getElementById('developerModeWarning');

            // Cek mock location
            const isMock = checkMockLocation(position);
            if (isMock || isDeveloperMode) {
                warningElement.classList.remove('hidden');
                submitBtn.disabled = true;
                locationStatus.textContent = 'Lokasi tidak valid terdeteksi.';
                locationStatus.className = 'text-sm mt-2 text-red-600 dark:text-red-300';
                return;
            } else {
                warningElement.classList.add('hidden');
            }

            if (accuracy > 50) {
                locationStatus.textContent = `Akurasi GPS rendah (${Math.round(accuracy)}m). Mohon tunggu.`;
                locationStatus.className = 'text-sm mt-2 text-yellow-600 dark:text-yellow-300';
                submitBtn.disabled = true;
            } else if (distance <= maxRadiusMeter) {
                locationStatus.textContent = `Dalam radius toko (${Math.round(distance)}m)`;
                locationStatus.className = 'text-sm mt-2 text-green-600 dark:text-green-300';
                submitBtn.disabled = false;
            } else {
                locationStatus.textContent = `Di luar radius toko (${Math.round(distance)}m)`;
                locationStatus.className = 'text-sm mt-2 text-red-600 dark:text-red-300';
                submitBtn.disabled = true;
            }
        }

        // Handle camera capture
        document.getElementById('fotoInput').addEventListener('change', async function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = async function(event) {
                    const lat = parseFloat(document.getElementById('latitude').value);
                    const lng = parseFloat(document.getElementById('longitude').value);
                    const accuracy = parseFloat(document.getElementById('accuracy').value);

                    // Tambahkan watermark
                    const watermarkedImage = await addWatermarkToImage(event.target.result, lat, lng,
                        accuracy);
                    document.getElementById('fotoData').value = watermarkedImage;

                    // Tampilkan preview
                    const preview = document.getElementById('cameraPreview');
                    preview.innerHTML = `
                        <img src="${watermarkedImage}" class="w-full h-full object-cover" alt="Preview">
                        <input type="file" id="fotoInput" accept="image/*" capture="user" class="hidden">
                    `;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Tab navigation
        document.getElementById('tabMasuk').addEventListener('click', function() {
            document.getElementById('tabMasuk').classList.add('active');
            document.getElementById('tabPulang').classList.remove('active');
            document.getElementById('formPulangExtra').classList.add('hidden');
            document.getElementById('absenType').value = 'masuk';
        });

        document.getElementById('tabPulang').addEventListener('click', function() {
            document.getElementById('tabPulang').classList.add('active');
            document.getElementById('tabMasuk').classList.remove('active');
            document.getElementById('formPulangExtra').classList.remove('hidden');
            document.getElementById('absenType').value = 'pulang';
        });

        // Main execution
        (async function() {
            isDeveloperMode = await detectDeveloperMode();

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(
                    async function(position) {
                            const userLat = position.coords.latitude;
                            const userLng = position.coords.longitude;

                            initMap(userLat, userLng);
                            await updateUI(position);

                            // Watch for position changes
                            navigator.geolocation.watchPosition(
                                async (pos) => {
                                        await updateUI(pos);
                                    },
                                    (err) => console.error('Error:', err), {
                                        enableHighAccuracy: true,
                                        maximumAge: 10000
                                    }
                            );
                        },
                        function(error) {
                            console.error('Error getting location:', error);
                            alert('Gagal mendapatkan lokasi. Aktifkan GPS dan izinkan akses lokasi.');
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                );
            } else {
                alert('Browser tidak mendukung geolocation.');
            }
        })();
    </script>
</x-layouts.app>
