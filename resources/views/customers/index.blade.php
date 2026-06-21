@extends('layouts.app')

@section('title', 'Customers — LPG Point')
@section('page-title', 'Customers')
@section('page-subtitle')
    Records → <span>Customer List</span>
@endsection

@section('topbar-actions')
    <form method="GET" action="{{ route('customers.index') }}" class="d-flex gap-2">
        <input
            type="search"
            name="q"
            value="{{ $search }}"
            @if ($search) autofocus @endif
            class="form-control form-control-sm"
            style="width: 180px;"
            placeholder="🔍 Naam ya phone..."
            id="customerSearchInput"
        >
        <select name="filter" class="form-select form-select-sm" style="width: 170px;" onchange="this.form.submit()">
            <option value="">Sab Customers</option>
            <option value="has_out" @selected($filter === 'has_out')>Cylinders Out</option>
            <option value="has_bal" @selected($filter === 'has_bal')>Udhaar Baaki</option>
            <option value="no_bal" @selected($filter === 'no_bal')>Zero Balance</option>
        </select>
    </form>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
        <i class="bi bi-person-plus-fill"></i> Add Customer
    </button>
@endsection

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <x-stat-card label="Total Customers" value="{{ $totalCustomers }}" icon="bi-people-fill" variant="white" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="Cylinders Out" value="{{ $cylindersOut }}" sub="Customers ke paas" icon="bi-send-fill" variant="accent" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="Total Udhaar" value="Rs {{ number_format($totalUdhaar) }}" sub="Outstanding balance" icon="bi-credit-card" variant="danger" />
        </div>
        <div class="col-6 col-lg-3">
            <x-stat-card label="Cash Customers" value="{{ $cashCustomers }}" sub="Zero balance" icon="bi-cash" variant="green" />
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people-fill text-primary"></i> Customer List</span>
                <span class="small text-secondary">{{ $customers->count() }} customers</span>
            </h5>
            <div class="tbl-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Cylinders Out</th>
                            <th>Total Returned</th>
                            <th>Udhaar Balance</th>
                            <th>Total Sales</th>
                            <th>Last Visit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="customer-avatar">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                                        <div>
                                            <div class="fw-semibold">{{ $customer->name }}</div>
                                            @if ($customer->address)
                                                <div class="small text-secondary">{{ $customer->address }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="small text-secondary">{{ $customer->phone }}</td>
                                <td>
                                    @if ($customer->cylinders_out > 0)
                                        <x-badge variant="primary">{{ $customer->cylinders_out }}</x-badge>
                                    @else
                                        <span class="text-secondary small">—</span>
                                    @endif
                                </td>
                                <td class="font-monospace text-success fw-semibold">0</td>
                                <td>
                                    <span class="font-monospace fw-bold {{ $customer->balance > 0 ? 'text-danger' : 'text-success' }}">
                                        Rs {{ number_format($customer->balance) }}
                                    </span>
                                </td>
                                <td class="font-monospace text-primary fw-semibold">Rs {{ number_format($customer->total_sales) }}</td>
                                <td class="small text-secondary">{{ $customer->last_visit_at?->format('d M Y') ?? '—' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn-icon" data-bs-toggle="modal" data-bs-target="#editCustomerModal-{{ $customer->id }}" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button type="button" class="btn-icon btn-icon-danger" data-bs-toggle="modal" data-bs-target="#deleteCustomerModal-{{ $customer->id }}" title="Delete">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-secondary py-4">Koi customer nahi mila</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('customers._add-modal')

    @foreach ($customers as $customer)
        <x-modal id="editCustomerModal-{{ $customer->id }}" title="Customer Edit Karein" icon="bi-pencil-fill">
            <form id="editCustomerForm-{{ $customer->id }}" method="POST" action="{{ route('customers.update', $customer) }}">
                @csrf
                @method('PUT')
                @include('customers._fields', ['customer' => $customer])
            </form>

            <x-slot:footer>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editCustomerForm-{{ $customer->id }}" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Customer</button>
            </x-slot:footer>
        </x-modal>

        <x-modal id="deleteCustomerModal-{{ $customer->id }}" title="Customer Delete?" icon="bi-trash3-fill" iconClass="text-danger">
            <p class="mb-0">Kya aap sure hain? <strong>{{ $customer->name }}</strong> delete ho jayega.</p>

            <x-slot:footer>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('customers.destroy', $customer) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash3-fill"></i> Delete Karo</button>
                </form>
            </x-slot:footer>
        </x-modal>
    @endforeach
@endsection

@section('scripts')
    <script>
        (function () {
            const input = document.getElementById('customerSearchInput');
            if (!input) return;
            let debounce;
            input.addEventListener('input', function () {
                clearTimeout(debounce);
                debounce = setTimeout(() => input.form.submit(), 400);
            });
        })();
    </script>
@endsection
