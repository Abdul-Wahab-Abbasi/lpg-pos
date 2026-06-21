<?php

use App\Models\Customer;
use App\Models\CustomerCylinderBalance;
use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('customers list can be rendered', function () {
    Customer::factory()->create(['name' => 'Tariq Mehmood']);

    $response = $this->get('/customers');

    $response->assertStatus(200);
    $response->assertSee('Tariq Mehmood');
});

test('a customer can be added', function () {
    $response = $this->post('/customers', [
        'name' => 'Saima Khan',
        'phone' => '0300-1234567',
        'address' => 'Model Town',
        'note' => 'Regular customer',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('customers', [
        'name' => 'Saima Khan',
        'phone' => '0300-1234567',
        'balance' => 0,
        'total_sales' => 0,
    ]);
});

test('adding a customer requires a name and phone', function () {
    $response = $this->post('/customers', []);

    $response->assertSessionHasErrors(['name', 'phone']);
});

test('a customer phone number must be unique', function () {
    Customer::factory()->create(['phone' => '0300-1234567']);

    $response = $this->post('/customers', [
        'name' => 'Another Customer',
        'phone' => '0300-1234567',
    ]);

    $response->assertSessionHasErrors('phone');
});

test('a customer can be updated', function () {
    $customer = Customer::factory()->create(['name' => 'Old Name', 'phone' => '0300-1111111']);

    $response = $this->put("/customers/{$customer->id}", [
        'name' => 'New Name',
        'phone' => '0300-1111111',
        'address' => 'New Address',
    ]);

    $response->assertRedirect();
    expect($customer->refresh()->name)->toBe('New Name');
    expect($customer->address)->toBe('New Address');
});

test('updating a customer keeps their own phone number valid', function () {
    $customer = Customer::factory()->create(['phone' => '0300-2222222']);

    $response = $this->put("/customers/{$customer->id}", [
        'name' => $customer->name,
        'phone' => '0300-2222222',
    ]);

    $response->assertSessionDoesntHaveErrors('phone');
});

test('a customer can be deleted', function () {
    $customer = Customer::factory()->create();

    $response = $this->delete("/customers/{$customer->id}");

    $response->assertRedirect();
    $this->assertModelMissing($customer);
});

test('customers can be searched by name or phone', function () {
    Customer::factory()->create(['name' => 'Ali Raza', 'phone' => '0311-1112222']);
    Customer::factory()->create(['name' => 'Bilal Ahmed', 'phone' => '0322-3334444']);

    $response = $this->get('/customers?q=Ali');

    $response->assertSee('Ali Raza');
    $response->assertDontSee('Bilal Ahmed');
});

test('customers can be filtered by outstanding balance', function () {
    Customer::factory()->create(['name' => 'Has Balance', 'balance' => 500]);
    Customer::factory()->create(['name' => 'Paid In Full', 'balance' => 0]);

    $response = $this->get('/customers?filter=has_bal');

    $response->assertSee('Has Balance');
    $response->assertDontSee('Paid In Full');
});

test('customers can be filtered by cylinders out', function () {
    $product = Product::factory()->create();
    $withOut = Customer::factory()->create(['name' => 'Has Cylinders Out']);
    $withoutOut = Customer::factory()->create(['name' => 'No Cylinders Out']);

    CustomerCylinderBalance::create([
        'customer_id' => $withOut->id,
        'product_id' => $product->id,
        'qty_out' => 2,
    ]);

    $response = $this->get('/customers?filter=has_out');

    $response->assertSee('Has Cylinders Out');
    $response->assertDontSee('No Cylinders Out');
});
