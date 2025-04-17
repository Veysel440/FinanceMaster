<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoalService;


class GoalController extends Controller
{
    protected $goalService;

    public function __construct(GoalService $goalService)
    {
        $this->middleware('auth');
        $this->goalService = $goalService;
    }

    public function index()
    {
        $goals = $this->goalService->getUserGoals()->map(function ($goal) {
            $progress = $this->goalService->getGoalProgress($goal->id);
            $goal->progress = $progress;
            return $goal;
        });

        return view('goals.index', compact('goals'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0',
            'end_date' => 'required|date|after:today',
        ]);

        $this->goalService->createGoal($validated);

        return redirect()->route('goals.index')
            ->with('success', 'Hedef başarıyla eklendi.');
    }

    public function edit($id)
    {
        $goal = $this->goalService->getGoal($id);
        if (!$goal) {
            return redirect()->route('goals.index')->with('error', 'Hedef bulunamadı.');
        }

        return view('goals.edit', compact('goal'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0',
            'end_date' => 'required|date|after:today',
        ]);

        if ($this->goalService->updateGoal($id, $validated)) {
            return redirect()->route('goals.index')
                ->with('success', 'Hedef başarıyla güncellendi.');
        }

        return redirect()->route('goals.index')
            ->with('error', 'Hedef güncellenemedi.');
    }

    public function destroy($id)
    {
        if ($this->goalService->deleteGoal($id)) {
            return redirect()->route('goals.index')
                ->with('success', 'Hedef başarıyla silindi.');
        }

        return redirect()->route('goals.index')
            ->with('error', 'Hedef silinemedi.');
    }
}
