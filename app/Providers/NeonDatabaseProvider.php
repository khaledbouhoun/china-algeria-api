<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class NeonDatabaseProvider extends ServiceProvider
{
  public function register()
  {
    //
  }

  public function boot()
  {
    // Only process if using PostgreSQL and Neon
    if (env('DB_CONNECTION') === 'pgsql' && str_contains(env('DB_HOST', ''), 'neon')) {

      $host = env('DB_HOST');

      // Extract endpoint ID from hostname
      // From: ep-fragrant-sun-aqyuwojg-pooler.c-8.us-east-1.aws.neon.tech
      // To: ep-fragrant-sun-aqyuwojg
      $endpoint = explode('.', $host)[0];

      // Update PostgreSQL config
      config([
        'database.connections.pgsql' => [
          'driver' => 'pgsql',
          'host' => env('DB_HOST'),
          'port' => env('DB_PORT', '5432'),
          'database' => env('DB_DATABASE'),
          'username' => env('DB_USERNAME'),
          'password' => env('DB_PASSWORD'),
          'charset' => 'utf8',
          'prefix' => '',
          'schema' => 'public',
          'sslmode' => 'require',
          'options' => [
            'endpoint' => $endpoint,
          ],
        ],
      ]);
    }
  }
}
