import { marked } from 'marked';

marked.setOptions({
    breaks: true,
    gfm: true,
});

export function initChat() {
    
    // Elements
    const state1 = document.getElementById("state-1");
    if (!state1 || state1.dataset.initialized === "true") return;
    state1.dataset.initialized = "true";
    const state2 = document.getElementById("state-2");

    const starterBtns = document.querySelectorAll(".starter-btn");
    const starterInput = document.getElementById("starterInput");
    const starterSend = document.getElementById("starterSend");

    const chatBox = document.querySelector(".chat-box");
    const mainInput = document.getElementById("chatInput");
    const mainSend = document.getElementById("chatSend");
    const micBtn = document.getElementById("chatMic");

    const API_URL = "/chat-proxy";
    let busy = false;
    let recognition = null;

    const urlParams = new URLSearchParams(window.location.search);
    let currentConversationId = urlParams.get('id');

    // Speech Recognition
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    
    if (SpeechRecognition) {
        recognition = new SpeechRecognition();
        recognition.lang = "id-ID";
        recognition.continuous = false;
        recognition.interimResults = false;

        recognition.addEventListener("result", (event) => {
            const text = event.results[0][0].transcript;
            mainInput.value = text;
            handleSend(text);
            mainInput.value = "";
        });

        recognition.onstart = () => micBtn?.classList.add("animate-pulse");
        recognition.onend = () => micBtn?.classList.remove("animate-pulse");
    }

    micBtn?.addEventListener("click", () => {
        if (!recognition) {
            alert("Browser kamu tidak mendukung voice input.");
            return;
        }
        recognition.start();
    });

    // Helper Functions
    function showState2() {
        if (state1) state1.classList.add("hidden");
        if (state2) state2.classList.remove("hidden");
        setTimeout(() => mainInput?.focus(), 80);
    }

    function scrollChatToBottom() {
        if (!chatBox) return;
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function addUserBubble(text) {
        if (!chatBox) return;
        const el = document.createElement("div");
        el.className = "self-end max-w-[80%] bg-orange-400 text-white rounded-4xl shadow p-4 break-words prose prose-sm";
        el.innerText = text;
        chatBox.appendChild(el);
        scrollChatToBottom();
    }

    function addBotBubble(text) {
        if (!chatBox) return;
        const el = document.createElement("div");
        el.className = "self-start max-w-[80%] bg-white dark:bg-slate-800 rounded-4xl shadow p-4 text-slate-700 dark:text-slate-200 break-words prose prose-sm";
        el.innerHTML = marked.parse(text || "");
        chatBox.appendChild(el);
        scrollChatToBottom();
    }

    function addTypingIndicator() {
        if (!chatBox) return null;
        const el = document.createElement("div");
        el.className = "bot-typing self-start flex items-center gap-2 text-slate-500 dark:text-slate-300";
        el.innerHTML = `<flux:icon.loading class="w-5 h-5 animate-spin text-slate-500 dark:text-slate-300"></flux:icon.loading><span class="ml-2">Sedang memproses...</span>`;
        chatBox.appendChild(el);
        scrollChatToBottom();
        return el;
    }

    function removeTypingIndicator() {
        document.querySelector(".bot-typing")?.remove();
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
        const resp = await fetch(API_URL, {
            method: "POST",
            headers: { 
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "X-Requested-With": "XMLHttpRequest"
            },
            // TAMBAHKAN currentConversationId DISINI
            body: JSON.stringify({ 
                message,
                conversation_id: currentConversationId 
            })
        });

        if (!resp.ok) {
            const txt = await resp.text().catch(() => "error");
            throw new Error(`API ${resp.status}: ${txt}`);
        }
        return resp.json();
    }

    async function loadHistory(id) {
        showState2(); 
        if (chatBox) chatBox.innerHTML = '';
        try {
            const resp = await fetch(`/chat/messages/${id}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" }
            });

            // Kalau chat gak ketemu (404), redirect manual ke halaman utama
            if (resp.status === 404) {
                window.location.href = '/AskAI';
                return;
            }
        
            if (!resp.ok) throw new Error("Gagal ambil history");
            const data = await resp.json();
            
            // Bersihkan chat box dulu (kecuali starter state)
            showState2();
            chatBox.innerHTML = ''; 

            // Looping data dari DB dan tampilin ke bubble
            data.messages.forEach(msg => {
                if (msg.role === 'user') {
                    addUserBubble(msg.content);
                } else {
                    addBotBubble(msg.content);
                }
            });
        } catch (err) {
            console.error("Gagal load history:", err);
        }
    }

    // Jalankan loadHistory kalau ada ID di URL
    if (currentConversationId) {
        loadHistory(currentConversationId);
    }

    async function handleSend(message) {
        if (!message || busy) return;
        busy = true;

        showState2();
        addUserBubble(message);
        const typingEl = addTypingIndicator();

        mainInput?.setAttribute("disabled", "true");
        starterInput?.setAttribute("disabled", "true");
        mainSend?.setAttribute("disabled", "true");
        starterSend?.setAttribute("disabled", "true");

        try {
            // Ambil seluruh objek data, jangan di-destructuring dulu
            const data = await sendToApi(message);
            const botResponse = data.response;

            // Sekarang data.conversation_id sudah bisa diakses
            if (!currentConversationId && data.conversation_id) {
                currentConversationId = data.conversation_id;
                window.history.pushState({}, '', `?id=${currentConversationId}`);
            }

            removeTypingIndicator();
            addBotBubble(botResponse ?? String(botResponse));
        } catch (err) {
            console.error("Chat API error:", err);
            removeTypingIndicator();
            showError("Gagal menghubungi API.");
        } finally {
            mainInput?.removeAttribute("disabled");
            starterInput?.removeAttribute("disabled");
            mainSend?.removeAttribute("disabled");
            starterSend?.removeAttribute("disabled");
            busy = false;
            setTimeout(() => mainInput?.focus(), 60);
        }
    }

    // Event Listeners
    starterBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            const text = (e.currentTarget?.innerText || "").trim();
            if (text) handleSend(text);
        });
    });

    starterSend?.addEventListener("click", () => {
        handleSend(starterInput.value.trim());
        starterInput.value = "";
    });

    starterInput?.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            handleSend(starterInput.value.trim());
            starterInput.value = "";
        }
    });

    mainSend?.addEventListener("click", () => {
        handleSend(mainInput.value.trim());
        mainInput.value = "";
    });

    mainInput?.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            handleSend(mainInput.value.trim());
            mainInput.value = "";
        }
    });

    console.log('✅ Chat initialized successfully');
}

// Expose ke global
window.initChat = initChat;