(() => {
    const sidebar = document.querySelector('#admin-sidebar');
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const closeButton = document.querySelector('[data-sidebar-close]');
    const backdrop = document.querySelector('[data-sidebar-backdrop]');
    const mobileSidebar = () => window.matchMedia('(max-width: 1099px)').matches;
    const setSidebar = open => {
        if (!sidebar || !toggle || !backdrop) return;
        sidebar.classList.toggle('is-open', open);
        backdrop.classList.toggle('is-visible', open);
        document.body.classList.toggle('sidebar-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        backdrop.setAttribute('aria-hidden', String(!open));
        if (open) closeButton?.focus();
    };

    toggle?.addEventListener('click', () => setSidebar(!sidebar.classList.contains('is-open')));
    closeButton?.addEventListener('click', () => {
        setSidebar(false);
        toggle?.focus();
    });
    backdrop?.addEventListener('click', () => setSidebar(false));
    sidebar?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
        if (mobileSidebar()) setSidebar(false);
    }));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && sidebar?.classList.contains('is-open')) {
            setSidebar(false);
            toggle?.focus();
        }
    });
    window.addEventListener('resize', () => {
        if (!mobileSidebar()) setSidebar(false);
    });

    document.querySelectorAll('[data-live-search]').forEach(form => {
        const input = form.querySelector('input[name="search"]');
        const results = document.querySelector('[data-live-results]');
        const status = form.querySelector('[data-live-search-status]');
        const button = form.querySelector('button[type="submit"], button:not([type])');
        if (!input || !results) return;

        let timer, controller, requestId = 0;
        const setButtonLoading = loading => {
            if (!button) return;
            button.classList.toggle('is-loading', loading);
            button.toggleAttribute('aria-busy', loading);
        };
        const loadResults = async url => {
            const currentRequest = ++requestId;
            controller?.abort();
            controller = new AbortController();
            results.classList.add('is-loading');
            setButtonLoading(true);
            if (status) status.textContent = '';
            try {
                const response = await fetch(url, {
                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                    signal: controller.signal,
                });
                if (!response.ok) throw new Error();
                const next = new DOMParser()
                    .parseFromString(await response.text(), 'text/html')
                    .querySelector('[data-live-results]');
                if (!next) throw new Error();
                results.innerHTML = next.innerHTML;
                history.replaceState({}, '', url);
                if (status) status.textContent = '';
            } catch (error) {
                if (error.name !== 'AbortError' && status) {
                    status.textContent = 'Could not update results. Please try again.';
                }
            } finally {
                if (currentRequest === requestId) {
                    results.classList.remove('is-loading');
                    setButtonLoading(false);
                }
            }
        };
        const search = () => {
            const url = new URL(form.action || window.location.href);
            const value = input.value.trim();
            value ? url.searchParams.set('search', value) : url.searchParams.delete('search');
            url.searchParams.delete('page');
            loadResults(url);
        };
        input.addEventListener('input', () => {
            clearTimeout(timer);
            setButtonLoading(true);
            timer = setTimeout(search, 300);
        });
        form.addEventListener('submit', event => {
            event.preventDefault();
            clearTimeout(timer);
            search();
        });
        results.addEventListener('click', event => {
            const link = event.target.closest('a');
            if (!link || !link.closest('nav[role="navigation"]')) return;
            event.preventDefault();
            loadResults(new URL(link.href));
        });
    });

    document.getElementById('imageInput')?.addEventListener('change', event => {
        const preview = document.getElementById('imagePreview');
        const file = event.target.files?.[0];
        if (!preview || !file) return;
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
        preview.onload = () => URL.revokeObjectURL(preview.src);
    });

    document.getElementById('stockQuantity')?.addEventListener('input', event => {
        const lowStockAlert = document.getElementById('lowStockAlert');
        if (lowStockAlert) lowStockAlert.max = Math.max(Number(event.target.value || 0), 0);
    });
})();
