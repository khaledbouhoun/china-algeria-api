<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
  public function __construct(private readonly OrderService $service)
  {
  }

  public function index(Request $request): JsonResponse
  {
    $orders = $this->service->list($request->user(), $request->all());

    return $this->success(OrderResource::collection($orders), 'Orders retrieved successfully.');
  }

  public function show(Request $request, int $id): JsonResponse
  {
    $order = $this->service->find($request->user(), $id);

    return $this->success(new OrderResource($order), 'Order retrieved successfully.');
  }

  public function store(StoreOrderRequest $request): JsonResponse
  {
    $order = $this->service->create($request->user(), $request->validated());

    return $this->success(new OrderResource($order), 'Order created successfully.', 201);
  }

  public function update(Request $request, int $id, UpdateOrderRequest $formRequest): JsonResponse
  {
    $order = $this->service->update($request->user(), $id, $formRequest->validated());

    return $this->success(new OrderResource($order), 'Order updated successfully.');
  }

  public function destroy(Request $request, int $id): JsonResponse
  {
    $this->service->delete($request->user(), $id);

    return $this->success(null, 'Order deleted successfully.');
  }
}
