<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Expense;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function finance(Request $request): View
    {
        $period = Carbon::parse($request->input('period', now()->format('Y-m')))->startOfMonth();
        $start = $period->copy()->startOfMonth();
        $end = $period->copy()->endOfMonth();

        $income = Payment::where('status', 'verified')->whereBetween('paid_at', [$start, $end])->sum('amount');
        $expenses = Expense::whereBetween('spent_at', [$start, $end])->sum('amount');
        $arrears = Bill::whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_PENDING])
            ->whereDate('period', '<=', $period)
            ->sum('amount');

        return view('reports.finance', [
            'period' => $period,
            'income' => $income,
            'expenses' => $expenses,
            'net' => $income - $expenses,
            'arrears' => $arrears,
            'payments' => Payment::with('bill.customer', 'bill.room')
                ->where('status', 'verified')
                ->whereBetween('paid_at', [$start, $end])
                ->latest('paid_at')
                ->get(),
            'expenseRows' => Expense::whereBetween('spent_at', [$start, $end])->latest('spent_at')->get(),
            'unpaidBills' => Bill::with('customer', 'room')
                ->whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_PENDING])
                ->whereDate('period', '<=', $period)
                ->latest('period')
                ->get(),
        ]);
    }
}
