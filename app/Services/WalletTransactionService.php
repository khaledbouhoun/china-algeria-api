<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletTransactionService
{
    public function list(array $filters = []): mixed
    {
        return WalletTransaction::query()->get();
    }

    public function find(int $id): ?WalletTransaction
    {
        return WalletTransaction::find($id);
    }

    public function create(array $data): WalletTransaction
    {
        return DB::transaction(function () use ($data): WalletTransaction {
            $wallet = Wallet::findOrFail($data['wallet_id']);
            $balanceBefore = (float) $wallet->balance;
            $amount = (float) ($data['amount'] ?? 0);
            $direction = (int) ($data['direction'] ?? 1);
            $balanceAfter = $balanceBefore + ($direction === 1 ? $amount : -$amount);
            $wallet->balance = $balanceAfter;
            $wallet->save();

            return WalletTransaction::create([
                ...$data,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
            ]);
        });
    }

    public function update(int $id, array $data): WalletTransaction
    {
        $transaction = WalletTransaction::findOrFail($id);
        $transaction->fill($data);
        $transaction->save();

        return $transaction->fresh();
    }

    public function delete(int $id): bool
    {
        $transaction = WalletTransaction::findOrFail($id);

        return (bool) $transaction->delete();
    }
}
