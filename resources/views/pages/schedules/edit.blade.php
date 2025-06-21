<x-layouts.app>
    <x-slot name="title">{{ __('Edit Jadwal Kunjungan') }}</x-slot>

    <x-ui.breadcrumb :items="[['label' => 'Jadwal Sales', 'url' => route('schedules.index')], ['label' => 'Edit Jadwal']]" />

    <div
        class="mb-6 p-5 bg-yellow-50 dark:bg-gray-700 border border-yellow-200 dark:border-gray-600 rounded-lg shadow-sm">
        <div class="flex items-center gap-4">
            <div>
                <h2 class="text-lg font-semibold  dark:text-white">Edit Jadwal Kunjungan</h2>
                <p class="text-sm  dark:text-gray-300">
                    Perbarui jadwal kunjungan sales ke toko dan estimasi tagihan. Jadwal ini digunakan sebagai acuan
                    absensi dan invoice.
                </p>
            </div>
        </div>
    </div>

    <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
        <form action="{{ route('schedules.update', $schedule->id) }}" method="POST"
            class="p-4 grid md:grid-cols-2 grid-cols-1 gap-4">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="user_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sales <span
                        class="text-red-500">*</span></label>
                <select name="user_id" id="user_id" class="tom-select w-full" required>
                    <option value="">Pilih Sales</option>
                    @foreach ($sales as $user)
                        <option value="{{ $user->id }}"
                            {{ old('user_id', $schedule->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="visit_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                    Kunjungan <span class="text-red-500">*</span></label>
                <input type="date" id="visit_date" name="visit_date" required
                    class="form-input block w-full rounded-lg border text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    value="{{ old('visit_date', $schedule->visit_date) }}">
                @error('visit_date')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4 col-span-2">
                <label for="time_tolerance"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Toleransi Keterlambatan (menit)
                    <span class="text-red-500">*</span></label>
                <input type="number" id="time_tolerance" name="time_tolerance" class="form-input w-32 rounded-lg"
                    placeholder="Contoh: 15" min="0"
                    value="{{ old('time_tolerance', $schedule->time_tolerance) }}">
                @error('time_tolerance')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <hr class="my-4 border-t border-gray-200 dark:border-gray-600 col-span-2">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300  mb-2">Toko & Estimasi Tagihan <span
                    class="text-red-500">*</span></h3>

            <div class="mb-4 col-span-2">
                <div id="store-list">
                    @php
                        $storeData =
                            old('stores') ??
                            $schedule->storeVisits
                                ->map(function ($visit) {
                                    return [
                                        'store_id' => $visit->store_id,
                                        'checkin_time' => $visit->checkin_time,
                                        'checkout_time' => $visit->checkout_time,
                                        'expected_invoice_amount' => $visit->expected_invoice_amount,
                                    ];
                                })
                                ->toArray();
                    @endphp

                    @foreach ($storeData as $index => $store)
                        <div class="store-row flex gap-2 mb-2">
                            <select name="stores[{{ $index }}][store_id]" class="tom-select w-full" required>
                                <option value="">Pilih Toko</option>
                                @foreach ($stores as $storeOption)
                                    <option value="{{ $storeOption->id }}"
                                        {{ $storeOption->id == $store['store_id'] ? 'selected' : '' }}>
                                        {{ $storeOption->name }}</option>
                                @endforeach
                            </select>
                            <div class="relative w-32">
                                <input type="time" name="stores[{{ $index }}][checkin_time]"
                                    class="form-input rounded-lg w-32 pl-10"
                                    value="{{ \Carbon\Carbon::parse($store['checkin_time'])->format('H:i') }}"
                                    required>
                                <div
                                    class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="relative w-32">

                                <input type="time" name="stores[{{ $index }}][checkout_time]"
                                    class="form-input rounded-lg w-32 pl-10"
                                    value="{{ \Carbon\Carbon::parse($store['checkout_time'])->format('H:i') }}"
                                    required>
                                <div
                                    class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <input type="text" name="stores[{{ $index }}][expected_invoice_amount]"
                                class="form-input currency-format w-1/3 rounded-lg"
                                value="{{ number_format($store['expected_invoice_amount'] ?? 0, 0, ',', '.') }}"
                                data-hidden-input="amount-{{ $index }}">
                            <input type="hidden" name="stores[{{ $index }}][expected_invoice_amount]"
                                id="amount-{{ $index }}" value="{{ $store['expected_invoice_amount'] }}">
                            <button type="button" onclick="removeStoreRow(this)" class="text-red-500 cursor-pointer"
                                title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2h.293l.347 9.293A2 2 0 006.635 17h6.73a2 2 0 001.995-1.707L15.707 6H16a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM8 8a1 1 0 112 0v5a1 1 0 11-2 0V8zm4 0a1 1 0 112 0v5a1 1 0 11-2 0V8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
                <button type="button" onclick="addStoreRow()"
                    class="mt-2 bg-emerald-500 text-sm text-white px-4 py-2 rounded-lg">+ Tambah Toko</button>
            </div>

            <div class="flex justify-end mt-4 col-span-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Perbarui
                    Jadwal</button>
                <a href="{{ route('schedules.index') }}"
                    class="ml-2 text-gray-700 bg-gray-200 px-5 py-2.5 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">Batal</a>
            </div>
        </form>
    </section>


    <script>
        let storeIndex = {{ $schedule->storeVisits->count() }};

        function formatRupiah(angka, prefix = 'Rp ') {
            if (!angka) return '';
            angka = angka.toString().replace(/[^,\d]/g, '');

            const split = angka.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                const separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix + rupiah;
        }

        function setupCurrencyFormat(input) {
            input.addEventListener('input', function() {
                const raw = this.value.replace(/[^0-9]/g, '');
                const formatted = formatRupiah(raw);
                this.value = formatted;

                const hiddenId = this.dataset.hiddenInput;
                if (hiddenId) {
                    const hiddenInput = document.getElementById(hiddenId);
                    if (hiddenInput) {
                        hiddenInput.value = raw;
                    }
                }
            });

            // Trigger pertama kali jika sudah ada nilai awal
            input.dispatchEvent(new Event('input'));
        }

        function setupTimeValidation(row) {
            const checkinInput = row.querySelector('input[name*="[checkin_time]"]');
            const checkoutInput = row.querySelector('input[name*="[checkout_time]"]');

            if (!checkinInput || !checkoutInput) return;

            checkinInput.addEventListener('change', function() {
                checkoutInput.min = checkinInput.value;
                if (checkoutInput.value && checkoutInput.value < checkinInput.value) {
                    checkoutInput.value = "";
                }
            });

            checkoutInput.addEventListener('change', function() {
                if (checkinInput.value && checkoutInput.value < checkinInput.value) {
                    alert("Waktu checkout tidak boleh lebih awal dari waktu check-in.");
                    checkoutInput.value = "";
                }
            });
        }

        function addStoreRow() {
            const container = document.getElementById('store-list');
            const row = document.createElement('div');
            row.className = 'store-row flex gap-2 mb-2';

            row.innerHTML = `
            <select name="stores[${storeIndex}][store_id]" class="tom-select w-full" required>
                <option value="">Pilih Toko</option>
                @foreach ($stores as $store)
                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                @endforeach
            </select>
            <div class="relative w-32">
                <input type="time" name="stores[${storeIndex}][checkin_time]" class="form-input rounded-lg w-32 pl-10" required>
                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="relative w-32">
                <input type="time" name="stores[${storeIndex}][checkout_time]" class="form-input rounded-lg w-32 pl-10" required>
                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <input type="text" name="stores[${storeIndex}][expected_invoice_amount]" 
                class="form-input currency-format block w-1/3 rounded-lg border text-sm dark:bg-gray-700 dark:text-white"
                data-hidden-input="amount-${storeIndex}" placeholder="Estimasi tagihan (Opsional)">
            <input type="hidden" name="stores[${storeIndex}][expected_invoice_amount]" id="amount-${storeIndex}">
            <button type="button" onclick="removeStoreRow(this)" class="text-red-500 cursor-pointer" title="Hapus">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2h.293l.347 
                    9.293A2 2 0 006.635 17h6.73a2 2 0 001.995-1.707L15.707 
                    6H16a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM8 
                    8a1 1 0 112 0v5a1 1 0 11-2 0V8zm4 0a1 1 0 
                    112 0v5a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                </svg>
            </button>
        `;

            container.appendChild(row);

            new TomSelect(row.querySelector('.tom-select'));
            setupTimeValidation(row);
            setupCurrencyFormat(row.querySelector('.currency-format'));

            storeIndex++;
        }

        function removeStoreRow(button) {
            button.parentElement.remove();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi untuk row awal
            document.querySelectorAll('.store-row').forEach(row => {
                setupCurrencyFormat(row.querySelector('.currency-format'));
                setupTimeValidation(row);
            });

            // Datepicker visit_date
            const visitInput = document.getElementById('visit_date');
            if (visitInput) {
                flatpickr(visitInput, {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "F j, Y",
                    altInputClass: 'form-input block w-full rounded-lg border text-sm placeholder-gray-400',
                    onReady(_, __, instance) {
                        instance.altInput.placeholder = "Pilih tanggal";
                    }
                });
            }

            // Error handling Laravel
            if (window.formErrors) {
                for (const [field, messages] of Object.entries(window.formErrors)) {
                    const fieldName = field.replace(/\.(\d+)\./g, '[$1][').replace(/\./g, ']') + ']';
                    const input = document.querySelector(`[name="${fieldName}"]`);
                    if (input) {
                        input.classList.add('border-red-500');
                        const error = document.createElement('p');
                        error.className = 'mt-1 text-sm text-red-600';
                        error.innerText = messages[0];
                        input.insertAdjacentElement('afterend', error);
                    }
                }
            }
        });
    </script>

</x-layouts.app>
