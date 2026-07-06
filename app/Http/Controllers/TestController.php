<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{

  public function index()
  {

    $x = 123;

    return response()->json([
      'x' => $x
    ]);
  }
}
