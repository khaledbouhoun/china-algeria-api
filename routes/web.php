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

  $decoded = json_decode($json, true);

  $jsonError = json_last_error_msg();

  $tmpPath = storage_path('app/firebase/firebase_credentials.json');

  if (!empty($json)) {
    File::ensureDirectoryExists(dirname($tmpPath));
    File::put($tmpPath, $json);
  }

  $fileExists = File::exists($tmpPath);

  $fileContent = $fileExists ? File::get($tmpPath) : null;

  $decodedFile = $fileExists ? json_decode($fileContent, true) : null;

  return response()->json([

    // Environment
    'php_version' => PHP_VERSION,
    'laravel' => app()->version(),

    // Env variables
    'env' => [
      'FIREBASE_PROJECT_ID' => env('FIREBASE_PROJECT_ID'),
      'FIREBASE_CREDENTIALS' => env('FIREBASE_CREDENTIALS'),
      'FIREBASE_CREDENTIALS_JSON_exists' => !empty($json),
      'FIREBASE_CREDENTIALS_JSON_length' => strlen($json ?? ''),
    ],

    // Raw JSON
    'json' => [
      'valid' => $decoded !== null,
      'error' => $jsonError,
      'type' => $decoded['type'] ?? null,
      'project_id' => $decoded['project_id'] ?? null,
      'client_email' => $decoded['client_email'] ?? null,
      'private_key_exists' => isset($decoded['private_key']),
      'private_key_length' => isset($decoded['private_key']) ? strlen($decoded['private_key']) : 0,
      'preview' => substr($json ?? '', 0, 300),
    ],

    // Temporary file
    'file' => [
      'path' => $tmpPath,
      'exists' => $fileExists,
      'size' => $fileExists ? filesize($tmpPath) : null,
      'json_valid' => $decodedFile !== null,
      'project_id' => $decodedFile['project_id'] ?? null,
    ],

    // Config loaded by Laravel
    'config' => config('firebase.projects.app'),

  ]);
});
