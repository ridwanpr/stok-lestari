<?php

use App\Models\User;
use Database\Seeders\SellerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('login screen renders for guests', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('Masuk ke Stok Lestari');
});

test('authenticated users are redirected away from login', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('login'));

    $response->assertRedirect(route('dashboard'));
});

test('valid seeded credentials log in and redirect to dashboard', function () {
    $this->seed(SellerSeeder::class);

    $response = $this->post(route('login.store'), [
        'email' => 'seller@stoklestari.test',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
});

test('valid factory credentials log in and redirect to dashboard', function () {
    $user = User::factory()->create([
        'password' => 'my-secret-password',
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'my-secret-password',
        'remember' => true,
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('invalid credentials fail with an error and keep user as guest', function () {
    $user = User::factory()->create([
        'email' => 'seller@stoklestari.test',
        'password' => 'correct-password',
    ]);

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
    $response->assertSessionHasInput('email', $user->email);
    $this->assertGuest();
});

test('logout ends the session', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});
