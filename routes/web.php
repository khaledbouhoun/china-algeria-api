<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
  return view('welcome');
});


Route::get('/test-login', function () {
  return view('test-login');
});



Route::get('/firebase-debug', function () {
  return response()->json([
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'has_json' => !empty(env('FIREBASE_CREDENTIALS_JSON')),
    'json_length' => strlen(env('FIREBASE_CREDENTIALS_JSON', '')),
    'json_preview' => substr(env('FIREBASE_CREDENTIALS_JSON', ''), 0, 30),
    'json' => json_decode(env('FIREBASE_CREDENTIALS_JSON'), true),
  ]);
});
