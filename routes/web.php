<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

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
  $json = env('FIREBASE_CREDENTIALS_JSON');

  return [
    'length' => strlen($json),
    'json_error' => json_last_error_msg(),
    'decode' => json_decode($json, true),

    'contains_backslash_n' => str_contains($json, '\\n'),
    'contains_real_newline' => str_contains($json, "-----BEGIN PRIVATE KEY-----\nMII"),

    'first_250' => substr($json, 0, 250),

    'private_key_sample' => preg_match(
      '/"private_key"\s*:\s*"(.{0,150})/s',
      $json,
      $m
    ) ? $m[1] : null,

    'hex_after_begin' => bin2hex(substr(
      $json,
      strpos($json, '-----BEGIN PRIVATE KEY-----'),
      40
    )),
  ];
});
