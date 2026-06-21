<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $filter = $request->query('filter', '');

        $customers = Customer::withSum('cylinderBalances as cylinders_out', 'qty_out')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($filter === 'has_out', fn ($query) => $query->whereHas('cylinderBalances', fn ($q) => $q->where('qty_out', '>', 0)))
            ->when($filter === 'has_bal', fn ($query) => $query->where('balance', '>', 0))
            ->when($filter === 'no_bal', fn ($query) => $query->where('balance', '<=', 0))
            ->orderBy('name')
            ->get();

        $allCustomers = Customer::withSum('cylinderBalances as cylinders_out', 'qty_out')->get();

        return view('customers.index', [
            'customers' => $customers,
            'search' => $search,
            'filter' => $filter,
            'totalCustomers' => $allCustomers->count(),
            'cylindersOut' => (int) $allCustomers->sum('cylinders_out'),
            'totalUdhaar' => $allCustomers->sum('balance'),
            'cashCustomers' => $allCustomers->filter(fn (Customer $c) => $c->balance <= 0)->count(),
        ]);
    }

    public function store(CustomerRequest $request): RedirectResponse
    {
        $customer = Customer::create($request->validated());

        return back()->with('success', $customer->name.' add ho gaya!');
    }

    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return back()->with('success', $customer->name.' update ho gaya!');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $name = $customer->name;
        $customer->delete();

        return back()->with('success', $name.' delete ho gaya!');
    }
}
