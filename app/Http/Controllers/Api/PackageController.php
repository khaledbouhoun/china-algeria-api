<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Package\StorePackageRequest;
use App\Http\Requests\Package\UpdatePackageRequest;
use App\Http\Resources\Package\PackageResource;
use App\Services\PackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
  public function __construct(private readonly PackageService $service)
  {
  }

  public function index(Request $request): JsonResponse
  {
    $user = $request->userOrFail();
    $items = $this->service->list($user, $request->all());

    return response()->json([
      'success' => true,
      'message' => 'Items retrieved successfully.',
      'data' => PackageResource::collection($items),
    ]);
  }

  public function show(Request $request, int $id): JsonResponse
  {
    $user = $request->userOrFail();
    $item = $this->service->find($user, $id);

    return response()->json([
      'success' => true,
      'message' => 'Item retrieved successfully.',
      'data' => $item ? new PackageResource($item) : null,
    ]);
  }

  public function store(StorePackageRequest $request): JsonResponse
  {
    $user = $request->userOrFail();
    $item = $this->service->create($user, $request->validated());

    return response()->json([
      'success' => true,
      'message' => 'Item created successfully.',
      'data' => new PackageResource($item),
    ], 201);
  }

  public function update(Request $request, int $id, UpdatePackageRequest $formRequest): JsonResponse
  {
    $user = $request->userOrFail();
    $item = $this->service->update($user, $id, $formRequest->validated());

    return response()->json([
      'success' => true,
      'message' => 'Item updated successfully.',
      'data' => new PackageResource($item),
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
