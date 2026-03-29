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

/** Landing page: ?register=1 or ?login=1 opens the matching auth modal (UX #25). */
function stripQueryParam(key) {
    const url = new URL(window.location.href);
    if (!url.searchParams.has(key)) {
        return;
    }
    url.searchParams.delete(key);
    const q = url.searchParams.toString();
    window.history.replaceState({}, '', url.pathname + (q ? `?${q}` : '') + url.hash);
}

function openAuthModalFromLandingQuery() {
    if (!document.getElementById('landing-root')) {
        return;
    }
    const params = new URLSearchParams(window.location.search);
    const reg = params.get('register');
    if (reg !== null && (reg === '' || reg === '1' || reg === 'true')) {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'register' }));
        stripQueryParam('register');
        return;
    }
    const log = params.get('login');
    if (log !== null && (log === '' || log === '1' || log === 'true')) {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login' }));
        stripQueryParam('login');
    }
}

openAuthModalFromLandingQuery();

console.log('Modal event listeners initialized');
