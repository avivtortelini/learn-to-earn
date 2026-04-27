@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama</label>
        <input class="form-control" name="name" value="{{ old('name', $customer->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">No Identitas</label>
        <input class="form-control" name="identity_number" value="{{ old('identity_number', $customer->identity_number ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Telepon</label>
        <input class="form-control" name="phone" value="{{ old('phone', $customer->phone ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
            @foreach(['active' => 'Aktif', 'inactive' => 'Nonaktif'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $customer->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Kamar Aktif</label>
        <select class="form-select" name="room_id">
            <option value="">Tanpa kamar</option>
            @foreach($rooms as $room)
                @php($selectedRoom = old('room_id', optional($customer->activeOccupancy)->room_id))
                <option value="{{ $room->id }}" @selected((int) $selectedRoom === $room->id) @disabled($room->activeOccupancy && (int) $selectedRoom !== $room->id)>
                    {{ $room->number }} - Rp {{ number_format($room->monthly_price, 0, ',', '.') }} {{ $room->activeOccupancy ? '(terisi)' : '' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Tanggal Masuk</label>
        <input class="form-control" type="date" name="started_at" value="{{ old('started_at', optional($customer->activeOccupancy->started_at ?? null)->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
    </div>
    <div class="col-12">
        <label class="form-label">Alamat</label>
        <textarea class="form-control" name="address" rows="3">{{ old('address', $customer->address ?? '') }}</textarea>
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>
