@csrf
<div class="mb-3">
    <label class="form-label">Nomor Kamar</label>
    <input class="form-control" name="number" value="{{ old('number', $room->number ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Harga Bulanan</label>
    <input class="form-control" type="number" name="monthly_price" value="{{ old('monthly_price', $room->monthly_price ?? '') }}" min="1" required>
</div>
<button class="btn btn-primary">Simpan</button>
<a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">Batal</a>
