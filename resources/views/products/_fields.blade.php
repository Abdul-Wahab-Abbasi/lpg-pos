@php
    $product ??= null;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Product Name *</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $product->name ?? '') }}" placeholder="e.g. 11 KG LPG Cylinder">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control" value="{{ old('category', $product->category ?? '') }}" placeholder="e.g. Cylinder">
    </div>
    <div class="col-md-4">
        <label class="form-label">Sale Price (Rs) *</label>
        <input type="number" name="sale_price" class="form-control @error('sale_price') is-invalid @enderror"
            value="{{ old('sale_price', $product->sale_price ?? '') }}" placeholder="0">
        @error('sale_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Refill Charge (Rs)</label>
        <input type="number" name="refill_charge" class="form-control" value="{{ old('refill_charge', $product->refill_charge ?? '') }}" placeholder="0">
    </div>
    <div class="col-md-4">
        <label class="form-label">Return Deposit</label>
        <input type="number" name="return_deposit" class="form-control" value="{{ old('return_deposit', $product->return_deposit ?? '') }}" placeholder="0">
    </div>
    <div class="col-12">
        <label class="form-label">Unit</label>
        <select name="unit" class="form-select">
            @foreach (['pcs' => 'pcs (pieces)', 'kg' => 'kg', 'ltr' => 'ltr'] as $value => $label)
                <option value="{{ $value }}" @selected(old('unit', $product->unit ?? 'pcs') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>
