<?php

namespace Tests\Unit;

use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Tests\TestCase;

class CountryResourceTest extends TestCase
{
  public function test_it_serializes_country_fields(): void
  {
    $country = new Country([
      'id' => 1,
      'country' => 'Algeria',
    ]);

    $resource = new CountryResource($country);

    $this->assertSame([
      'id' => 1,
      'country' => 'Algeria',
      'created_at' => null,
      'updated_at' => null,
    ], $resource->toArray(new Request()));
  }
}
