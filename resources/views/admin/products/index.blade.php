@extends('admin.layout')

@section('title', 'Products')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">Catalog</div>
        <h1>Products</h1>
        <p>Manage items, variants, prices, stock, units and barcode labels.</p>
    </div>
    <a class="btn" href="{{ route('admin.products.create') }}">Add Product</a>
</div>

<form class="card search-row" method="GET" action="{{ route('admin.products.index') }}" data-live-search style="margin-bottom:16px;">
    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search name, SKU, barcode, category, brand or variant" autocomplete="off">
    <button class="btn" type="submit">Search</button>
    <div class="live-search-status" data-live-search-status aria-live="polite"></div>
</form>

<div data-live-results>
    <div class="product-index-table table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Barcode</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Variants</th>
                    <th>Status</th>
                    <th>Barcode Label</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr>
                    <td>
                        @if($product->image_path)
                            <img class="product-image" src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}">
                        @else
                            <div class="product-image product-image-empty">N/A</div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $product->name }}</strong><br>
                        <small>{{ $product->sku }}</small>
                    </td>
                    <td><strong>{{ $product->primaryBarcode?->barcode ?? '-' }}</strong></td>
                    <td>{{ $product->category?->name ?? '-' }}</td>
                    <td>{{ $product->unit?->short_name ?? '-' }}</td>
                    <td>{{ $adminSetting->currency }} {{ number_format($product->selling_price, 2) }}</td>
                    <td>
                        @if($product->has_variants)
                            {{ number_format($product->variants->sum('stock_quantity'), 2) }} total
                        @else
                            {{ number_format($product->stock_quantity, 2) }}
                        @endif
                    </td>
                    <td>
                        @if($product->variants_count > 0)
                            <button class="btn btn-light" type="button" data-variant-drawer-open="productVariants{{ $product->id }}">
                                {{ $product->variants_count }} variant(s)
                            </button>
                        @else
                            <span class="muted">No variants</span>
                        @endif
                    </td>
                    <td><span class="badge">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        @if($product->primaryBarcode)
                            <form action="{{ route('admin.barcode-labels.print') }}" method="POST" target="_blank" class="stack" style="flex-wrap:nowrap;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="number" name="quantity" min="1" max="200" value="1" aria-label="Label quantity" style="width:72px;">
                                <button class="btn btn-light" type="submit">Print</button>
                            </form>
                        @else
                            <span class="muted">No barcode</span>
                        @endif
                    </td>
                    <td>
                        <a class="btn btn-light" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                        <a class="btn btn-light" href="{{ route('admin.products.variants.index', $product) }}">Variants</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="11" class="empty-state">No products found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="product-card-grid">
        @forelse($products as $product)
            <article class="product-card">
                <div class="product-card-main">
                    @if($product->image_path)
                        <img class="product-card-image" src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}">
                    @else
                        <div class="product-card-image product-image-empty">N/A</div>
                    @endif
                    <div class="product-card-title">
                        <strong>{{ $product->name }}</strong>
                        <span>{{ $product->sku }}</span>
                    </div>
                    <span class="badge">{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
                </div>

                <div class="product-card-meta">
                    <div><span>Barcode</span><strong>{{ $product->primaryBarcode?->barcode ?? '-' }}</strong></div>
                    <div><span>Category</span><strong>{{ $product->category?->name ?? '-' }}</strong></div>
                    <div><span>Unit</span><strong>{{ $product->unit?->short_name ?? '-' }}</strong></div>
                    <div><span>Price</span><strong>{{ $adminSetting->currency }} {{ number_format($product->selling_price, 2) }}</strong></div>
                    <div><span>Stock</span><strong>{{ $product->has_variants ? number_format($product->variants->sum('stock_quantity'), 2).' total' : number_format($product->stock_quantity, 2) }}</strong></div>
                    <div><span>Variants</span><strong>{{ $product->variants_count }}</strong></div>
                </div>

                <div class="product-card-actions">
                    <a class="btn btn-light" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                    <a class="btn btn-light" href="{{ route('admin.products.variants.index', $product) }}">Variants</a>
                    @if($product->variants_count > 0)
                        <button class="btn btn-light" type="button" data-variant-drawer-open="productVariants{{ $product->id }}">View Variants</button>
                    @endif
                    @if($product->primaryBarcode)
                        <form action="{{ route('admin.barcode-labels.print') }}" method="POST" target="_blank" class="product-card-print">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="number" name="quantity" min="1" max="200" value="1" aria-label="Label quantity">
                            <button class="btn" type="submit">Print Barcode</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="card empty-state">No products found.</div>
        @endforelse
    </div>

    @foreach($products as $product)
        @if($product->variants_count > 0)
            <section class="variant-drawer" id="productVariants{{ $product->id }}" aria-hidden="true" aria-label="{{ $product->name }} variants">
                <div class="variant-drawer-backdrop" data-variant-drawer-close></div>
                <aside class="variant-drawer-panel" role="dialog" aria-modal="true" aria-labelledby="productVariants{{ $product->id }}Title">
                    <div class="variant-drawer-head">
                        <div>
                            <div class="eyebrow">Variants</div>
                            <h2 id="productVariants{{ $product->id }}Title">{{ $product->name }}</h2>
                        </div>
                        <button class="sidebar-close" type="button" aria-label="Close variants" data-variant-drawer-close>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="variant-drawer-list">
                        @foreach($product->variants as $variant)
                            <article class="variant-card">
                                <div class="variant-card-head">
                                    <div>
                                        <strong>{{ $variant->name }}</strong>
                                        <span>{{ $variant->sku }}</span>
                                    </div>
                                    <span class="badge">{{ $variant->is_active ? 'Active' : 'Inactive' }}</span>
                                </div>
                                <div class="variant-card-meta">
                                    <div><span>Barcode</span><strong>{{ $variant->primaryBarcode?->barcode ?? '-' }}</strong></div>
                                    <div><span>Price</span><strong>{{ $adminSetting->currency }} {{ number_format($variant->selling_price, 2) }}</strong></div>
                                    <div><span>Stock</span><strong>{{ number_format($variant->stock_quantity, 2) }}</strong></div>
                                </div>
                                @if($variant->primaryBarcode)
                                    <form action="{{ route('admin.barcode-labels.print') }}" method="POST" target="_blank" class="variant-card-print">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">
                                        <input type="number" name="quantity" min="1" max="200" value="1" aria-label="Variant label quantity">
                                        <button class="btn btn-light" type="submit">Print Barcode</button>
                                    </form>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </aside>
            </section>
        @endif
    @endforeach

    <div style="margin-top:16px;">{{ $products->links() }}</div>
</div>
@endsection
