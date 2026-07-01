<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderItemStep\StoreOrderItemStepRequest;
use App\Http\Requests\OrderItemStep\UpdateOrderItemStepRequest;
use App\Http\Resources\OrderItemStepResource;
use App\Services\OrderItemStepService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderItemStepController extends Controller
{
    public function __construct(private readonly OrderItemStepService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->list($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Items retrieved successfully.',
            'data' => OrderItemStepResource::collection($items),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $item = $this->service->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Item retrieved successfully.',
            'data' => $item ? new OrderItemStepResource($item) : null,
        ]);
    }

    public function store(StoreOrderItemStepRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully.',
            'data' => new OrderItemStepResource($item),
        ], 201);
    }

    public function update(int $id, UpdateOrderItemStepRequest $request): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully.',
            'data' => new OrderItemStepResource($item),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully.',
            'data' => null,
        ]);
    }
}
