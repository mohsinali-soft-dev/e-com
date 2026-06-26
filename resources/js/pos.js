(() => {
    const root = document.querySelector('[data-pos]');
    if (!root) return;

    let cart = [];
    let searchResults = [];
    let activeIndex = -1;
    let debounceTimer = null;
    let currentController = null;

    const config = {
        searchUrl: root.dataset.searchUrl,
        scanUrl: root.dataset.scanUrl,
        checkoutUrl: root.dataset.checkoutUrl,
        csrf: root.dataset.csrf,
        currency: root.dataset.currency || '',
    };

    const searchInput = document.getElementById('barcodeInput');
    const searchPanel = document.getElementById('posSearchResults');
    const posMessage = document.getElementById('posMessage');
    const cartBody = document.getElementById('cartBody');
    const subtotalElement = document.getElementById('subtotal');
    const grandTotal = document.getElementById('grandTotal');
    const cartCount = document.getElementById('cartCount');
    const discountInput = document.getElementById('discount');
    const paidAmount = document.getElementById('paidAmount');
    const changeAmount = document.getElementById('changeAmount');
    const paymentMethod = document.getElementById('paymentMethod');
    const checkoutButton = document.getElementById('checkoutButton');
    const customerId = document.getElementById('customerId');

    const money = value => Number(value || 0).toFixed(2);
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
    })[character]);

    const setMessage = (message, className = 'muted') => {
        posMessage.className = className;
        posMessage.textContent = message;
    };

    const wholeQuantity = item => item.sale_type === 'piece';
    const normalizeQuantity = (item, value) => {
        const minimum = wholeQuantity(item) ? 1 : 0.01;
        const parsed = Math.max(minimum, Math.min(Number(value || minimum), item.stock));
        return wholeQuantity(item) ? Math.floor(parsed) : parsed;
    };

    searchInput?.addEventListener('input', () => {
        const query = searchInput.value.trim();
        clearTimeout(debounceTimer);
        if (query.length < 2) {
            closeSearchPanel();
            return;
        }
        debounceTimer = setTimeout(() => searchProducts(query), 250);
    });

    searchInput?.addEventListener('keydown', async event => {
        if (event.key === 'ArrowDown') {
            event.preventDefault();
            moveActive(1);
            return;
        }
        if (event.key === 'ArrowUp') {
            event.preventDefault();
            moveActive(-1);
            return;
        }
        if (event.key === 'Escape') {
            closeSearchPanel();
            return;
        }
        if (event.key === 'Enter') {
            event.preventDefault();
            await enterSearch();
        }
    });

    searchPanel?.addEventListener('mousedown', event => {
        const item = event.target.closest('[data-search-result]');
        if (!item) return;
        const index = Number(item.dataset.searchResult);
        const result = searchResults[index];
        if (result && result.selectable) {
            addToCart(result);
            resetSearchInput();
        }
    });

    document.addEventListener('click', event => {
        if (!root.contains(event.target)) closeSearchPanel();
    });

    discountInput?.addEventListener('input', renderTotals);
    paidAmount?.addEventListener('input', renderTotals);
    root.querySelector('[data-clear-search]')?.addEventListener('click', resetSearchInput);
    root.querySelector('[data-clear-cart]')?.addEventListener('click', () => clearCart());
    checkoutButton?.addEventListener('click', checkout);

    async function enterSearch() {
        if (activeIndex >= 0 && searchResults[activeIndex]) {
            const result = searchResults[activeIndex];
            if (!result.selectable) {
                setMessage('This item is out of stock.', 'text-danger');
                return;
            }
            addToCart(result);
            resetSearchInput();
            return;
        }

        const query = searchInput.value.trim();
        if (!query) return;

        const results = await searchProducts(query, true);
        const exact = results.find(item => item.exact && item.selectable);
        if (exact) {
            addToCart(exact);
            resetSearchInput();
            return;
        }
        const firstSelectable = results.find(item => item.selectable);
        if (results.length === 1 && firstSelectable) {
            addToCart(firstSelectable);
            resetSearchInput();
            return;
        }
        if (!results.length) {
            setMessage('No product found.', 'text-danger');
        }
    }

    async function searchProducts(query, immediate = false) {
        if (!config.searchUrl) return [];
        if (currentController) currentController.abort();
        currentController = new AbortController();

        if (immediate) setMessage('Searching product...');

        try {
            const url = new URL(config.searchUrl, window.location.origin);
            url.searchParams.set('q', query);
            const response = await fetch(url.toString(), {
                headers: {'Accept': 'application/json'},
                signal: currentController.signal,
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Search failed.');
            searchResults = data.results || [];
            activeIndex = searchResults.findIndex(item => item.selectable);
            renderSearchPanel();
            return searchResults;
        } catch (error) {
            if (error.name !== 'AbortError') setMessage(error.message, 'text-danger');
            return [];
        }
    }

    function renderSearchPanel() {
        if (!searchResults.length) {
            searchPanel.innerHTML = '<div class="pos-search-empty">No matching products found.</div>';
            openSearchPanel();
            return;
        }

        searchPanel.innerHTML = searchResults.map((item, index) => {
            const disabled = !item.selectable;
            const active = index === activeIndex ? ' is-active' : '';
            const image = item.image_url
                ? `<img class="pos-search-image" src="${escapeHtml(item.image_url)}" alt="${escapeHtml(item.name)}">`
                : '<div class="pos-search-image pos-search-image-empty">N/A</div>';
            const stockCopy = disabled ? 'Out of Stock' : `Stock: ${escapeHtml(item.stock)} ${escapeHtml(item.unit || '')}`;
            return `
                <button class="pos-search-item${active}${disabled ? ' is-disabled' : ''}" type="button" role="option" aria-selected="${index === activeIndex ? 'true' : 'false'}" data-search-result="${index}" ${disabled ? 'disabled' : ''}>
                    ${image}
                    <span class="pos-search-copy">
                        <strong>${escapeHtml(item.name)}</strong>
                        <small>SKU: ${escapeHtml(item.sku || '-')} · Barcode: ${escapeHtml(item.barcode || '-')}</small>
                        <small>${escapeHtml(config.currency)} ${money(item.price)} · ${stockCopy}</small>
                    </span>
                    <span class="pos-search-tag">${disabled ? 'Out' : matchLabel(item.match_type)}</span>
                </button>`;
        }).join('');
        openSearchPanel();
    }

    function matchLabel(type) {
        return {
            exact_barcode: 'Barcode',
            exact_sku: 'SKU',
            exact_variant_barcode: 'Variant barcode',
            exact_variant_sku: 'Variant SKU',
            product_name: 'Name',
            variant_name: 'Variant',
        }[type] || 'Match';
    }

    function moveActive(direction) {
        if (!searchResults.length) return;
        const selectableIndexes = searchResults.map((item, index) => item.selectable ? index : null).filter(index => index !== null);
        if (!selectableIndexes.length) return;
        const currentPosition = selectableIndexes.indexOf(activeIndex);
        const nextPosition = currentPosition === -1
            ? 0
            : (currentPosition + direction + selectableIndexes.length) % selectableIndexes.length;
        activeIndex = selectableIndexes[nextPosition];
        renderSearchPanel();
    }

    function openSearchPanel() {
        searchPanel.hidden = false;
        searchInput.setAttribute('aria-expanded', 'true');
    }

    function closeSearchPanel() {
        searchPanel.hidden = true;
        searchInput.setAttribute('aria-expanded', 'false');
        activeIndex = -1;
    }

    function resetSearchInput() {
        searchInput.value = '';
        searchResults = [];
        closeSearchPanel();
        searchInput.focus();
    }

    function addToCart(product) {
        if (!product.selectable && product.selectable !== undefined) {
            setMessage('This item is out of stock.', 'text-danger');
            return;
        }
        const key = `${product.id}:${product.variant_id || 0}`;
        const existing = cart.find(item => item.key === key);
        if (existing) {
            const nextQuantity = normalizeQuantity(existing, existing.qty + 1);
            if (nextQuantity > product.stock || nextQuantity === existing.qty) {
                setMessage(`Only ${product.stock} ${product.unit || 'units'} available.`, 'text-danger');
                return;
            }
            existing.qty = nextQuantity;
        } else {
            cart.push({...product, key, price: Number(product.price), stock: Number(product.stock), qty: 1});
        }
        renderCart();
        setMessage(`Added ${product.name}.`, 'text-success');
    }

    function updateQty(key, value) {
        const item = cart.find(item => item.key === key);
        if (!item) return;
        item.qty = normalizeQuantity(item, value);
        renderCart();
    }

    function removeItem(key) {
        cart = cart.filter(item => item.key !== key);
        renderCart();
    }

    function clearCart(showMessage = true) {
        cart = [];
        discountInput.value = 0;
        paidAmount.value = 0;
        renderCart();
        if (showMessage) setMessage('Cart cleared.');
        resetSearchInput();
    }

    function totals() {
        const subtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
        const discount = Math.min(Math.max(Number(discountInput.value || 0), 0), subtotal);
        const total = Math.max(subtotal - discount, 0);
        const paid = Math.max(Number(paidAmount.value || 0), 0);
        return {subtotal, discount, total, paid, change: Math.max(paid - total, 0)};
    }

    function renderTotals() {
        const value = totals();
        subtotalElement.textContent = money(value.subtotal);
        grandTotal.textContent = money(value.total);
        changeAmount.textContent = money(value.change);
    }

    function renderCart() {
        if (!cart.length) {
            cartBody.innerHTML = '<tr><td colspan="6" class="empty-state">No items scanned yet.</td></tr>';
        } else {
            cartBody.innerHTML = cart.map(item => `
                <tr>
                    <td><strong>${escapeHtml(item.name)}</strong><br><small>${escapeHtml(item.sku)} - Stock ${item.stock}</small></td>
                    <td>${escapeHtml(item.barcode || '-')}</td>
                    <td>${escapeHtml(config.currency)} ${money(item.price)}</td>
                    <td><input type="number" min="${wholeQuantity(item) ? 1 : 0.01}" max="${item.stock}" step="${wholeQuantity(item) ? 1 : 0.01}" value="${item.qty}" data-cart-qty="${escapeHtml(item.key)}" style="width:105px;"></td>
                    <td><strong>${escapeHtml(config.currency)} ${money(item.price * item.qty)}</strong></td>
                    <td><button class="btn btn-light" type="button" data-remove-item="${escapeHtml(item.key)}">Remove</button></td>
                </tr>`).join('');
            cartBody.querySelectorAll('[data-cart-qty]').forEach(input => {
                input.addEventListener('change', () => updateQty(input.dataset.cartQty, input.value));
            });
            cartBody.querySelectorAll('[data-remove-item]').forEach(button => {
                button.addEventListener('click', () => removeItem(button.dataset.removeItem));
            });
        }
        cartCount.textContent = cart.length;
        renderTotals();
    }

    async function checkout() {
        if (!cart.length) {
            setMessage('Add at least one product before checkout.', 'text-danger');
            return;
        }
        const value = totals();
        if (value.paid < value.total) {
            setMessage('Paid amount is less than the total amount.', 'text-danger');
            paidAmount.focus();
            return;
        }

        checkoutButton.disabled = true;
        checkoutButton.textContent = 'Saving Sale...';
        try {
            const response = await fetch(config.checkoutUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrf},
                body: JSON.stringify({
                    items: cart,
                    discount: value.discount,
                    paid_amount: value.paid,
                    payment_method: paymentMethod.value,
                    customer_id: customerId.value || null,
                }),
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                const validationMessage = data.errors ? Object.values(data.errors).flat()[0] : null;
                throw new Error(validationMessage || data.message || 'Checkout failed.');
            }
            clearCart(false);
            posMessage.className = 'text-success';
            posMessage.innerHTML = `Sale completed. Invoice <strong>${escapeHtml(data.invoice_no)}</strong>, change ${escapeHtml(config.currency)} ${money(data.change_amount)}. <a href="${data.sale_url}">View invoice</a>`;
        } catch (error) {
            setMessage(error.message, 'text-danger');
        } finally {
            checkoutButton.disabled = false;
            checkoutButton.textContent = 'Complete Sale';
        }
    }

    searchInput?.focus();
})();
