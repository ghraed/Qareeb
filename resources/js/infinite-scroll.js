export function setupInfiniteScroll(root) {
    const items = root.querySelector('[data-scroll-items]');
    const sentinel = root.querySelector('[data-scroll-sentinel]');
    const status = root.querySelector('[data-scroll-status]');
    const more = root.querySelector('[data-scroll-more]');
    let nextUrl = root.dataset.nextUrl;
    let loading = false;
    let observer;

    async function loadMore() {
        if (loading || !nextUrl) return;
        loading = true;
        observer?.disconnect();
        items.setAttribute('aria-busy', 'true');
        more?.setAttribute('aria-disabled', 'true');
        status.textContent = 'Loading more…';
        let succeeded = false;

        try {
            const response = await fetch(nextUrl, { credentials: 'same-origin' });
            if (!response.ok || response.redirected) throw new Error('Unable to load items');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const incoming = Array.from(page.querySelectorAll('[data-infinite-scroll]'))
                .find(list => list.dataset.infiniteScroll === root.dataset.infiniteScroll);
            const newItems = incoming?.querySelector('[data-scroll-items]');
            if (!newItems) throw new Error('Missing list');

            const count = newItems.children.length;
            items.append(...Array.from(newItems.children));
            nextUrl = incoming.dataset.nextUrl;
            root.dataset.nextUrl = nextUrl;
            status.textContent = nextUrl ? `${count} more items loaded.` : 'All items loaded.';
            if (nextUrl && more) {
                more.setAttribute('href', nextUrl);
                more.textContent = 'Load more';
            }
            else if (more) more.hidden = true;
            succeeded = true;
        } catch {
            status.textContent = 'Could not load more items. Please try again.';
            if (more) more.textContent = 'Try again';
        } finally {
            loading = false;
            items.setAttribute('aria-busy', 'false');
            more?.removeAttribute('aria-disabled');
            if (succeeded && nextUrl) observer?.observe(sentinel);
        }
    }

    more?.addEventListener('click', event => {
        event.preventDefault();
        loadMore();
    });

    if (nextUrl && typeof IntersectionObserver !== 'undefined') {
        observer = new IntersectionObserver(entries => {
            if (entries.some(entry => entry.isIntersecting)) loadMore();
        }, { rootMargin: '200px 0px' });
        observer.observe(sentinel);
    }
}

export default function initInfiniteScroll() {
    document.querySelectorAll('[data-infinite-scroll]').forEach(setupInfiniteScroll);
}
