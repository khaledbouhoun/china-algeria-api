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
    $user = $request->user();
    $items = $this->service->list($user, $request->all());

    return $this->success(PackageResource::collection($items), 'Items retrieved successfully.');
  }

  public function show(Request $request, int $id): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->find($user, $id);

    return $this->success($item ? new PackageResource($item) : null, 'Item retrieved successfully.');
  }

  public function store(StorePackageRequest $request): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->create($user, $request->validated());

    return $this->success(new PackageResource($item), 'Item created successfully.', 201);
  }

  public function update(Request $request, int $id, UpdatePackageRequest $formRequest): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->update($user, $id, $formRequest->validated());

    return $this->success(new PackageResource($item), 'Item updated successfully.');
  }

  public function destroy(Request $request, int $id): JsonResponse
  {
    $user = $request->user();
    $this->service->delete($user, $id);

    return $this->success(null, 'Item deleted successfully.');
  }
}
