<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $occupiedRooms = Room::whereHas('activeOccupancy')->count();

        return view('dashboard', [
            'totalRooms' => Room::count(),
            'occupiedRooms' => $occupiedRooms,
            'emptyRooms' => Room::count() - $occupiedRooms,
            'unpaidBills' => Bill::whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_PENDING])->count(),
            'monthlyIncome' => Payment::where('status', 'verified')->whereBetween('paid_at', [$start, $end])->sum('amount'),
            'monthlyExpense' => Expense::whereBetween('spent_at', [$start, $end])->sum('amount'),
            'recentPayments' => Payment::with('bill.customer', 'bill.room')->latest()->limit(5)->get(),
        ]);
    }
}
