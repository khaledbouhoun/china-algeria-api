<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletTransaction\StoreWalletTransactionRequest;
use App\Http\Requests\WalletTransaction\UpdateWalletTransactionRequest;
use App\Http\Resources\WalletTransaction\WalletTransactionResource;
use App\Services\WalletTransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletTransactionController extends Controller
{
  public function __construct(private readonly WalletTransactionService $service)
  {
  }

  public function index(Request $request): JsonResponse
  {
    $user = $request->user();
    $items = $this->service->list($user, $request->all());

    return $this->success(WalletTransactionResource::collection($items), 'Items retrieved successfully.');
  }

  public function show(Request $request, int $id): JsonResponse
  {
    $user = $request->user();
    $item = $this->service->find($user, $id);

    return $this->success($item ? new WalletTransactionResource($item) : null, 'Item retrieved successfully.');
  }

  public function store(StoreWalletTransactionRequest $request): JsonResponse
  {
    $item = $this->service->create($request->user(), $request->validated());

    return $this->success(new WalletTransactionResource($item), 'Item created successfully.', 201);
  }

  public function update(Request $request, int $id, UpdateWalletTransactionRequest $formRequest): JsonResponse
  {
    $item = $this->service->update($request->user(), $id, $formRequest->validated());

    return $this->success(new WalletTransactionResource($item), 'Item updated successfully.');
  }

  public function destroy(Request $request, int $id): JsonResponse
  {
    $this->service->delete($request->user(), $id);

    return $this->success(null, 'Item deleted successfully.');
  }
}
