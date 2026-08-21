<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recuperation\StoreRecuperationRequest;
use App\Http\Resources\Recuperation\RecuperationResource;
use App\Services\RecuperationService;
use Illuminate\Http\JsonResponse;

class RecuperationController extends Controller
{
  public function __construct(private readonly RecuperationService $service)
  {
  }

  /**
   * Handle client recuperation of one or multiple package items.
   *
   * @param StoreRecuperationRequest $request
   * @return JsonResponse
   */
  public function store(StoreRecuperationRequest $request): JsonResponse
  {
    $items = $this->service->recuperate($request->user(), $request->validated());

    return $this->success(
      RecuperationResource::collection(collect($items)),
      'Items recuperated by client successfully.',
      200
    );
  }
}


