<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FirebaseTokenMiddlewareTest extends TestCase
{
  public function test_it_returns_401_when_authorization_header_is_missing(): void
  {
    Route::get('/test-firebase-token', fn() => response()->json(['ok' => true]))
      ->middleware('firebase.token');

    $response = $this->getJson('/test-firebase-token');

    $response->assertStatus(401)
      ->assertJson([
        'status' => 'error',
        'message' => 'Missing authentication token.',
      ]);
  }
}
