import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Handle custom modal events from buttons
window.addEventListener('open-modal', (event) => {
    if (event.detail === 'login') {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login-modal' }));
    } else if (event.detail === 'register') {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'register-modal' }));
    }
});

console.log('Modal event listeners initialized');
