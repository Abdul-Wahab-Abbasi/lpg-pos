@php
    $navSections = [
        'Main' => [
            ['label' => 'Dashboard', 'icon' => 'bi-grid-fill', 'href' => '/', 'match' => '/'],
        ],
        'Sales' => [
            ['label' => 'New Sale', 'icon' => 'bi-cart-plus-fill', 'href' => '/sales/create', 'match' => 'sales/*'],
            ['label' => 'Return', 'icon' => 'bi-arrow-return-left', 'href' => '/returns/create', 'match' => 'returns/*'],
            ['label' => 'Refill', 'icon' => 'bi-droplet-fill', 'href' => '/refills/create', 'match' => 'refills/*'],
        ],
        'Records' => [
            ['label' => 'Transactions', 'icon' => 'bi-receipt', 'href' => '/transactions', 'match' => 'transactions*'],
            ['label' => 'Customers', 'icon' => 'bi-people-fill', 'href' => '/customers', 'match' => 'customers*'],
        ],
        'Stock' => [
            ['label' => 'Inventory', 'icon' => 'bi-box-seam-fill', 'href' => '/inventory', 'match' => 'inventory*'],
            ['label' => 'Products', 'icon' => 'bi-tag-fill', 'href' => '/products', 'match' => 'products*'],
        ],
        '' => [
            ['label' => 'Reports', 'icon' => 'bi-bar-chart-fill', 'href' => '/reports', 'match' => 'reports*'],
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'LPG Point')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">🔥</div>
            <div class="sidebar-logo-text">
                <div class="name">LPG Point</div>
                <div class="subtitle">Gas Shop POS</div>
            </div>
        </div>

        <div>
            @foreach ($navSections as $section => $items)
                @if ($section !== '')
                    <div class="sidebar-section">{{ $section }}</div>
                @endif
                @foreach ($items as $item)
                    <a href="{{ $item['href'] }}" class="sidebar-link @if (request()->is($item['match'])) active @endif">
                        <i class="bi {{ $item['icon'] }}"></i>{{ $item['label'] }}
                    </a>
                @endforeach
            @endforeach
        </div>

        <div class="sidebar-footer">
            <div class="d-flex align-items-center gap-2">
                <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'O', 0, 1)) }}</div>
                <div>
                    <div class="sidebar-username">{{ auth()->user()?->name ?? 'Owner' }}</div>
                    <div class="sidebar-userrole">Owner</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                    @csrf
                    <button type="submit" class="sidebar-logout" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h1 class="fs-3 fw-bold mb-0">@yield('page-title', 'LPG Point')</h1>
                @hasSection('page-subtitle')
                    <div class="topbar-subtitle">@yield('page-subtitle')</div>
                @endif
            </div>
            <div class="d-flex gap-2 align-items-center">
                @yield('topbar-actions')
            </div>
        </div>

        @yield('content')
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        @foreach (['success', 'danger', 'warning', 'info'] as $variant)
            @if (session()->has($variant))
                <x-toast :variant="$variant" class="toast-autoshow">{{ session($variant) }}</x-toast>
            @endif
        @endforeach
        @yield('toasts')
    </div>

    @yield('scripts')
</body>
</html>
