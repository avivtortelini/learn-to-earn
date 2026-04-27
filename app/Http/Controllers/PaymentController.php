<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        return view('payments.index', [
            'payments' => Payment::with('bill.customer', 'bill.room', 'verifier')->latest()->paginate(10),
        ]);
    }

    public function create(Bill $bill): View
    {
        return view('payments.create', compact('bill'));
    }

    public function store(Request $request, Bill $bill): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'paid_at' => ['required', 'date'],
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'notes' => ['nullable', 'string'],
        ]);

        $path = $request->file('proof')->store('payment-proofs', 'public');

        DB::transaction(function () use ($bill, $data, $path) {
            $bill->payments()->create([
                'amount' => $data['amount'],
                'paid_at' => $data['paid_at'],
                'proof_path' => $path,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            $bill->update(['status' => Bill::STATUS_PENDING]);
        });

        return redirect()->route('payments.index')->with('success', 'Bukti pembayaran berhasil diunggah.');
    }

    public function verify(Payment $payment): RedirectResponse
    {
        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            $payment->bill->update(['status' => Bill::STATUS_PAID]);
        });

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        DB::transaction(function () use ($payment) {
            $bill = $payment->bill;
            Storage::disk('public')->delete($payment->proof_path);
            $payment->delete();

            if (! $bill->payments()->exists()) {
                $bill->update(['status' => Bill::STATUS_UNPAID]);
            }
        });

        return back()->with('success', 'Pembayaran berhasil dihapus.');
    }
}
