<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $rooms = Room::with('activeOccupancy.customer')
            ->when($request->filled('q'), fn ($query) => $query->where('number', 'like', '%'.$request->q.'%'))
            ->orderBy('number')
            ->paginate(10)
            ->withQueryString();

        return view('rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        return view('rooms.create', ['room' => new Room()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Room::create($this->validated($request));

        return redirect()->route('rooms.index')->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function edit(Room $room): View
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $room->update($this->validated($request, $room->id));

        return redirect()->route('rooms.index')->with('success', 'Kamar berhasil diperbarui.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        if ($room->activeOccupancy()->exists()) {
            return back()->with('error', 'Kamar yang sedang terisi tidak dapat dihapus.');
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Kamar berhasil dihapus.');
    }

    private function validated(Request $request, ?int $roomId = null): array
    {
        return $request->validate([
            'number' => ['required', 'string', 'max:50', 'unique:rooms,number,'.$roomId],
            'monthly_price' => ['required', 'integer', 'min:1'],
        ]);
    }
}
