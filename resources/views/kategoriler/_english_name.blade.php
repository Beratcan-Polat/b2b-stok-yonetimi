<div class="mb-4">
    <label for="name_en" class="form-label">{{ __('panel.category-name-english') }}</label>
    <input
        type="text"
        id="name_en"
        name="name_en"
        lang="en"
        class="form-control @error('name_en') is-invalid @enderror"
        value="{{ old('name_en', $kategori->name_en ?? '') }}"
        maxlength="255"
        aria-invalid="{{ $errors->has('name_en') ? 'true' : 'false' }}"
        aria-describedby="name-en-help{{ $errors->has('name_en') ? ' name-en-error' : '' }}"
    >
    @error('name_en')
        <div id="name-en-error" class="invalid-feedback">{{ $message }}</div>
    @enderror
    <div id="name-en-help" class="form-text">{{ __('panel.category-name-english-help') }}</div>
</div>
