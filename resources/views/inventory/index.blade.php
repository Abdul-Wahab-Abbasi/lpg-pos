@extends('layouts.app')

@section('title', 'Inventory — LPG Point')
@section('page-title', 'Inventory')
@section('page-subtitle')
    Stock → <span>Cylinder Stock</span>
@endsection

@section('topbar-actions')
    @if ($lowStockCount > 0)
        <x-badge variant="danger" class="px-3 py-2">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ $lowStockCount }} Low Stock Item{{ $lowStockCount > 1 ? 's' : '' }}
        </x-badge>
    @endif
@endsection

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-4">
            <x-stat-card label="Total Products" value="{{ $products->count() }}" icon="bi-tag-fill" variant="white" />
        </div>
        <div class="col-6 col-lg-4">
            <x-stat-card label="Total In Stock" value="{{ $products->sum('qty') }}" sub="Godam mein" icon="bi-box-seam-fill" variant="green" />
        </div>
        <div class="col-6 col-lg-4">
            <x-stat-card label="Low Stock" value="{{ $lowStockCount }}" sub="Restock chahiye" icon="bi-exclamation-triangle-fill" variant="danger" />
        </div>
    </div>

    <div class="row g-3">
        @forelse ($products as $product)
            <div class="col-md-6 col-lg-4">
                <div class="inventory-card @if ($product->is_low_stock) inventory-card-low @endif">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-bold text-white">{{ $product->name }}</div>
                            <div class="small text-secondary mt-1">{{ $product->category }} • {{ $product->unit }}</div>
                        </div>
                        @if ($product->is_low_stock)
                            <x-badge variant="danger"><i class="bi bi-exclamation-triangle-fill"></i> Low</x-badge>
                        @endif
                    </div>

                    @php
                        $stockColor = $product->is_low_stock ? 'var(--lpg-danger)' : ($product->stock_percent < 50 ? 'var(--lpg-warn)' : 'var(--lpg-green)');
                        $progressClass = $product->is_low_stock ? 'bg-danger' : ($product->stock_percent < 50 ? 'bg-warning' : 'bg-success');
                    @endphp

                    <div class="inventory-card-value" style="color: {{ $stockColor }}">{{ $product->qty }}</div>
                    <div class="small text-secondary mt-1">Max: {{ $product->max_qty }} | Min Alert: {{ $product->min_qty }}</div>

                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar {{ $progressClass }}" style="width: {{ $product->stock_percent }}%"></div>
                    </div>
                    <div class="small text-secondary mt-1">{{ $product->stock_percent }}% stocked</div>

                    <div class="inventory-price-row">
                        <div class="inventory-price-chip"><div class="label">Sale</div><div class="value">Rs {{ number_format($product->sale_price) }}</div></div>
                        <div class="inventory-price-chip"><div class="label">Refill</div><div class="value">Rs {{ number_format($product->refill_charge) }}</div></div>
                        <div class="inventory-price-chip"><div class="label">Deposit</div><div class="value">Rs {{ number_format($product->return_deposit) }}</div></div>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#restockModal-{{ $product->id }}">
                            <i class="bi bi-plus-circle-fill"></i> Restock
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#priceModal-{{ $product->id }}">
                            <i class="bi bi-pencil"></i> Price
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#levelsModal-{{ $product->id }}">
                            <i class="bi bi-sliders"></i> Levels
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-secondary py-5">Koi product nahi. Pehle Products page pe add karein.</div>
        @endforelse
    </div>

    @foreach ($products as $product)
        <x-modal id="restockModal-{{ $product->id }}" title="Restock Karein" icon="bi-plus-circle-fill">
            <form id="restockForm-{{ $product->id }}" method="POST" action="{{ route('inventory.restock', $product) }}">
                @csrf
                <div class="bg-body-tertiary rounded p-3 mb-3">
                    <div class="small text-secondary mb-1">Product</div>
                    <div class="fw-bold text-white">{{ $product->name }}</div>
                    <div class="d-flex gap-4 mt-2">
                        <div>
                            <div class="small text-secondary">Current Stock</div>
                            <div class="font-monospace text-primary fw-bold">{{ $product->qty }}</div>
                        </div>
                        <div>
                            <div class="small text-secondary">Min Level</div>
                            <div class="font-monospace fw-semibold">{{ $product->min_qty }}</div>
                        </div>
                        <div>
                            <div class="small text-secondary">Max Capacity</div>
                            <div class="font-monospace fw-semibold">{{ $product->max_qty }}</div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kitne Cylinder Aaye? *</label>
                    <input type="number" name="qty" min="1" class="form-control" placeholder="0">
                </div>
                <div class="mb-0">
                    <label class="form-label">Note (Optional)</label>
                    <input type="text" name="note" class="form-control" placeholder="Supplier ka naam ya koi baat...">
                </div>
            </form>

            <x-slot:footer>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="restockForm-{{ $product->id }}" class="btn btn-success"><i class="bi bi-plus-circle-fill"></i> Restock Karo</button>
            </x-slot:footer>
        </x-modal>

        <x-modal id="priceModal-{{ $product->id }}" title="Price Update Karein" icon="bi-pencil-fill">
            <form id="priceForm-{{ $product->id }}" method="POST" action="{{ route('inventory.price', $product) }}">
                @csrf
                @method('PATCH')
                <div class="fw-semibold text-white mb-3">{{ $product->name }}</div>
                <div class="mb-3">
                    <label class="form-label">Sale Price (Rs)</label>
                    <input type="number" name="sale_price" class="form-control" value="{{ $product->sale_price }}" placeholder="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">Refill Charge (Rs)</label>
                    <input type="number" name="refill_charge" class="form-control" value="{{ $product->refill_charge }}" placeholder="0">
                </div>
                <div class="mb-0">
                    <label class="form-label">Return Deposit (Rs)</label>
                    <input type="number" name="return_deposit" class="form-control" value="{{ $product->return_deposit }}" placeholder="0">
                </div>
            </form>

            <x-slot:footer>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="priceForm-{{ $product->id }}" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Karein</button>
            </x-slot:footer>
        </x-modal>

        <x-modal id="levelsModal-{{ $product->id }}" title="Stock Levels Set Karein" icon="bi-sliders">
            <form id="levelsForm-{{ $product->id }}" method="POST" action="{{ route('inventory.levels', $product) }}">
                @csrf
                @method('PATCH')
                <div class="fw-semibold text-white mb-3">{{ $product->name }}</div>
                <div class="mb-3">
                    <label class="form-label">Minimum Qty (Alert Level)</label>
                    <input type="number" name="min_qty" min="0" class="form-control" value="{{ $product->min_qty }}" placeholder="0">
                </div>
                <div class="mb-0">
                    <label class="form-label">Maximum Capacity</label>
                    <input type="number" name="max_qty" min="1" class="form-control" value="{{ $product->max_qty }}" placeholder="0">
                </div>
            </form>

            <x-slot:footer>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="levelsForm-{{ $product->id }}" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save</button>
            </x-slot:footer>
        </x-modal>
    @endforeach
@endsection
