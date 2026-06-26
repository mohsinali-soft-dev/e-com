(() => {
    const root = document.querySelector('[data-pos]');
    if (!root) return;

    let cart = [];
    const config = {
        scanUrl: root.dataset.scanUrl,
        checkoutUrl: root.dataset.checkoutUrl,
        csrf: root.dataset.csrf,
        currency: root.dataset.currency || '',
    };
    const barcodeInput = document.getElementById('barcodeInput');
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
        const minimum = wholeQuantity(item) ? 1 : 0.001;
        const parsed = Math.max(minimum, Math.min(Number(value || minimum), item.stock));
        return wholeQuantity(item) ? Math.floor(parsed) : parsed;
    };

    barcodeInput?.addEventListener('keydown', event => {
        if (event.key === 'Enter') {
            event.preventDefault();
            scanCurrentBarcode();
        }
    });
    discountInput?.addEventListener('input', renderTotals);
    paidAmount?.addEventListener('input', renderTotals);
    root.querySelector('[data-scan-current]')?.addEventListener('click', scanCurrentBarcode);
    root.querySelector('[data-clear-cart]')?.addEventListener('click', () => clearCart());
    checkoutButton?.addEventListener('click', checkout);

    async function scanCurrentBarcode() {
        const barcode = barcodeInput.value.trim();
        if (!barcode) return;
        setMessage('Searching product...');
        try {
            const response = await fetch(config.scanUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrf},
                body: JSON.stringify({barcode}),
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Product not found.');
            addToCart(data.product);
            setMessage(`Added ${data.product.name}.`, 'text-success');
        } catch (error) {
            setMessage(error.message, 'text-danger');
        } finally {
            barcodeInput.value = '';
            barcodeInput.focus();
        }
    }

    function addToCart(product) {
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
        barcodeInput.focus();
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
                    <td>${escapeHtml(item.barcode)}</td>
                    <td>${escapeHtml(config.currency)} ${money(item.price)}</td>
                    <td><input type="number" min="${wholeQuantity(item) ? 1 : 0.001}" max="${item.stock}" step="${wholeQuantity(item) ? 1 : 0.001}" value="${item.qty}" data-cart-qty="${escapeHtml(item.key)}" style="width:105px;"></td>
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
})();
