<x-layouts.app>
    <x-slot name="title">
        {{ __('Create Leave') }}
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            {{ __('Create Leave') }}
        </h1>
    </x-slot>

    <div class="mt-6 space-x-2   items-center justify-center">
        <div class=" ">
            <form action="{{ route('leaves.store') }}" method="POST" class="bg-white grid md:grid-cols-2 grid-cols-1 gap-4 dark:bg-gray-800 shadow-md rounded px-8 mx-8 pt-6 pb-8 mb-4">
                @csrf
                <div class="mb-4 md:col-span-2">
                    <label for="user_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('User') }}</label>
                    <select id="user_id" name="user_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('user_id') border-red-500 @enderror">
                        <option value="" disabled {{ old('user_id') ? '' : 'selected' }}>{{ __('Select user') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>


                <div class="mb-4 md:col-span-2">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Leave Name') }}</label>
                    <input type="text" id="name" name="name" required class="form-input block w-full rounded-lg border border-gray-300 bg-gray-50 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror" placeholder="{{ __('Enter leave name') }}" value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="start_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Start Date') }}</label>
                    <input type="date" id="start_date" name="start_date" required class="form-input block w-full rounded-lg border border-gray-300 bg-gray-50 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('start_date') border-red-500 @enderror" value="{{ old('start_date') }}">
                    @error('start_date')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="end_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('End Date') }}</label>
                    <input type="date" id="end_date" name="end_date" required class="form-input block w-full rounded-lg border border-gray-300 bg-gray-50 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('end_date') border-red-500 @enderror" value="{{ old('end_date') }}">
                    @error('end_date')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="reason" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Reason') }}</label>
                    <textarea id="reason" name="reason" rows="3" required class="form-textarea block w-full rounded-lg border border-gray-300 bg-gray-50 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('reason') border-red-500 @enderror" placeholder="{{ __('Enter reason for leave') }}">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Description') }}</label>
                    <textarea id="description" name="description" rows="3" required class="form-textarea block w-full rounded-lg border border-gray-300 bg-gray-50 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('description') border-red-500 @enderror" placeholder="{{ __('Enter description for leave') }}">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div></div>
                <div class="flex items-center justify-end">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{ __('Submit') }}</button>
                    <button type="button" onclick="window.location='{{ route('leaves.index') }}'" class="ml-2 text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800">{{ __('Cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
    <x-slot name="scripts">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownButton = document.getElementById('dropdown-button');
            const dropdownMenu = document.getElementById('dropdown-menu');
            const searchInput = document.getElementById('search-input');
            const dropdownItems = document.getElementById('dropdown-items');
            const hiddenInput = document.getElementById('user_id');
            const selectedUserLabel = document.getElementById('selected-user-label');

            function toggleDropdown(force = null) {
                let shouldShow;
                if (force === null) {
                    shouldShow = dropdownMenu.classList.contains('hidden');
                } else {
                    shouldShow = !!force;
                }
                dropdownMenu.classList.toggle('hidden', !shouldShow);
                if (shouldShow) {
                    searchInput.value = '';
                    filterItems('');
                    setTimeout(() => searchInput.focus(), 100);
                }
            }

            function filterItems(search) {
                const items = dropdownItems.querySelectorAll('a');
                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(search.toLowerCase()) ? 'block' : 'none';
                });
            }

            dropdownButton.addEventListener('click', (e) => {
                e.preventDefault();
                toggleDropdown();
            });

            searchInput.addEventListener('input', (e) => {
                filterItems(e.target.value);
            });

            dropdownItems.querySelectorAll('a').forEach(item => {
                item.addEventListener('mousedown', function (e) {
                    // Use mousedown to prevent blur before click
                    e.preventDefault();
                    const id = this.dataset.id;
                    const label = this.dataset.label;
                    hiddenInput.value = id;
                    selectedUserLabel.textContent = label;
                    toggleDropdown(false);
                });
            });

            // Close dropdown if clicked outside
            document.addEventListener('mousedown', function (event) {
                if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    toggleDropdown(false);
                }
            });

            // Set selected label if old value exists
            const oldUserId = hiddenInput.value;
            if (oldUserId) {
                const selected = dropdownItems.querySelector('a[data-id="' + oldUserId + '"]');
                if (selected) {
                    selectedUserLabel.textContent = selected.getAttribute('data-label');
                }
            }
        });
    </script>
</x-slot>

</x-layouts.app>