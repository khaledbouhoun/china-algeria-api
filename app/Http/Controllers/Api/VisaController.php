<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Visa\StoreVisaRequest;
use App\Http\Requests\Visa\UpdateVisaRequest;
use App\Http\Resources\Visa\VisaResource;
use App\Services\VisaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisaController extends Controller
{
  public function __construct(private readonly VisaService $service)
  {
  }

  public function index(Request $request): JsonResponse
  {
    $user = $request->user();
    $items = $this->service->list($user, $request->all());

    return $this->success(VisaResource::collection($items), 'Items retrieved successfully.');
  }

  public function show(Request $request, int $id): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->find($user, $id);

    return $this->success($item ? new VisaResource($item) : null, 'Item retrieved successfully.');
  }

  public function store(StoreVisaRequest $request): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->create($user, $request->validated());

    return $this->success(new VisaResource($item), 'Item created successfully.', 201);
  }

  public function update(Request $request, int $id, UpdateVisaRequest $formRequest): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->update($user, $id, $formRequest->validated());

    return $this->success(new VisaResource($item), 'Item updated successfully.');
  }

  public function destroy(Request $request, int $id): JsonResponse
  {
    $user = $request->user();
    $this->service->delete($user, $id);

    return $this->success(null, 'Item deleted successfully.');
  }
}
