<div id="schedule-table">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-200">
            <tr>
                <th class="px-6 py-3">Nama</th>
                <th class="px-6 py-3">Email</th>
                <th class="px-6 py-3">No. Handpone</th>
                <th class="px-6 py-3">Role</th>
                <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4">{{ $user->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $user->email ?? '-' }}</td>

                    <td class="px-6 py-4">{{ $user->phone }}</td>
                    @forelse ($user->roles as $role)
                        <td class="px-6 py-4 capitalize">{{ $role->name }}</td>
                    @empty
                        <td class="px-6 py-4">-</td>
                    @endforelse
                    <td class="px-6 py-4 text-center relative">
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            <button @click="open = !open" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v.01M12 12v.01M12 18v.01" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute right-0 mt-2 w-32 z-50 bg-white border border-gray-200 dark:bg-gray-800 dark:border-gray-600 rounded-md shadow-lg">
                                <a href="{{ route('users.edit', $user->id) }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('users.destroy', $user->id) }}"
                                    onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full text-left block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">
                        Tidak ada user tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 pagination">
        {{ $users->links() }}
    </div>
</div>
