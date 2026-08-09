<?php

use App\Models\User;

test('public registration screen is disabled', function () {
    $response = $this->get('/register');

    $response->assertNotFound();
});

test('public registration endpoint is disabled', function () {
    $email = 'test-registration-disabled@example.com';

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => $email,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertNotFound();
    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => $email]);
});
