<div class="flex items-center justify-between px-3 py-2">
    <div class="flex items-center space-x-2">
        @if($isRunning)
            {{-- CPU aktif (warna hijau + animasi nyala) --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-green-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 9h6v6H9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 10v4m16-4v4M10 4h4m-4 16h4M6 6h12v12H6z" />
            </svg>
        @else
            {{-- CPU mati (warna abu-abu) --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 9h6v6H9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 10v4m16-4v4M10 4h4m-4 16h4M6 6h12v12H6z" />
            </svg>
        @endif

        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-200">
            LM Studio Server
        </span>
    </div>

    <label class="inline-flex items-center cursor-pointer">
        <input type="checkbox"
               wire:click="toggleServer"
               wire:model="isRunning"
               class="sr-only peer">
        <div class="relative w-11 h-6 bg-zinc-300 peer-focus:outline-none rounded-full peer dark:bg-zinc-600
                    peer-checked:bg-green-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                    after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5
                    after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
    </label>
</div>
