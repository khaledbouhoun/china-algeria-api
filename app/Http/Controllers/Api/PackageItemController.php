<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PackageItem\StorePackageItemRequest;
use App\Http\Requests\PackageItem\UpdatePackageItemRequest;
use App\Http\Resources\PackageItem\PackageItemResource;
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
    $user = $request->user();
    $items = $this->service->list($user, $request->all());

    return $this->success(PackageItemResource::collection($items), 'Items retrieved successfully.');
  }

  public function show(Request $request, int $id): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->find($user, $id);

    return $this->success($item ? new PackageItemResource($item) : null, 'Item retrieved successfully.');
  }

  public function store(StorePackageItemRequest $request): JsonResponse
  {
    $item = $this->service->create($request->user(), $request->validated());

    return $this->success(new PackageItemResource($item), 'Item created successfully.', 201);
  }

  public function update(Request $request, int $id, UpdatePackageItemRequest $formRequest): JsonResponse
  {
    $item = $this->service->update($request->user(), $id, $formRequest->validated());

    return $this->success(new PackageItemResource($item), 'Item updated successfully.');
  }

  public function destroy(Request $request, int $id): JsonResponse
  {
    $this->service->delete($request->user(), $id);

    return $this->success(null, 'Item deleted successfully.');
  }
}
