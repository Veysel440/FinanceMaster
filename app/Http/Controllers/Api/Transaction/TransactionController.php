<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Http\Resources\Transaction\TransactionResource;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'category_id']);
        $transactions = $this->transactionService->getUserTransactions($filters);

        return response()->json([
            'success' => true,
            'data'    => TransactionResource::collection($transactions),
            'meta'    => [
                'current_page' => $transactions->currentPage(),
                'last_page'    => $transactions->lastPage(),
                'total'        => $transactions->total(),
            ],
        ]);
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->createTransaction($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'İşlem başarıyla eklendi.',
            'data'    => new TransactionResource($transaction),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $transaction = $this->transactionService->getTransaction($id);
        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'İşlem bulunamadı.'], 404);
        }
        return response()->json([
            'success' => true,
            'data'    => new TransactionResource($transaction),
        ]);
    }

    public function update(UpdateTransactionRequest $request, $id): JsonResponse
    {
        $updated = $this->transactionService->updateTransaction($id, $request->validated());
        if ($updated) {
            return response()->json(['success' => true, 'message' => 'İşlem güncellendi.']);
        }
        return response()->json(['success' => false, 'message' => 'İşlem güncellenemedi.'], 400);
    }

    public function destroy($id): JsonResponse
    {
        $deleted = $this->transactionService->deleteTransaction($id);
        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'İşlem silindi.']);
        }
        return response()->json(['success' => false, 'message' => 'İşlem silinemedi.'], 400);
    }
}
