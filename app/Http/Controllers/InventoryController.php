<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestockRequest;
use App\Http\Requests\UpdateLevelsRequest;
use App\Http\Requests\UpdatePriceRequest;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $products = Product::orderBy('name')->get();

        return view('inventory.index', [
            'products' => $products,
            'lowStockCount' => $products->filter(fn (Product $product) => $product->is_low_stock)->count(),
        ]);
    }

    public function restock(RestockRequest $request, Product $product): RedirectResponse
    {
        DB::transaction(function () use ($request, $product) {
            $product->increment('qty', $request->integer('qty'));

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'RESTOCK',
                'qty_change' => $request->integer('qty'),
                'qty_after' => $product->qty,
                'note' => $request->note,
                'created_by' => auth()->id(),
            ]);
        });

        return back()->with('success', $request->integer('qty').' cylinders stock mein add ho gaye!');
    }

    public function updatePrice(UpdatePriceRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return back()->with('success', 'Prices update ho gayi!');
    }

    public function updateLevels(UpdateLevelsRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return back()->with('success', 'Stock levels update ho gaye!');
    }
}
