<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\StoreWalletRequest;
use App\Http\Requests\Wallet\UpdateWalletRequest;
use App\Http\Resources\Wallet\WalletResource;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
  public function __construct(private readonly WalletService $service)
  {
  }

  public function index(Request $request): JsonResponse
  {
    $user = $request->user();
    $items = $this->service->list($user, $request->all());

    return $this->success(WalletResource::collection($items), 'Items retrieved successfully.');
  }

  public function show(Request $request, int $id): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->find($user, $id);

    return $this->success($item ? new WalletResource($item) : null, 'Item retrieved successfully.');
  }

  public function store(StoreWalletRequest $request): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->create($user, $request->validated());

    return $this->success(new WalletResource($item), 'Item created successfully.', 201);
  }

  public function update(Request $request, int $id, UpdateWalletRequest $formRequest): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->update($user, $id, $formRequest->validated());

    return $this->success(new WalletResource($item), 'Item updated successfully.');
  }

  public function destroy(Request $request, int $id): JsonResponse
  {
    $user = $request->user();
    $this->service->delete($user, $id);

    return $this->success(null, 'Item deleted successfully.');
  }
}
