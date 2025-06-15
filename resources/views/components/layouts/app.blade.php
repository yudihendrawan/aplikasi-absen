<x-layouts.app.sidebar :title="$title ?? null">

    <flux:main>
        {{ $slot }}
    </flux:main>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log('Scripts loaded from partial layout');

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @elseif (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '{{ session('error') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

</x-layouts.app.sidebar>
