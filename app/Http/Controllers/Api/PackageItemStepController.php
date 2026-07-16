<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PackageItemStep\StorePackageItemStepRequest;
use App\Http\Requests\PackageItemStep\UpdatePackageItemStepRequest;
use App\Http\Resources\PackageItemStep\PackageItemStepResource;
use App\Services\PackageItemStepService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageItemStepController extends Controller
{
    public function __construct(private readonly PackageItemStepService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->list($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Items retrieved successfully.',
            'data' => PackageItemStepResource::collection($items),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $item = $this->service->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Item retrieved successfully.',
            'data' => $item ? new PackageItemStepResource($item) : null,
        ]);
    }

    public function store(StorePackageItemStepRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully.',
            'data' => new PackageItemStepResource($item),
        ], 201);
    }

    public function update(int $id, UpdatePackageItemStepRequest $request): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully.',
            'data' => new PackageItemStepResource($item),
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
