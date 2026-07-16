<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Visa\StoreVisaRequest;
use App\Http\Requests\Visa\UpdateVisaRequest;
use App\Http\Resources\Visa\VisaResource;
use App\Services\VisaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisaController extends Controller
{
  public function __construct(private readonly VisaService $service)
  {
  }

  public function index(Request $request): JsonResponse
  {
    $user = $request->userOrFail();
    $items = $this->service->list($user, $request->all());

    return response()->json([
      'success' => true,
      'message' => 'Items retrieved successfully.',
      'data' => VisaResource::collection($items),
    ]);
  }

  public function show(Request $request, int $id): JsonResponse
  {
    $user = $request->userOrFail();
    $item = $this->service->find($user, $id);

    return response()->json([
      'success' => true,
      'message' => 'Item retrieved successfully.',
      'data' => $item ? new VisaResource($item) : null,
    ]);
  }

  public function store(StoreVisaRequest $request): JsonResponse
  {
    $user = $request->userOrFail();
    $item = $this->service->create($user, $request->validated());

    return response()->json([
      'success' => true,
      'message' => 'Item created successfully.',
      'data' => new VisaResource($item),
    ], 201);
  }

  public function update(Request $request, int $id, UpdateVisaRequest $formRequest): JsonResponse
  {
    $user = $request->userOrFail();
    $item = $this->service->update($user, $id, $formRequest->validated());

    return response()->json([
      'success' => true,
      'message' => 'Item updated successfully.',
      'data' => new VisaResource($item),
    ]);
  }

  public function destroy(Request $request, int $id): JsonResponse
  {
    $user = $request->userOrFail();
    $this->service->delete($user, $id);

    return response()->json([
      'success' => true,
      'message' => 'Item deleted successfully.',
      'data' => null,
    ]);
  }
}
