<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderItem\StoreOrderItemRequest;
use App\Http\Requests\OrderItem\UpdateOrderItemRequest;
use App\Http\Resources\OrderItem\OrderItemResource;
use App\Services\OrderItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
  public function __construct(private readonly OrderItemService $service)
  {
  }

  public function index(Request $request): JsonResponse
  {
    $user = $request->userOrFail();
    $items = $this->service->list($user, $request->all());

    return response()->json([
      'success' => true,
      'message' => 'Items retrieved successfully.',
      'data' => OrderItemResource::collection($items),
    ]);
  }

  public function show(Request $request, int $id): JsonResponse
  {
    $user = $request->userOrFail();
    $item = $this->service->find($user, $id);

    return response()->json([
      'success' => true,
      'message' => 'Item retrieved successfully.',
      'data' => $item ? new OrderItemResource($item) : null,
    ]);
  }

  public function store(StoreOrderItemRequest $request): JsonResponse
  {
    $user = $request->userOrFail();
    $item = $this->service->create($user, $request->validated());

    return response()->json([
      'success' => true,
      'message' => 'Item created successfully.',
      'data' => new OrderItemResource($item),
    ], 201);
  }

  public function update(Request $request, int $id, UpdateOrderItemRequest $formRequest): JsonResponse
  {
    $user = $request->userOrFail();
    $item = $this->service->update($user, $id, $formRequest->validated());

    return response()->json([
      'success' => true,
      'message' => 'Item updated successfully.',
      'data' => new OrderItemResource($item),
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
