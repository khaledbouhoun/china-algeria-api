<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderItem\StoreOrderItemReceivedRequest;
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
    $user = $request->user();
    $items = $this->service->list($user, $request->all());

    return $this->success(OrderItemResource::collection($items), 'Items retrieved successfully.');
  }

  public function show(Request $request, int $id): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->find($user, $id);

    return $this->success($item ? new OrderItemResource($item) : null, 'Item retrieved successfully.');
  }

  public function store(StoreOrderItemRequest $request): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->create($user, $request->validated());

    return $this->success(new OrderItemResource($item), 'Item created successfully.', 201);
  }

  public function update(Request $request, int $id, UpdateOrderItemRequest $formRequest): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->update($user, $id, $formRequest->validated());

    return $this->success(new OrderItemResource($item), 'Item updated successfully.');
  }

  public function destroy(Request $request, int $id): JsonResponse
  {
    $user = $request->user();
    $this->service->delete($user, $id);

    return $this->success(null, 'Item deleted successfully.');
  }

  // ==========================================
  // Actions Functions
  // ==========================================

  public function receive(StoreOrderItemReceivedRequest $request): JsonResponse
  {
    $user = $request->user();

    $itemsIds = $request->validated('items_ids');

    $items = $this->service->receive($user, $itemsIds);

    return $this->success(
      OrderItemResource::collection($items),
      'Items received successfully.'
    );
  }
}
