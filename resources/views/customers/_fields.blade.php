@php
    $customer ??= null;
@endphp

<div class="mb-3">
    <label class="form-label">Naam *</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $customer->name ?? '') }}" placeholder="Customer ka naam">
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label class="form-label">Phone *</label>
    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
        value="{{ old('phone', $customer->phone ?? '') }}" placeholder="03xx-xxxxxxx">
    @error('phone')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label class="form-label">Address</label>
    <input type="text" name="address" class="form-control" value="{{ old('address', $customer->address ?? '') }}" placeholder="Ghar ka pata">
</div>
<div class="mb-0">
    <label class="form-label">Notes</label>
    <input type="text" name="note" class="form-control" value="{{ old('note', $customer->note ?? '') }}" placeholder="Koi khaas baat...">
</div>
