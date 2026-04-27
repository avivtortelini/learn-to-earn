<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillController extends Controller
{
    public function index(Request $request): View
    {
        $bills = Bill::with('customer', 'room')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('period'), fn ($query) => $query->whereDate('period', Carbon::parse($request->period)->startOfMonth()))
            ->latest('period')
            ->paginate(10)
            ->withQueryString();

        return view('bills.index', compact('bills'));
    }

    public function create(): View
    {
        return view('bills.create', [
            'customers' => Customer::with('activeOccupancy.room')->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'period' => ['required', 'date'],
            'due_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $customer = Customer::with('activeOccupancy.room')->findOrFail($data['customer_id']);
        abort_unless($customer->activeOccupancy, 422, 'Pelanggan belum menempati kamar aktif.');

        try {
            Bill::create([
                'customer_id' => $customer->id,
                'room_id' => $customer->activeOccupancy->room_id,
                'period' => Carbon::parse($data['period'])->startOfMonth(),
                'due_date' => $data['due_date'],
                'amount' => $customer->activeOccupancy->room->monthly_price,
                'status' => Bill::STATUS_UNPAID,
                'notes' => $data['notes'] ?? null,
            ]);
        } catch (QueryException) {
            return back()->withInput()->with('error', 'Tagihan untuk pelanggan dan periode tersebut sudah ada.');
        }

        return redirect()->route('bills.index')->with('success', 'Tagihan berhasil dibuat.');
    }

    public function generator(): View
    {
        return view('bills.generate');
    }

    public function generate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'period' => ['required', 'date'],
            'due_date' => ['required', 'date'],
        ]);

        $period = Carbon::parse($data['period'])->startOfMonth();
        $created = 0;

        Customer::with('activeOccupancy.room')
            ->where('status', 'active')
            ->whereHas('activeOccupancy')
            ->get()
            ->each(function (Customer $customer) use ($period, $data, &$created) {
                $bill = Bill::firstOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'period' => $period,
                    ],
                    [
                        'room_id' => $customer->activeOccupancy->room_id,
                        'due_date' => $data['due_date'],
                        'amount' => $customer->activeOccupancy->room->monthly_price,
                        'status' => Bill::STATUS_UNPAID,
                    ]
                );

                if ($bill->wasRecentlyCreated) {
                    $created++;
                }
            });

        return redirect()->route('bills.index')->with('success', $created.' tagihan baru berhasil dibuat.');
    }

    public function edit(Bill $bill): View
    {
        return view('bills.edit', compact('bill'));
    }

    public function update(Request $request, Bill $bill): RedirectResponse
    {
        $data = $request->validate([
            'due_date' => ['required', 'date'],
            'amount' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $bill->update($data);

        return redirect()->route('bills.index')->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Bill $bill): RedirectResponse
    {
        if ($bill->payments()->exists()) {
            return back()->with('error', 'Tagihan yang sudah memiliki pembayaran tidak dapat dihapus.');
        }

        $bill->delete();

        return redirect()->route('bills.index')->with('success', 'Tagihan berhasil dihapus.');
    }
}
