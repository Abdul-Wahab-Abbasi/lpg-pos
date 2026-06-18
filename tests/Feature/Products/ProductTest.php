<?php

use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('products list can be rendered', function () {
    Product::factory()->create(['name' => '11 KG LPG Cylinder']);

    $response = $this->get('/products');

    $response->assertStatus(200);
    $response->assertSee('11 KG LPG Cylinder');
});

test('a product can be created', function () {
    $response = $this->post('/products', [
        'name' => '19 KG LPG Cylinder',
        'category' => 'Cylinder',
        'sale_price' => 4200,
        'refill_charge' => 1200,
        'return_deposit' => 700,
        'unit' => 'pcs',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('products', [
        'name' => '19 KG LPG Cylinder',
        'qty' => 0,
        'min_qty' => 2,
        'max_qty' => 50,
    ]);
});

test('creating a product requires a name and sale price', function () {
    $response = $this->post('/products', []);

    $response->assertSessionHasErrors(['name', 'sale_price']);
});

test('a product can be updated', function () {
    $product = Product::factory()->create(['name' => 'Old Name']);

    $response = $this->put("/products/{$product->id}", [
        'name' => 'New Name',
        'sale_price' => 3000,
        'unit' => 'pcs',
    ]);

    $response->assertRedirect();
    expect($product->refresh()->name)->toBe('New Name');
});

test('a product can be deleted', function () {
    $product = Product::factory()->create();

    $response = $this->delete("/products/{$product->id}");

    $response->assertRedirect();
    $this->assertModelMissing($product);
});
