@csrf
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Tanggal</label>
        <input class="form-control" type="date" name="spent_at" value="{{ old('spent_at', $expense->spent_at?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Kategori</label>
        <input class="form-control" name="category" value="{{ old('category', $expense->category ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Nominal</label>
        <input class="form-control" type="number" name="amount" value="{{ old('amount', $expense->amount ?? '') }}" required>
    </div>
    <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <textarea class="form-control" name="description">{{ old('description', $expense->description ?? '') }}</textarea>
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>
