@extends('layouts.app')

@section('title', 'Dashboard — LPG Point')
@section('page-title', 'Dashboard')
@section('page-subtitle', now()->format('l, j F Y'))

@section('topbar-actions')
    <x-badge variant="success">● Live</x-badge>
    <a href="/sales/create" class="btn btn-primary"><i class="bi bi-cart-plus-fill"></i> New Sale</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body text-center text-secondary py-5">
            <i class="bi bi-grid-fill" style="font-size: 2.5rem; color: var(--lpg-border);"></i>
            <p class="mt-3 mb-0">Dashboard abhi under construction hai — Sprint 8 mein aaj ki revenue, cylinders out, recent transactions aur stock overview yahan dikhenge.</p>
        </div>
    </div>
@endsection
