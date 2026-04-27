<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Occupancy;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::with('activeOccupancy.room')
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->q.'%')
                    ->orWhere('phone', 'like', '%'.$request->q.'%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create', [
            'customer' => new Customer(['status' => 'active']),
            'rooms' => Room::with('activeOccupancy')->orderBy('number')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $roomId = $data['room_id'] ?? null;
            $startedAt = $data['started_at'] ?? now()->toDateString();
            unset($data['room_id'], $data['started_at']);

            $customer = Customer::create($data);

            if ($roomId && $customer->status === 'active') {
                $this->ensureRoomAvailable((int) $roomId);
                Occupancy::create([
                    'customer_id' => $customer->id,
                    'room_id' => $roomId,
                    'started_at' => $startedAt,
                ]);
            }
        });

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', [
            'customer' => $customer->load('activeOccupancy.room'),
            'rooms' => Room::with('activeOccupancy')->orderBy('number')->get(),
        ]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($customer, $data) {
            $roomId = $data['room_id'] ?? null;
            $startedAt = $data['started_at'] ?? now()->toDateString();
            unset($data['room_id'], $data['started_at']);

            $customer->update($data);
            $active = $customer->activeOccupancy;

            if ($customer->status === 'inactive') {
                $active?->update(['ended_at' => now()->toDateString()]);
                return;
            }

            if ($roomId && (! $active || $active->room_id !== (int) $roomId)) {
                $this->ensureRoomAvailable((int) $roomId);
                $active?->update(['ended_at' => now()->toDateString()]);
                Occupancy::create([
                    'customer_id' => $customer->id,
                    'room_id' => $roomId,
                    'started_at' => $startedAt,
                ]);
            }

            if (! $roomId && $active) {
                $active->update(['ended_at' => now()->toDateString()]);
            }
        });

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function deactivate(Customer $customer): RedirectResponse
    {
        DB::transaction(function () use ($customer) {
            $customer->update(['status' => 'inactive']);
            $customer->activeOccupancy?->update(['ended_at' => now()->toDateString()]);
        });

        return back()->with('success', 'Pelanggan dinonaktifkan dan kamar dikosongkan.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'identity_number' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'started_at' => ['nullable', 'date'],
        ]);
    }

    private function ensureRoomAvailable(int $roomId): void
    {
        if (Occupancy::where('room_id', $roomId)->whereNull('ended_at')->exists()) {
            throw ValidationException::withMessages([
                'room_id' => 'Kamar ini sudah ditempati penghuni aktif.',
            ]);
        }
    }
}
