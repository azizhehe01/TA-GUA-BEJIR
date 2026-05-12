import { initChat } from './chat.js';

function setup() {
    
    if (document.getElementById('state-1')) {
        initChat();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setup);
} else {
    setup();
}

document.addEventListener('livewire:navigated', setup);