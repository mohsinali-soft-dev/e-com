@extends('admin.layout')

@section('title', 'POS')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">Checkout</div>
        <h1>Point of Sale</h1>
        <p>Scan a generated product barcode and complete the customer’s sale.</p>
    </div>
    <a class="btn btn-light" href="{{ route('admin.sales.index') }}">View Sales</a>
</div>

<div class="pos-layout">
    <div>
        <div class="card">
            <label for="barcodeInput">Barcode Scanner</label>
            <div class="search-row">
                <input id="barcodeInput" type="text" placeholder="Scan barcode and press Enter" autofocus autocomplete="off">
                <button class="btn" type="button" onclick="scanCurrentBarcode()">Add Product</button>
                <button class="btn btn-light" type="button" onclick="clearCart()">Clear</button>
            </div>
            <p id="posMessage" class="muted" aria-live="polite" style="margin-bottom:0;"></p>
        </div>

        <div class="card" style="margin-top:16px;">
            <div class="page-head">
                <div><div class="eyebrow">Current cart</div><h2 style="margin:4px 0;">Invoice Items</h2></div>
                <span class="badge"><span id="cartCount">0</span>&nbsp;products</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Product</th><th>Barcode</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead>
                    <tbody id="cartBody"><tr><td colspan="6" class="empty-state">No items scanned yet.</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>

    <aside class="card pos-summary">
        <div class="eyebrow">Payment</div>
        <h2 style="margin:4px 0 14px;">Bill Summary</h2>
        <div class="summary-row"><span>Subtotal</span><strong>{{ $adminSetting->currency }} <span id="subtotal">0.00</span></strong></div>

        <label for="discount">Discount</label>
        <input id="discount" type="number" step="0.01" min="0" value="0">

        <div class="summary-row total"><span>Total</span><span>{{ $adminSetting->currency }} <span id="grandTotal">0.00</span></span></div>

        <label for="paidAmount">Paid / Received Amount</label>
        <input id="paidAmount" type="number" step="0.01" min="0" value="0">

        <div class="summary-row">
            <span>Change</span>
            <strong class="text-success">{{ $adminSetting->currency }} <span id="changeAmount">0.00</span></strong>
        </div>

        <label for="paymentMethod">Payment Method</label>
        <select id="paymentMethod">
            <option value="cash">Cash</option>
            <option value="card">Card</option>
            <option value="bank">Bank Transfer</option>
        </select>

        <label for="customerId">Customer (optional)</label>
        <select id="customerId">
            <option value="">Walk-in customer</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->phone ? '— '.$customer->phone : '' }}</option>
            @endforeach
        </select>

        <button id="checkoutButton" class="btn" type="button" onclick="checkout()" style="margin-top:18px;width:100%;">Complete Sale</button>
    </aside>
</div>

<script>
    let cart = [];
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
    const escapeHtml = value => String(value).replace(/[&<>"']/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[character]);

    barcodeInput.addEventListener('keydown', event => {
        if (event.key === 'Enter') {
            event.preventDefault();
            scanCurrentBarcode();
        }
    });
    discountInput.addEventListener('input', renderTotals);
    paidAmount.addEventListener('input', renderTotals);

    async function scanCurrentBarcode() {
        const barcode = barcodeInput.value.trim();
        if (!barcode) return;
        posMessage.className = 'muted';
        posMessage.textContent = 'Searching product...';
        try {
            const response = await fetch('{{ route('admin.pos.scan') }}', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({barcode})
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Product not found.');
            addToCart(data.product);
            posMessage.className = 'text-success';
            posMessage.textContent = `Added ${data.product.name}.`;
        } catch (error) {
            posMessage.className = 'text-danger';
            posMessage.textContent = error.message;
        } finally {
            barcodeInput.value = '';
            barcodeInput.focus();
        }
    }

    function addToCart(product) {
        const key = `${product.id}:${product.variant_id || 0}`;
        const existing = cart.find(item => item.key === key);
        if (existing) {
            if (existing.qty + 1 > product.stock) {
                posMessage.className = 'text-danger';
                posMessage.textContent = `Only ${product.stock} ${product.unit || 'units'} available.`;
                return;
            }
            existing.qty += 1;
        } else {
            cart.push({...product, key, price: Number(product.price), stock: Number(product.stock), qty: 1});
        }
        renderCart();
    }

    function updateQty(key, value) {
        const item = cart.find(item => item.key === key);
        if (!item) return;
        const quantity = Math.max(0.001, Math.min(Number(value || 1), item.stock));
        item.qty = quantity;
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
        if (showMessage) posMessage.textContent = 'Cart cleared.';
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
                    <td><strong>${escapeHtml(item.name)}</strong><br><small>${escapeHtml(item.sku)} · Stock ${item.stock}</small></td>
                    <td>${escapeHtml(item.barcode)}</td>
                    <td>{{ $adminSetting->currency }} ${money(item.price)}</td>
                    <td><input type="number" min="0.001" max="${item.stock}" step="${item.sale_type === 'piece' ? '1' : '0.001'}" value="${item.qty}" onchange="updateQty('${item.key}', this.value)" style="width:105px;"></td>
                    <td><strong>{{ $adminSetting->currency }} ${money(item.price * item.qty)}</strong></td>
                    <td><button class="btn btn-light" type="button" onclick="removeItem('${item.key}')">Remove</button></td>
                </tr>`).join('');
        }
        cartCount.textContent = cart.length;
        renderTotals();
    }

    async function checkout() {
        if (!cart.length) {
            posMessage.className = 'text-danger';
            posMessage.textContent = 'Add at least one product before checkout.';
            return;
        }
        const value = totals();
        if (value.paid < value.total) {
            posMessage.className = 'text-danger';
            posMessage.textContent = 'Paid amount is less than the total amount.';
            paidAmount.focus();
            return;
        }

        checkoutButton.disabled = true;
        checkoutButton.textContent = 'Saving Sale...';
        try {
            const response = await fetch('{{ route('admin.pos.checkout') }}', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({
                    items: cart,
                    discount: value.discount,
                    paid_amount: value.paid,
                    payment_method: paymentMethod.value,
                    customer_id: customerId.value || null
                })
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                const validationMessage = data.errors ? Object.values(data.errors).flat()[0] : null;
                throw new Error(validationMessage || data.message || 'Checkout failed.');
            }
            clearCart(false);
            posMessage.className = 'text-success';
            posMessage.innerHTML = `Sale completed. Invoice <strong>${escapeHtml(data.invoice_no)}</strong>, change {{ $adminSetting->currency }} ${money(data.change_amount)}. <a href="${data.sale_url}">View invoice</a>`;
        } catch (error) {
            posMessage.className = 'text-danger';
            posMessage.textContent = error.message;
        } finally {
            checkoutButton.disabled = false;
            checkoutButton.textContent = 'Complete Sale';
        }
    }
</script>
@endsection
