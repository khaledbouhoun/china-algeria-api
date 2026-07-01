<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserSession\StoreUserSessionRequest;
use App\Http\Requests\UserSession\UpdateUserSessionRequest;
use App\Http\Resources\UserSessionResource;
use App\Services\UserSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserSessionController extends Controller
{
    public function __construct(private readonly UserSessionService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->list($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Items retrieved successfully.',
            'data' => UserSessionResource::collection($items),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $item = $this->service->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Item retrieved successfully.',
            'data' => $item ? new UserSessionResource($item) : null,
        ]);
    }

    public function store(StoreUserSessionRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully.',
            'data' => new UserSessionResource($item),
        ], 201);
    }

    public function update(int $id, UpdateUserSessionRequest $request): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully.',
            'data' => new UserSessionResource($item),
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
