const COLOR_SCHEME_KEY = 'novel-color-scheme';

/** @returns {'light'|'dark'|'system'} */
export function readColorSchemePreference() {
    try {
        const v = localStorage.getItem(COLOR_SCHEME_KEY);
        if (v === 'light' || v === 'dark' || v === 'system') {
            return v;
        }
    } catch {
        //
    }

    return 'system';
}

export function isDarkForScheme(scheme) {
    if (scheme === 'dark') {
        return true;
    }
    if (scheme === 'light') {
        return false;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches;
}

export function applyColorSchemeClass(scheme) {
    document.documentElement.classList.toggle('dark', isDarkForScheme(scheme));
}

export function stripReaderFocusUnlessChapterPage() {
    if (! document.getElementById('novel-chapter-reader')) {
        document.documentElement.classList.remove('novel-reader-focus');
    }
}

export function initNovelColorScheme() {
    stripReaderFocusUnlessChapterPage();
    applyColorSchemeClass(readColorSchemePreference());
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (readColorSchemePreference() === 'system') {
            applyColorSchemeClass('system');
        }
    });
}

export function cycleColorScheme() {
    const order = ['system', 'light', 'dark'];
    const cur = readColorSchemePreference();
    const next = order[(order.indexOf(cur) + 1) % order.length];
    try {
        localStorage.setItem(COLOR_SCHEME_KEY, next);
    } catch {
        //
    }
    applyColorSchemeClass(next);

    return next;
}

export function bindNovelColorSchemeControls() {
    document.querySelectorAll('[data-novel-theme-cycle]').forEach((el) => {
        el.addEventListener('click', () => cycleColorScheme());
    });
}
