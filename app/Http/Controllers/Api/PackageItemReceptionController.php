<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PackageItemReception\StorePackageItemReceptionRequest;
use App\Http\Requests\PackageItemReception\UpdatePackageItemReceptionRequest;
use App\Http\Resources\PackageItemReception\PackageItemReceptionResource;
use App\Services\PackageItemReceptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageItemReceptionController extends Controller
{
    public function __construct(private readonly PackageItemReceptionService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->list($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Items retrieved successfully.',
            'data' => PackageItemReceptionResource::collection($items),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $item = $this->service->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Item retrieved successfully.',
            'data' => $item ? new PackageItemReceptionResource($item) : null,
        ]);
    }

    public function store(StorePackageItemReceptionRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully.',
            'data' => new PackageItemReceptionResource($item),
        ], 201);
    }

    public function update(int $id, UpdatePackageItemReceptionRequest $request): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully.',
            'data' => new PackageItemReceptionResource($item),
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
