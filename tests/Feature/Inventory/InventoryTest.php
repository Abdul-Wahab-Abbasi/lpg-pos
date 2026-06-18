<?php

use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('inventory index can be rendered', function () {
    Product::factory()->create(['name' => '11 KG LPG Cylinder']);

    $response = $this->get('/inventory');

    $response->assertStatus(200);
    $response->assertSee('11 KG LPG Cylinder');
});

test('restocking increases qty and writes a stock movement', function () {
    $product = Product::factory()->create(['qty' => 10, 'max_qty' => 50]);

    $response = $this->post("/inventory/{$product->id}/restock", [
        'qty' => 15,
        'note' => 'Supplier delivery',
    ]);

    $response->assertRedirect();
    expect($product->refresh()->qty)->toBe(25);

    $this->assertDatabaseHas('stock_movements', [
        'product_id' => $product->id,
        'type' => 'RESTOCK',
        'qty_change' => 15,
        'qty_after' => 25,
        'note' => 'Supplier delivery',
    ]);
});

test('restock requires a positive quantity', function () {
    $product = Product::factory()->create(['qty' => 10]);

    $response = $this->post("/inventory/{$product->id}/restock", ['qty' => 0]);

    $response->assertSessionHasErrors('qty');
    expect($product->refresh()->qty)->toBe(10);
});

test('prices can be updated from the inventory screen', function () {
    $product = Product::factory()->create();

    $response = $this->patch("/inventory/{$product->id}/price", [
        'sale_price' => 5000,
        'refill_charge' => 1500,
        'return_deposit' => 900,
    ]);

    $response->assertRedirect();
    expect((float) $product->refresh()->sale_price)->toBe(5000.0);
});

test('stock levels can be updated from the inventory screen', function () {
    $product = Product::factory()->create(['min_qty' => 2, 'max_qty' => 20]);

    $response = $this->patch("/inventory/{$product->id}/levels", [
        'min_qty' => 5,
        'max_qty' => 40,
    ]);

    $response->assertRedirect();
    $product->refresh();
    expect($product->min_qty)->toBe(5);
    expect($product->max_qty)->toBe(40);
});

test('a product under 25 percent of max qty is flagged low stock', function () {
    $product = Product::factory()->create(['qty' => 10, 'max_qty' => 50]);
    expect($product->is_low_stock)->toBeTrue();

    $product = Product::factory()->create(['qty' => 13, 'max_qty' => 50]);
    expect($product->is_low_stock)->toBeFalse();
});
