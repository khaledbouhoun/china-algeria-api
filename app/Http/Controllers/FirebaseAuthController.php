<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FirebaseAuthController extends Controller
{
  public function register(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required|min:6',
    ]);

    $response = Http::post(
      'https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=' . env('FIREBASE_API_KEY'),
      [
        'email' => $request->email,
        'password' => $request->password,
        'returnSecureToken' => true,
      ]
    );

    return response()->json($response->json(), $response->status());
  }
}
