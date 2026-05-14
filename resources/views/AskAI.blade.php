<x-layouts.chatApp :title="__('AskAI 🗿')">

@php
    // Default: belum ada chat. JS yang akan switch state.
    $hasChat = false;
@endphp

<div id="state-1" class="transition-opacity duration-200 {{ $hasChat ? 'hidden opacity-0' : 'opacity-100' }} min-h-screen flex items-center justify-center bg-gradient-to-b from-slate-100 to-slate-200 dark:from-slate-900 dark:to-slate-800 p-6">
    <div class="flex flex-col items-center gap-8">

        {{-- Icon Glow --}}
        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-orange-400 to-amber-200 shadow-xl flex items-center justify-center overflow-hidden">
            <img
                src="{{ asset('images/icon-siem-aziz.png') }}"
                alt="SIEM Icon"
                class="w-20 h-20 object-contain"
            >
        </div>


        {{-- Title --}}
        <h2 class="text-xl text-slate-700 dark:text-slate-200 font-medium tracking-wide text-center">
            Ada kerentanan apa hari ini 🧐 ?
        </h2>

        {{-- Card --}}
        <div class="w-[380px] bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-6 backdrop-blur-md border border-white/40 dark:border-slate-700/40">

            <div class="flex flex-col gap-3 text-sm font-medium">
                @foreach([
                    "Berikan saya wazuh alert",
                    "Berikan saya daftar agent di Wazuh saya",
                    "Tolong ringkas alert critical"
                ] as $item)
                    <button type="button" class="starter-btn relative overflow-hidden w-full text-left px-4 py-3 rounded-4xl transition-all duration-300 text-slate-600 dark:text-white group shadow">
                        <span class="absolute inset-0 bg-gradient-to-r from-orange-400 to-amber-200 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl"></span>
                        <span class="relative group-hover:text-yellow-800">
                            {{ $item }}
                        </span>
                    </button>
                @endforeach
            </div>

            {{-- Starter Input Bar --}}
            <div class="mt-5 flex items-center bg-slate-100 dark:bg-slate-800 rounded-4xl px-4 py-3 gap-3">
                <input
                    type="text"
                    id="starterInput"
                    class="starter-input flex-1 bg-transparent outline-none text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500"
                    placeholder="Tanyakan sesuatu tentang Wazuh..."
                    aria-label="starter-input"
                >
                <button id="starterSend" type="button" class="starter-send p-2 rounded-full bg-black dark:bg-white text-white dark:text-black hover:opacity-80 transition" aria-label="starter-send">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" class="w-4 h-4" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0l-4-4m4 4l-4 4"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<div id="state-2" class="{{ $hasChat ? '' : 'hidden' }} min-h-screen flex flex-col items-center justify-between bg-slate-100 dark:bg-slate-900 p-6">

    {{-- Chat Box --}}
    <div class="w-full max-w-xl flex flex-col gap-4 chat-box overflow-y-auto p-4" style="max-height:85vh;">

        {{-- initial empty; JS will append messages here --}}

    </div>

    {{-- Input Box --}}
    <div class="w-full max-w-xl mt-0 bg-white dark:bg-slate-800 rounded-4xl shadow-lg p-2  flex items-center gap-3 border border-black/5 dark:border-white/10">
        <button id="chatMic" type="button" class="chat-send p-2 rounded-full bg-black dark:bg-white text-white dark:text-black hover:opacity-80 transition">
            <flux:icon.microphone class="w-4 h-4" />
        </button>
        <input
            type="text"
            id="chatInput"
            class="chat-input flex-1 bg-transparent outline-none text-slate-700 dark:text-slate-200"
            placeholder="Bertanya, menulis atau mencari apa saja..."
            aria-label="chat-input"
            autocomplete="off"
        >
        <button id="chatSend" type="button" class="chat-send p-2 rounded-full bg-black dark:bg-white text-white dark:text-black hover:opacity-80 transition">
            <flux:icon.paper-airplane class="w-4 h-4" />
        </button>
    </div>
</div>

</x-layouts.chatApp>

@vite(['resources/css/app.css', 'resources/js/app.js'])