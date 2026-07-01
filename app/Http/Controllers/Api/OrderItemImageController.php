<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderItemImage\StoreOrderItemImageRequest;
use App\Http\Requests\OrderItemImage\UpdateOrderItemImageRequest;
use App\Http\Resources\OrderItemImageResource;
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
        $items = $this->service->list($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Items retrieved successfully.',
            'data' => OrderItemImageResource::collection($items),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $item = $this->service->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Item retrieved successfully.',
            'data' => $item ? new OrderItemImageResource($item) : null,
        ]);
    }

    public function store(StoreOrderItemImageRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully.',
            'data' => new OrderItemImageResource($item),
        ], 201);
    }

    public function update(int $id, UpdateOrderItemImageRequest $request): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully.',
            'data' => new OrderItemImageResource($item),
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
