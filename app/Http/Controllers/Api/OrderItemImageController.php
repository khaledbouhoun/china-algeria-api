<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderItemImage\StoreOrderItemImageRequest;
use App\Http\Requests\OrderItemImage\UpdateOrderItemImageRequest;
use App\Http\Resources\OrderItemImage\OrderItemImageResource;
use App\Services\OrderItemImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderItemImageController extends Controller
{
  public function __construct(private readonly OrderItemImageService $service)
  {
  }

  public function index(Request $request): JsonResponse
  {
    $user = $request->user();
    $items = $this->service->list($user, $request->all());

    return $this->success(OrderItemImageResource::collection($items), 'Items retrieved successfully.');
  }

  public function show(Request $request, int $id): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->find($user, $id);

    return $this->success($item ? new OrderItemImageResource($item) : null, 'Item retrieved successfully.');
  }

  public function store(StoreOrderItemImageRequest $request): JsonResponse
  {
    $item = $this->service->create($request->user(), $request->validated());

    return $this->success(new OrderItemImageResource($item), 'Item created successfully.', 201);
  }

  public function update(Request $request, int $id, UpdateOrderItemImageRequest $formRequest): JsonResponse
  {
    $item = $this->service->update($request->user(), $id, $formRequest->validated());

    return $this->success(new OrderItemImageResource($item), 'Item updated successfully.');
  }

  public function destroy(Request $request, int $id): JsonResponse
  {
    $this->service->delete($request->user(), $id);

    return $this->success(null, 'Item deleted successfully.');
  }
}
