<x-layouts.chatApp :title="__('AskAI 🗿')">

@php
    // Sementara manual, nanti bisa diganti dari controller
    $hasChat = true;
@endphp

{{-- ============================= --}}
{{--   STATE 1: BELUM ADA CHAT     --}}
{{-- ============================= --}}
@if (!$hasChat)

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-slate-100 to-slate-200 dark:from-slate-900 dark:to-slate-800 p-6">

        <div class="flex flex-col items-center gap-8">

            {{-- Icon Glow --}}
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-400 to-blue-400 shadow-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L15 8L22 9L17 14L18 21L12 18L6 21L7 14L2 9L9 8L12 2Z"/>
                </svg>
            </div>

            {{-- Title --}}
            <h2 class="text-xl text-slate-700 dark:text-slate-200 font-medium tracking-wide">
                Ada kerentanan apa hari ini 🧐 ?
            </h2>

            {{-- Card --}}
            <div class="w-[380px] bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-6 backdrop-blur-md border border-white/40 dark:border-slate-700/40">

                <div class="flex flex-col gap-3 text-sm font-medium text-slate-600 dark:text-white">

                    @foreach([
                        "Berikan saya wazuh alert",
                        "Berikan saya daftar agent di Wazuh saya",
                        "Halo semuanya 😘😘"
                    ] as $item)
                        <button class="relative overflow-hidden w-full text-left px-4 py-3 rounded-4xl transition-all duration-700 text-slate-600 dark:text-white group shadow">
                            <span class="absolute inset-0 bg-gradient-to-r from-purple-500 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-700 rounded-xl"></span>
                            <span class="relative group-hover:text-white">
                                {{ $item }}
                            </span>
                        </button>
                    @endforeach

                </div>

                {{-- Input Bar --}}
                <div class="mt-5 flex items-center bg-slate-100 dark:bg-slate-800 rounded-4xl px-4 py-3 gap-3">
                    <input 
                        type="text"
                        class="flex-1 bg-transparent outline-none text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500"
                        placeholder="Bertanya, menulis atau mencari apa saja..."
                    >
                    <button class="p-2 rounded-full bg-black dark:bg-white text-white dark:text-black hover:opacity-80 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" class="w-4 h-4" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0l-4-4m4 4l-4 4"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

    </div>

@else
{{-- ============================= --}}
{{--   STATE 2: SUDAH MULAI CHAT   --}}
{{-- ============================= --}}

    <div class="min-h-screen flex flex-col items-center justify-between bg-slate-100 dark:bg-slate-900 p-6">

        {{-- Chat Bubble --}}
        <div class="w-full max-w-xl flex flex-col gap-4">

            {{-- Bot Bubble --}}
            <div class="self-start max-w-[80%] bg-white dark:bg-slate-800 rounded-4xl shadow p-4 text-slate-700 dark:text-slate-200">
                <p>Berdasarkan data terbaru, traffic meningkat dari social media 45%...</p>

                <div class="mt-3 bg-slate-100 dark:bg-slate-700 rounded-xl p-3">
                    <p class="font-medium">Example File</p>
                    <p class="text-xs opacity-70">500 KB</p>
                </div>
            </div>

            {{-- User Bubble --}}
            <div class="self-end max-w-[80%] bg-gradient-to-r from-purple-500 to-blue-500 text-white rounded-4xl shadow p-4">
                Bisa export ini ke CSV?
            </div>

            {{-- Bot typing --}}
            <div class="self-start flex items-center gap-2 text-slate-500 dark:text-slate-300">
                <span class="animate-pulse">• • •</span> Analyzing data...
            </div>

        </div>

        {{-- Input Box --}}
        <div class="w-full max-w-xl mt-6 bg-white dark:bg-slate-800 rounded-4xl shadow-lg p-4 flex items-center gap-3 border border-black/5 dark:border-white/10">
            <input 
                type="text"
                class="flex-1 bg-transparent outline-none text-slate-700 dark:text-slate-200"
                placeholder="Bertanya, menulis atau mencari apa saja..."
            >
            <button class="p-2 rounded-full bg-black dark:bg-white text-white dark:text-black hover:opacity-80 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" class="w-4 h-4" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0l-4-4m4 4l-4 4"/>
                </svg>
            </button>
        </div>

    </div>

@endif

</x-layouts.chatApp>
