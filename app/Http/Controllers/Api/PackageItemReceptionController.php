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
    $user = $request->user();
    $items = $this->service->list($user, $request->all());

    return $this->success(PackageItemReceptionResource::collection($items), 'Items retrieved successfully.');
  }

  public function show(Request $request, int $id): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->find($user, $id);

    return $this->success($item ? new PackageItemReceptionResource($item) : null, 'Item retrieved successfully.');
  }

  public function store(StorePackageItemReceptionRequest $request): JsonResponse
  {
    $item = $this->service->create($request->user(), $request->validated());

    return $this->success(new PackageItemReceptionResource($item), 'Item created successfully.', 201);
  }

  public function update(Request $request, int $id, UpdatePackageItemReceptionRequest $formRequest): JsonResponse
  {
    $item = $this->service->update($request->user(), $id, $formRequest->validated());

    return $this->success(new PackageItemReceptionResource($item), 'Item updated successfully.');
  }

  public function destroy(Request $request, int $id): JsonResponse
  {
    $this->service->delete($request->user(), $id);

    return $this->success(null, 'Item deleted successfully.');
  }
}
