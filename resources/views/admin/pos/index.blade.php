@extends('admin.layout')

@section('title', 'POS')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">Checkout</div>
        <h1>POS Billing</h1>
        <p>Scan barcode or type barcode manually, then press Enter.</p>
    </div>
</div>

<div class="form-grid" style="align-items:start;">
    <div class="card">
        <label>Barcode Scanner Input</label>
        <input id="barcodeInput" type="text" placeholder="Scan barcode here" autofocus autocomplete="off">
        <p id="posMessage" style="margin-bottom:0;"></p>

        <div style="margin-top:18px;display:flex;gap:10px;flex-wrap:wrap;">
            <button class="btn" type="button" onclick="focusScanner()">Focus Scanner</button>
            <button class="btn btn-light" type="button" onclick="clearCart()">Clear Cart</button>
        </div>
    </div>

    <div class="card">
        <div class="stat-label">Current Bill Total</div>
        <div class="stat">Rs. <span id="grandTotal">0.00</span></div>
        <p><span id="cartCount">0</span> item(s) in cart</p>

        <label>Paid Amount</label>
        <input id="paidAmount" type="number" step="0.01" value="0">

        <label>Payment Method</label>
        <select id="paymentMethod">
            <option value="cash">Cash</option>
            <option value="card">Card</option>
            <option value="bank">Bank Transfer</option>
        </select>

        <button class="btn" type="button" onclick="checkout()" style="margin-top:16px;width:100%;">Complete Sale</button>
    </div>
</div>

<div class="card" style="margin-top:16px;">
    <div class="table-wrap">
        <table>
            <thead>
            <tr><th>Product</th><th>Barcode</th><th>Price</th><th>Qty</th><th>Total</th><th>Action</th></tr>
            </thead>
            <tbody id="cartBody"><tr><td colspan="6">No items scanned yet.</td></tr></tbody>
        </table>
    </div>
</div>

<script>
    let cart = [];
    const barcodeInput = document.getElementById('barcodeInput');
    const posMessage = document.getElementById('posMessage');
    const cartBody = document.getElementById('cartBody');
    const grandTotal = document.getElementById('grandTotal');
    const cartCount = document.getElementById('cartCount');
    const paidAmount = document.getElementById('paidAmount');
    const paymentMethod = document.getElementById('paymentMethod');

    function focusScanner() { barcodeInput.focus(); }

    barcodeInput.addEventListener('keydown', async function (event) {
        if (event.key !== 'Enter') return;
        event.preventDefault();
        const barcode = barcodeInput.value.trim();
        if (!barcode) return;
        await scanBarcode(barcode);
        barcodeInput.value = '';
        focusScanner();
    });

    async function scanBarcode(barcode) {
        posMessage.textContent = 'Searching product...';
        try {
            const response = await fetch('{{ route('admin.pos.scan') }}', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({ barcode })
            });
            const data = await response.json();
            if (!response.ok || !data.success) { posMessage.textContent = data.message || 'Product not found.'; return; }
            addToCart(data.product);
            posMessage.textContent = 'Added: ' + data.product.name;
        } catch (error) { posMessage.textContent = 'Scan failed. Please try again.'; }
    }

    function addToCart(product) {
        const existing = cart.find(item => item.id === product.id);
        if (existing) existing.qty = Number(existing.qty) + 1;
        else cart.push({id: product.id, name: product.name, sku: product.sku, barcode: product.barcode, unit: product.unit || '', price: Number(product.price), qty: 1});
        renderCart();
    }

    function updateQty(productId, qty) {
        const item = cart.find(item => item.id === productId);
        if (!item) return;
        item.qty = Math.max(0.001, Number(qty || 1));
        renderCart();
    }

    function removeItem(productId) { cart = cart.filter(item => item.id !== productId); renderCart(); }
    function clearCart() { cart = []; renderCart(); paidAmount.value = 0; focusScanner(); }

    function currentTotal() { return cart.reduce((sum, item) => sum + (item.price * item.qty), 0); }

    function renderCart() {
        if (cart.length === 0) {
            cartBody.innerHTML = '<tr><td colspan="6">No items scanned yet.</td></tr>';
            grandTotal.textContent = '0.00'; cartCount.textContent = '0'; return;
        }
        let total = 0; cartBody.innerHTML = '';
        cart.forEach(item => {
            const lineTotal = item.price * item.qty; total += lineTotal;
            cartBody.innerHTML += `<tr><td><strong>${item.name}</strong><br><small>${item.sku}</small></td><td>${item.barcode}</td><td>Rs. ${item.price.toFixed(2)}</td><td><input type="number" min="0.001" step="0.001" value="${item.qty}" onchange="updateQty(${item.id}, this.value)" style="width:110px;"></td><td>Rs. ${lineTotal.toFixed(2)}</td><td><button class="btn btn-light" type="button" onclick="removeItem(${item.id})">Remove</button></td></tr>`;
        });
        grandTotal.textContent = total.toFixed(2); cartCount.textContent = cart.length; paidAmount.value = total.toFixed(2);
    }

    async function checkout() {
        if (cart.length === 0) { posMessage.textContent = 'Cart is empty.'; return; }
        posMessage.textContent = 'Saving sale...';
        const response = await fetch('{{ route('admin.pos.checkout') }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify({items: cart, paid_amount: Number(paidAmount.value), payment_method: paymentMethod.value})
        });
        const data = await response.json();
        if (!response.ok || !data.success) { posMessage.textContent = data.message || 'Checkout failed.'; return; }
        posMessage.textContent = `${data.message} Invoice: ${data.invoice_no}. Change: Rs. ${Number(data.change_amount).toFixed(2)}`;
        clearCart();
    }

    focusScanner();
</script>
@endsection
