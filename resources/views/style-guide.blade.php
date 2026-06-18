@extends('layouts.app')

@section('title', 'Style Guide — LPG Point')
@section('page-title', 'Style Guide')
@section('page-subtitle', 'Sprint 2 — Layout & Design System (local-only)')

@section('topbar-actions')
    <span class="badge text-bg-success">● Live</span>
@endsection

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <x-stat-card label="Aaj ki Revenue" value="Rs 12,450" sub="6 transactions" icon="bi-currency-rupee" variant="accent" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="Cylinders Out" value="84" sub="Customers ke paas" icon="bi-send-fill" variant="blue" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="Refills Aaj" value="9" sub="Gas bharwaye" icon="bi-droplet-fill" variant="green" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="Low Stock Alert" value="2" sub="Items restock chahiye" icon="bi-exclamation-triangle-fill" variant="danger" />
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Badges</h5>
            <div class="d-flex flex-wrap gap-2">
                <x-badge variant="primary">Sale</x-badge>
                <x-badge variant="info">Return</x-badge>
                <x-badge variant="success">Refill</x-badge>
                <x-badge variant="secondary">Cash</x-badge>
                <x-badge variant="warning">Credit</x-badge>
                <x-badge variant="danger">Low Stock</x-badge>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Buttons</h5>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-primary"><i class="bi bi-cart-plus-fill"></i> Primary</button>
                <button class="btn btn-secondary">Secondary</button>
                <button class="btn btn-success">Success</button>
                <button class="btn btn-danger">Danger</button>
                <button class="btn btn-outline-secondary">Outline</button>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Mono / invoice number</h5>
            <p class="mb-0">
                Sample invoice no. <span class="font-monospace fw-semibold">INV-00001</span>
                &nbsp;·&nbsp;
                Amount <span class="font-monospace text-success fw-semibold">Rs 2,400</span>
            </p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#styleGuideModal">
                Open modal
            </button>
            <button type="button" class="btn btn-outline-secondary" id="styleGuideToastBtn">
                Show toast
            </button>
        </div>
    </div>

    <x-modal id="styleGuideModal" title="Bootstrap modal" icon="bi-info-circle-fill">
        This is Bootstrap's own Modal JS component, no hand-rolled <code>openModal()</code>.

        <x-slot:footer>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save</button>
        </x-slot:footer>
    </x-modal>
@endsection

@section('toasts')
    <x-toast variant="success" id="styleGuideToast" data-bs-autohide="false">
        Bootstrap's Toast JS component works too.
    </x-toast>
@endsection

@section('scripts')
    <script>
        document.getElementById('styleGuideToastBtn').addEventListener('click', function () {
            new bootstrap.Toast(document.getElementById('styleGuideToast')).show();
        });
    </script>
@endsection
