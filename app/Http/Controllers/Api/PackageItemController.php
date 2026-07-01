<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PackageItem\StorePackageItemRequest;
use App\Http\Requests\PackageItem\UpdatePackageItemRequest;
use App\Http\Resources\PackageItemResource;
use App\Services\PackageItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageItemController extends Controller
{
    public function __construct(private readonly PackageItemService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->list($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Items retrieved successfully.',
            'data' => PackageItemResource::collection($items),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $item = $this->service->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Item retrieved successfully.',
            'data' => $item ? new PackageItemResource($item) : null,
        ]);
    }

    public function store(StorePackageItemRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully.',
            'data' => new PackageItemResource($item),
        ], 201);
    }

    public function update(int $id, UpdatePackageItemRequest $request): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully.',
            'data' => new PackageItemResource($item),
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
