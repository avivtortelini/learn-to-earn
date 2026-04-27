@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama</label>
        <input class="form-control" name="name" value="{{ old('name', $user->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Role</label>
        <select class="form-select" name="role" required>
            @foreach(['owner' => 'Pemilik', 'receptionist' => 'Receptionist'] as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user->role ?? 'receptionist') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Password</label>
        <input class="form-control" type="password" name="password" {{ $user->exists ? '' : 'required' }}>
        @if($user->exists)<div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>@endif
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>
