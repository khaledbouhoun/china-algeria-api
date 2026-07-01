<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PackageStep\StorePackageStepRequest;
use App\Http\Requests\PackageStep\UpdatePackageStepRequest;
use App\Http\Resources\PackageStepResource;
use App\Services\PackageStepService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageStepController extends Controller
{
    public function __construct(private readonly PackageStepService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->list($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Items retrieved successfully.',
            'data' => PackageStepResource::collection($items),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $item = $this->service->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Item retrieved successfully.',
            'data' => $item ? new PackageStepResource($item) : null,
        ]);
    }

    public function store(StorePackageStepRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully.',
            'data' => new PackageStepResource($item),
        ], 201);
    }

    public function update(int $id, UpdatePackageStepRequest $request): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully.',
            'data' => new PackageStepResource($item),
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
