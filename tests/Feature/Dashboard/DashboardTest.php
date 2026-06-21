<?php

use App\Models\User;

test('the dashboard renders with the sidebar layout', function () {
    $response = $this->actingAs(User::factory()->create())->get('/');

    $response->assertStatus(200);
    $response->assertSee('Dashboard');
    $response->assertSee('LPG Point');
});
