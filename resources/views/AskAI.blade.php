<x-layouts.chatApp :title="__('AskAI 🗿')">

@php
    // Default: belum ada chat. JS yang akan switch state.
    $hasChat = false;
@endphp

{{-- ============================= --}}
{{--   STATE 1: BELUM ADA CHAT     --}}
{{-- ============================= --}}
<div id="state-1" class="{{ $hasChat ? 'hidden' : '' }} min-h-screen flex items-center justify-center bg-gradient-to-b from-slate-100 to-slate-200 dark:from-slate-900 dark:to-slate-800 p-6">
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

            <div class="flex flex-col gap-3 text-sm font-medium text-slate-600 dark:text-white">
                @foreach([
                    "Berikan saya wazuh alert",
                    "Berikan saya daftar agent di Wazuh saya",
                    "Tolong ringkas alert critical"
                ] as $item)
                    <button type="button" class="starter-btn relative overflow-hidden w-full text-left px-4 py-3 rounded-4xl transition-all duration-300 text-slate-600 dark:text-white group shadow">
                        <span class="absolute inset-0 bg-gradient-to-r from-orange-400 to-amber-200 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl"></span>
                        <span class="relative group-hover:text-white">
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

{{-- ============================= --}}
{{--   STATE 2: SUDAH MULAI CHAT   --}}
{{-- ============================= --}}
<div id="state-2" class="{{ $hasChat ? '' : 'hidden' }} hidden min-h-screen flex flex-col items-center justify-between bg-slate-100 dark:bg-slate-900 p-6">

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

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Elements (always rendered; hidden by class)
    const state1 = document.getElementById("state-1");
    const state2 = document.getElementById("state-2");

    // Starter controls
    const starterBtns = document.querySelectorAll(".starter-btn");
    const starterInput = document.getElementById("starterInput");
    const starterSend = document.getElementById("starterSend");

    // Main chat controls
    const chatBox = document.querySelector(".chat-box");
    const mainInput = document.getElementById("chatInput");
    const mainSend = document.getElementById("chatSend");

    // Mic controls
    const micBtn = document.getElementById("chatMic");
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    let recognition = null;
    
    if (SpeechRecognition) {
        recognition = new SpeechRecognition();
        recognition.lang = "id-ID";
        recognition.continuous = false;
        recognition.interimResults = false;
    
        // result: masukkan ke input + auto-send
        recognition.addEventListener("result", (event) => {
            const text = event.results[0][0].transcript;
            mainInput.value = text;
            handleSend(text);
            mainInput.value = "";
        });
    
        // animasi mic nyala saat recording
        recognition.onstart = () => micBtn?.classList.add("animate-pulse");
        recognition.onend = () => micBtn?.classList.remove("animate-pulse");
    }
    
    // Run recognition when mic clicked
    micBtn?.addEventListener("click", () => {
        if (!recognition) {
            alert("Browser kamu tidak mendukung voice input.");
            return;
        }
        recognition.start();
    });

    
    // Config
    const API_URL = "http://localhost:3232/chat"; // sesuaikan kalau perlu
    let busy = false; // disable while awaiting response

    // Helpers
    function showState2() {
        if (state1) state1.classList.add("hidden");
        if (state2) state2.classList.remove("hidden");
        // focus main input
        setTimeout(() => mainInput?.focus(), 80);
    }

    function addUserBubble(text) {
        if (!chatBox) return;
        const el = document.createElement("div");
        el.className = "self-end max-w-[80%] bg-gradient-to-r from-orange-400 to-amber-200 text-white rounded-4xl shadow p-2 break-words";
        el.innerText = text;
        chatBox.appendChild(el);
        scrollChatToBottom();
    }

    function addBotBubble(text) {
        if (!chatBox) return;
        const el = document.createElement("div");
        el.className = "self-start max-w-[80%] bg-white dark:bg-slate-800 rounded-4xl shadow p-4 text-slate-700 dark:text-slate-200 break-words";
        el.innerText = text;
        chatBox.appendChild(el);
        scrollChatToBottom();
    }

    function addTypingIndicator() {
        if (!chatBox) return null;
        const el = document.createElement("div");
        el.className = "bot-typing self-start flex items-center gap-2 text-slate-500 dark:text-slate-300";
        el.innerHTML = `
            <flux:icon.loading class="w-5 h-5 animate-spin text-slate-500 dark:text-slate-300"></flux:icon.loading>
            <span class="ml-2">Sedang memproses...</span>
        `;
        chatBox.appendChild(el);
        scrollChatToBottom();
        return el;
    }

    function removeTypingIndicator() {
        const t = document.querySelector(".bot-typing");
        if (t) t.remove();
    }

    function scrollChatToBottom() {
        if (!chatBox) return;
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function showError(msg) {
        if (!chatBox) return;
        const el = document.createElement("div");
        el.className = "self-start bg-red-500 text-white px-4 py-2 rounded-xl";
        el.innerText = msg;
        chatBox.appendChild(el);
        scrollChatToBottom();
    }

    async function sendToApi(message) {
        // call external FastAPI
        const resp = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ message })
        });
        if (!resp.ok) {
            const txt = await resp.text().catch(() => resp.statusText || "error");
            throw new Error(`API ${resp.status}: ${txt}`);
        }
        return resp.json();
    }

    async function handleSend(message) {
        if (!message || busy) return;
        busy = true;

        // if currently in starter state, switch to chat view
        showState2();

        // UI: add user bubble and typing indicator + disable inputs
        addUserBubble(message);
        const typingEl = addTypingIndicator();
        mainInput?.setAttribute("disabled", "true");
        starterInput?.setAttribute("disabled", "true");
        mainSend?.setAttribute("disabled", "true");
        starterSend?.setAttribute("disabled", "true");

        try {
            const { response } = await sendToApi(message);
            // remove typing and show response
            removeTypingIndicator();
            addBotBubble(response ?? String(response));
        } catch (err) {
            console.error("Chat API error:", err);
            removeTypingIndicator();
            showError("Gagal menghubungi API. Pastikan FastAPI jalan (uvicorn api:app --port=3232).");
        } finally {
            // re-enable
            mainInput?.removeAttribute("disabled");
            starterInput?.removeAttribute("disabled");
            mainSend?.removeAttribute("disabled");
            starterSend?.removeAttribute("disabled");
            busy = false;
            // focus main input
            setTimeout(() => mainInput?.focus(), 60);
        }
    }

    // Wire starter buttons
    starterBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            const text = (e.currentTarget?.innerText || "").trim();
            if (text) handleSend(text);
        });
    });

    // Starter input send
    starterSend?.addEventListener("click", () => {
        handleSend(starterInput.value.trim());
        starterInput.value = ""; // <-- reset
    });

    starterInput?.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            handleSend(starterInput.value.trim());
            starterInput.value = ""; // <-- reset
        }
    });

    // Main chat send
    mainSend?.addEventListener("click", () => {
        handleSend(mainInput.value.trim());
        mainInput.value = ""; // <-- reset
    });

    mainInput?.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            handleSend(mainInput.value.trim());
            mainInput.value = ""; // <-- reset
        }
    });


    // Optional: if you want to start in chat mode based on server variable
    @if ($hasChat)
        showState2();
    @endif
});
</script>
