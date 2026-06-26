// ── Service Worker (PWA) ──────────────────────────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

// ── NProgress (page loading bar) ─────────────────────────────────────────────
import NProgress from 'nprogress';
import 'nprogress/nprogress.css';
NProgress.configure({ showSpinner: false, speed: 300, minimum: 0.1 });

// ── Page Transition ───────────────────────────────────────────────────────────
// Intercept all same-origin link clicks → show progress bar + View Transition
function navigate(url) {
    NProgress.start();
    if (!document.startViewTransition) {
        window.location.href = url;
        return;
    }
    document.startViewTransition(() => {
        window.location.href = url;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Intercept anchor clicks that are plain page navigations (not modal/fetch triggers)
    document.addEventListener('click', (e) => {
        const anchor = e.target.closest('a[href]');
        if (!anchor) return;

        const href = anchor.getAttribute('href');
        // Skip: external, hash-only, javascript:, download, or has data-no-transition
        if (
            !href ||
            href.startsWith('#') ||
            href.startsWith('javascript') ||
            anchor.hasAttribute('download') ||
            anchor.hasAttribute('data-no-transition') ||
            anchor.target === '_blank' ||
            anchor.closest('[x-data]')?.hasAttribute('@click.prevent') // skip alpine-handled links
        ) return;

        // Only same-origin
        try {
            const url = new URL(href, window.location.origin);
            if (url.origin !== window.location.origin) return;
        } catch { return; }

        e.preventDefault();
        navigate(href);
    });

    // Intercept form submits (non-AJAX forms like logout)
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (
            form.getAttribute('data-no-transition') ||
            form.closest('[x-data]') // Alpine handles these via fetch
        ) return;
        NProgress.start();
    });

    // Done when page fully loaded
    NProgress.done();
});

// Safety: also finish on popstate (browser back/forward)
window.addEventListener('pageshow', () => NProgress.done());
