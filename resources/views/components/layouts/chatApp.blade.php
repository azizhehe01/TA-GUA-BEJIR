<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main class="!p-0 !lg:p-0">
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>

@vite(['resources/css/app.css', 'resources/js/app.js'])
