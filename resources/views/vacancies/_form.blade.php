<div class="row g-3">
    <div class="col-md-8">
        <label for="title" class="form-label">Título *</label>
        <input type="text" id="title" name="title" value="{{ old('title', $vacancy->title) }}"
               class="form-control @error('title') is-invalid @enderror" required maxlength="255">
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="type" class="form-label">Tipo *</label>
        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
            <option value="">Selecione</option>
            @foreach ($types as $type)
                <option value="{{ $type->value }}" @selected(old('type', $vacancy->type?->value) === $type->value)>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-8">
        <label for="location" class="form-label">Localização</label>
        <input type="text" id="location" name="location" value="{{ old('location', $vacancy->location) }}"
               class="form-control @error('location') is-invalid @enderror" maxlength="255">
        @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="salary" class="form-label">Salário (R$)</label>
        <input type="number" id="salary" name="salary" value="{{ old('salary', $vacancy->salary) }}"
               class="form-control @error('salary') is-invalid @enderror" min="0" step="0.01">
        @error('salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Descrição *</label>
        <textarea id="description" name="description" rows="6"
                  class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $vacancy->description) }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input type="checkbox" id="is_paused" name="is_paused" value="1" class="form-check-input"
                   @checked(old('is_paused', $vacancy->is_paused))>
            <label for="is_paused" class="form-check-label">Vaga pausada (não recebe novas candidaturas)</label>
        </div>
    </div>
</div>
