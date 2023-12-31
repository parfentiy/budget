<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\PlanBudget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
=======
use App\Models\expenses;
use Illuminate\Http\Request;
use App\Models\PlanBudget;
use App\Models\ExpensesType;
use App\Models\BudgetNote;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;
>>>>>>> 1e03a7501220e7f7749dc0dc3d824ac3c6af1b27

class PlanBudgetController extends Controller
{
    //
<<<<<<< HEAD
    public function index() {
        if (isset(request()->delete)) {
            PlanBudget::whereId(request()->delete)->delete();

            return view('planbudget');
        } else {

            return view('planbudget', [
                'budgetId' => request()->budgetId,
            ]);
        }
    }

    public function add() {
        if (request()->isClone) {
            $dataset = PlanBudget::whereId(request()->cloneFrom)->first()->dataset;
            $incomes = PlanBudget::whereId(request()->cloneFrom)->first()->incomes;
        } else {
            $dataset = json_encode([]);
            $incomes = json_encode([]);
        }
        $newBudget = PlanBudget::create([
            'month' => request()->month,
            'year' => request()->year,
            'user_id' => Auth::user()->id,
            'dataset' => $dataset,
            'incomes' => $incomes,
        ]);

        return view('planbudget', [
            'budgetId' => $newBudget->id,
        ]);
    }

    public function clone() {

        return view('planbudget', [
        ]);
    }

    public function addItem() {
        $budget = PlanBudget::where('id', request()->currentBudget)->first();
        foreach (json_decode($budget->dataset, true) as $item) {
            $array[] = $item;
        }
        $array[] = [
            'account' => (int) request()->account_id,
            'sum' => (int) request()->sum,
            'order' => (int) request()->order,
        ];

        $budget->dataset = $array;
        $budget->save();

        return view('planbudget', [
            'budgetId' => $budget->id,
        ]);
    }

    public function deleteItem() {
        $budget = PlanBudget::where('id', request()->currentBudget)->first();
        $array = [];
        foreach (json_decode($budget->dataset, true) as $item) {
            if ($item['order'] != request()->id) {
                $array[] = $item;
            }
        }

        $budget->dataset = $array;
        $budget->save();

        return view('planbudget', [
            'budgetId' => $budget->id,
        ]);
    }

    public function addIncome() {
        $budget = PlanBudget::where('id', request()->currentBudget)->first();
        foreach (json_decode($budget->incomes, true) as $item) {
            $array[] = $item;
        }
        $array[] = [
            'account' => (int) request()->account_id,
            'order' => (int) request()->order,
        ];

        $budget->incomes = $array;
        $budget->save();

        return view('planbudget', [
            'budgetId' => $budget->id,
        ]);
    }

    public function deleteIncome() {
        $budget = PlanBudget::where('id', request()->currentBudget)->first();
        $array = [];
        foreach (json_decode($budget->incomes, true) as $item) {
            if ($item['account'] != (int)request()->id) {
                $array[] = $item;
            }
        }

        $budget->incomes = $array;
        $budget->save();

        return view('planbudget', [
            'budgetId' => $budget->id,
        ]);
=======
    public function budgetEdit(Request $request)
    {

        $da = $request->date;
        $dateComps = date_parse($da);
        $year = $dateComps['year'];
        $month = $dateComps['month'];
        $budgetItems = PlanBudget::where('month', '=', $month)
            ->where('year', '=', $year)
            ->orderBy('ordernum_id')->get();

        $expOut = [];
        $sumPercent = 0;
        foreach ($budgetItems as $budgetItem) {
            $s = ExpensesType::find($budgetItem['expensestypes_id']);

            if(expenses::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->where('expensestypes_id', '=', $budgetItem->expensestypes_id)->count() == 0) {
                $sumWasted = 0;
            } else {
                $sumWasted = expenses::select('expensestypes_id', expenses::raw('SUM(sum) as sum'))
                    ->where('expensestypes_id', '=', $budgetItem->expensestypes_id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->groupBy('expensestypes_id')
                    ->first()->sum;
            }

            if (ExpensesType::find($budgetItem->expensestypes_id)->is_percent) {
                $expOut[] = [
                    'expType' => ExpensesType::find($budgetItem->expensestypes_id)->name,
                    'sumWasted' => $sumPercent/100*ExpensesType::find($budgetItem->expensestypes_id)->percent,
                    'id' => $s->id,
                    'name' => $s->name,
                    'sum' => $budgetItem->sum,
                    'is_percent' => $s->is_percent,
                    'percent' => $s->percent,
                    'ordernum' => $budgetItem->ordernum_id,
                ];
            } else {
                $expOut[] = [
                    'expType' => ExpensesType::find($budgetItem->expensestypes_id)->name,
                    'sumWasted' => $sumWasted,
                    'id' => $s->id,
                    'name' => $s->name,
                    'sum' => $budgetItem->sum,
                    'is_percent' => $s->is_percent,
                    'percent' => $s->percent,
                    'ordernum' => $budgetItem->ordernum_id,
                ];
            }
        }

        return view('planningedit', [
            'month' => $month,
            'year' => $year,
            'budgetItems' => $budgetItems,
            'expTypes' => $expOut,
            'expensesTypes' => ExpensesType::all(),
        ]);

    }

    public function budgetSave(Request $request, $year, $month)
    {
        PlanBudget::find($request->expenseid)
            ->update([
                'sum' => $request->sum,
                'expensestypes_id' => $request->expensestypes_id,
                'ordernum_id' => $request->ordernum,
            ]);
        return view('planningform');
    }

    public function budgetDelete(Request $request)
    {
        PlanBudget::find($request->expenseid)->delete();

        return view('planningform');
    }

    public function budgetCreate(Request $request, $year, $month)
    {
        PlanBudget::create([
            'expensestypes_id' => $request->expType,
            'sum' => $request->sum,
            'month' => $month,
            'year' => $year,
            'ordernum_id' => $request->ordernum
        ]);

        return view('planningform');
    }

    public function noteUpdate(Request $request, $month, $year)
    {
        $note = BudgetNote::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)->first();
        if(!$note) {
            BudgetNote::create([
                'note' => $request->note,
                'created_at' => $year . '-' . $month . '-15',
            ]);

            return view('reportslist');
        } else {
            BudgetNote::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->update([
                    'note' => $request->note,
                ]);

            return view('reportslist');
        }
>>>>>>> 1e03a7501220e7f7749dc0dc3d824ac3c6af1b27
    }
}
