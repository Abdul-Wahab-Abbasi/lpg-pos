<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using username and password', function () {
    $user = User::factory()->create(['username' => 'testuser', 'password' => 'password']);

    $response = $this->post('/login', [
        'username' => 'testuser',
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect('/');
});

test('users cannot authenticate with an invalid password', function () {
    $user = User::factory()->create(['username' => 'testuser']);

    $this->post('/login', [
        'username' => 'testuser',
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('guests are redirected to login when accessing a protected route', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});

test('authenticated users can log out', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/login');
});
