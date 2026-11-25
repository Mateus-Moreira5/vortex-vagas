<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Nome *</label>
        <input type="text" id="name" name="name" value="{{ old('name', $candidate->name) }}"
               class="form-control @error('name') is-invalid @enderror" required maxlength="255">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">E-mail *</label>
        <input type="email" id="email" name="email" value="{{ old('email', $candidate->email) }}"
               class="form-control @error('email') is-invalid @enderror" required maxlength="255">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label">Telefone</label>
        <input type="text" id="phone" name="phone" value="{{ old('phone', $candidate->phone) }}"
               class="form-control @error('phone') is-invalid @enderror" maxlength="20" placeholder="(86) 99999-9999">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="birth_date" class="form-label">Data de nascimento</label>
        <input type="date" id="birth_date" name="birth_date"
               value="{{ old('birth_date', $candidate->birth_date?->format('Y-m-d')) }}"
               class="form-control @error('birth_date') is-invalid @enderror" max="{{ now()->subDay()->format('Y-m-d') }}">
        @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
