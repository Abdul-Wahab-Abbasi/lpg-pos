@extends('layouts.app')

@section('title', 'Products — LPG Point')
@section('page-title', 'Products')
@section('page-subtitle')
    Stock → <span>Cylinder Types</span>
@endsection

@section('topbar-actions')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
        <i class="bi bi-plus-lg"></i> Add Product
    </button>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="bi bi-tag-fill text-primary"></i> Sab Products</h5>
            <div class="tbl-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Sale Price</th>
                            <th>Refill Charge</th>
                            <th>Return Deposit</th>
                            <th>Unit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td class="font-monospace text-secondary small">{{ $product->id }}</td>
                                <td class="fw-semibold">{{ $product->name }}</td>
                                <td><x-badge variant="primary">{{ $product->category }}</x-badge></td>
                                <td class="font-monospace text-primary fw-semibold">Rs {{ number_format($product->sale_price) }}</td>
                                <td class="font-monospace text-info">Rs {{ number_format($product->refill_charge) }}</td>
                                <td class="font-monospace text-secondary">Rs {{ number_format($product->return_deposit) }}</td>
                                <td class="text-secondary">{{ $product->unit }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn-icon" data-bs-toggle="modal" data-bs-target="#editProductModal-{{ $product->id }}" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button type="button" class="btn-icon btn-icon-danger" data-bs-toggle="modal" data-bs-target="#deleteProductModal-{{ $product->id }}" title="Delete">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-secondary py-4">Koi product nahi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modal id="addProductModal" title="Naya Product Add Karein" icon="bi-tag-fill">
        <form id="addProductForm" method="POST" action="{{ route('products.store') }}">
            @csrf
            @include('products._fields', ['product' => null])
        </form>

        <x-slot:footer>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" form="addProductForm" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Product</button>
        </x-slot:footer>
    </x-modal>

    @foreach ($products as $product)
        <x-modal id="editProductModal-{{ $product->id }}" title="Product Edit Karein" icon="bi-pencil-fill">
            <form id="editProductForm-{{ $product->id }}" method="POST" action="{{ route('products.update', $product) }}">
                @csrf
                @method('PUT')
                @include('products._fields', ['product' => $product])
            </form>

            <x-slot:footer>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editProductForm-{{ $product->id }}" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Product</button>
            </x-slot:footer>
        </x-modal>

        <x-modal id="deleteProductModal-{{ $product->id }}" title="Product Delete?" icon="bi-trash3-fill" iconClass="text-danger">
            <p class="mb-0">Kya aap sure hain? <strong>{{ $product->name }}</strong> delete ho jayega.</p>

            <x-slot:footer>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('products.destroy', $product) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash3-fill"></i> Delete Karo</button>
                </form>
            </x-slot:footer>
        </x-modal>
    @endforeach
@endsection
