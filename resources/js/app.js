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

/**
 * Landing hero: typewriter headline. Keeps Blade fallback text in DOM until animation runs;
 * never wipes the headline on failure (empty h1 if JS breaks was a bug).
 */
function initLandingHeroTypewriter() {
    const h1 = document.getElementById('landing-hero-headline');
    if (!h1 || !document.getElementById('landing-root')) {
        return;
    }

    const full = h1.getAttribute('data-type-text')?.trim();
    if (!full) {
        return;
    }

    let span = h1.querySelector('.landing-hero-typewriter');
    if (!span) {
        span = document.createElement('span');
        span.className = 'landing-hero-typewriter';
        span.setAttribute('aria-hidden', 'true');
        h1.insertBefore(span, h1.firstChild);
    }

    let caret = h1.querySelector('.landing-hero-typewriter-caret');
    if (!caret) {
        caret = document.createElement('span');
        caret.className = 'landing-hero-typewriter-caret';
        caret.setAttribute('aria-hidden', 'true');
        h1.appendChild(caret);
    }

    h1.setAttribute('aria-label', full);

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduceMotion) {
        span.textContent = full;
        caret.remove();

        return;
    }

    function delayAfterChar(ch) {
        let base = 28 + Math.random() * 36;
        if (ch === ' ') {
            base = 14 + Math.random() * 18;
        }
        if (ch === ',' || ch === ';') {
            base = 100 + Math.random() * 80;
        }
        if (ch === '.' || ch === '…' || ch === '—' || ch === '–') {
            base = 220 + Math.random() * 120;
        }

        return base;
    }

    try {
        span.textContent = '';
        let i = 0;

        function tick() {
            if (i >= full.length) {
                caret.classList.add('is-done');
                window.setTimeout(() => {
                    caret.remove();
                }, 500);

                return;
            }

            span.textContent += full[i];
            const ch = full[i];
            i += 1;
            window.setTimeout(tick, delayAfterChar(ch));
        }

        tick();
    } catch {
        span.textContent = full;
        caret.remove();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLandingHeroTypewriter);
} else {
    initLandingHeroTypewriter();
}

console.log('Modal event listeners initialized');
