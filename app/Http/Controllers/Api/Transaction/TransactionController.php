<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->middleware('auth');
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['type', 'category_id']);
        $transactions = $this->transactionService->getUserTransactions($filters);
        $categories = Category::where('user_id', auth()->id())
            ->orWhere('is_default', true)
            ->get();

        return view('transactions.index', compact('transactions', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('user_id', auth()->id())
            ->orWhere('is_default', true)
            ->get();

        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $this->transactionService->createTransaction($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'İşlem başarıyla eklendi.');
    }

    public function edit($id)
    {
        $transaction = $this->transactionService->getTransaction($id);
        if (!$transaction) {
            return redirect()->route('transactions.index')->with('error', 'İşlem bulunamadı.');
        }

        $categories = Category::where('user_id', auth()->id())
            ->orWhere('is_default', true)
            ->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        if ($this->transactionService->updateTransaction($id, $validated)) {
            return redirect()->route('transactions.index')
                ->with('success', 'İşlem başarıyla güncellendi.');
        }

        return redirect()->route('transactions.index')
            ->with('error', 'İşlem güncellenemedi.');
    }

    public function destroy($id)
    {
        if ($this->transactionService->deleteTransaction($id)) {
            return redirect()->route('transactions.index')
                ->with('success', 'İşlem başarıyla silindi.');
        }

        return redirect()->route('transactions.index')
            ->with('error', 'İşlem silinemedi.');
    }
}
