@php
    $formUrun = $urun ?? null;
@endphp

@if ($kategoriler->isEmpty())
    <div class="alert alert-warning" role="alert">
        {{ __('panel.no-categories') }}
        @can('create', App\Models\Category::class)
            <a href="{{ route('kategoriler.create') }}" class="alert-link">{{ __('panel.new-category') }}</a>
        @endcan
    </div>
@endif

<div class="mb-3">
    <label for="category_id" class="form-label">{{ __('panel.category') }}</label>
    <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror"
        aria-invalid="{{ $errors->has('category_id') ? 'true' : 'false' }}"
        @error('category_id') aria-describedby="category-error" @enderror required>
        <option value="">{{ __('panel.select-category') }}</option>
        @foreach ($kategoriler as $kategori)
            <option value="{{ $kategori->id }}" @selected(old('category_id', $formUrun?->category_id) == $kategori->id)>
                {{ $kategori->localizedName() }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <div id="category-error" class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="name" class="form-label">{{ __('panel.product-name') }}</label>
    <input
        type="text"
        id="name"
        name="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $formUrun?->name) }}"
        maxlength="255"
        aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
        @error('name') aria-describedby="name-error" @enderror
        required
    >
    @error('name')
        <div id="name-error" class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="sku" class="form-label">SKU</label>
    <input
        type="text"
        id="sku"
        name="sku"
        class="form-control @error('sku') is-invalid @enderror"
        value="{{ old('sku', $formUrun?->sku) }}"
        maxlength="100"
        aria-invalid="{{ $errors->has('sku') ? 'true' : 'false' }}"
        @error('sku') aria-describedby="sku-error" @enderror
        required
    >
    @error('sku')
        <div id="sku-error" class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-12 col-md-6 mb-3">
        <label for="price" class="form-label">{{ __('panel.price') }}</label>
        <input
            type="number"
            id="price"
            name="price"
            class="form-control @error('price') is-invalid @enderror"
            value="{{ old('price', $formUrun?->price) }}"
            min="0"
            step="0.01"
            aria-invalid="{{ $errors->has('price') ? 'true' : 'false' }}"
            @error('price') aria-describedby="price-error" @enderror
            required
        >
        @error('price')
            <div id="price-error" class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6 mb-3">
        <label for="stock" class="form-label">{{ __('panel.stock-quantity') }}</label>
        <input
            type="number"
            id="stock"
            name="stock"
            class="form-control @error('stock') is-invalid @enderror"
            value="{{ old('stock', $formUrun?->stock ?? 0) }}"
            min="0"
            step="1"
            aria-invalid="{{ $errors->has('stock') ? 'true' : 'false' }}"
            @error('stock') aria-describedby="stock-error" @enderror
            required
        >
        @error('stock')
            <div id="stock-error" class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@if ($formUrun?->image_path)
    <div class="mb-3">
        <p class="form-label">{{ __('panel.current-image') }}</p>
        <img src="{{ asset('storage/'.$formUrun->image_path) }}" alt="{{ $formUrun->name }}"
            class="img-thumbnail tema-urun-onizleme">
    </div>
@endif

<div class="mb-4">
    <label for="image" class="form-label">{{ $formUrun ? __('panel.new-image') : __('panel.product-image') }}</label>
    <input
        type="file"
        id="image"
        name="image"
        class="form-control @error('image') is-invalid @enderror"
        accept=".jpeg,.jpg,.png,.webp"
        aria-invalid="{{ $errors->has('image') ? 'true' : 'false' }}"
        aria-describedby="image-help @error('image') image-error @enderror"
    >
    @error('image')
        <div id="image-error" class="invalid-feedback">{{ $message }}</div>
    @enderror
    <div id="image-help" class="form-text">
        {{ __('panel.image-help') }}
        @if ($formUrun)
            {{ __('panel.keep-image-help') }}
        @endif
    </div>
</div>

<div class="d-flex align-items-center flex-wrap gap-2">
    <button type="submit" class="btn btn-primary" @disabled($kategoriler->isEmpty())>{{ $butonMetni }}</button>
    <a href="{{ auth()->user()->can('viewAny', App\Models\Product::class) ? route('urunler.index') : route('anasayfa') }}" class="btn btn-secondary">
        {{ __('panel.cancel-button') }}
    </a>
</div>
