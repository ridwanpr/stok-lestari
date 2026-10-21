<div>
    <label for="name" class="block text-sm font-medium text-ink-primary">
        Nama Produk <span class="text-danger-600">*</span>
    </label>
    <div class="mt-1.5">
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $product->name) }}"
            required
            maxlength="255"
            placeholder="Contoh: Roti Tawar Gandum"
            aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
            aria-describedby="{{ $errors->has('name') ? 'name-error' : '' }}"
            class="block w-full min-h-[44px] rounded-lg border {{ $errors->has('name') ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600' : 'border-surface-border focus:border-brand-700 focus:ring-brand-700' }} bg-surface-panel px-3.5 py-2.5 text-sm text-ink-primary placeholder:text-ink-faint focus:outline-none focus:ring-2 sm:leading-6"
        >
    </div>
    @error('name')
        <p id="name-error" class="mt-1.5 text-xs font-medium text-danger-600">
            {{ $message }}
        </p>
    @enderror
</div>

<div>
    <label for="normal_price" class="block text-sm font-medium text-ink-primary">
        Harga Normal (Rupiah) <span class="text-danger-600">*</span>
    </label>
    <div class="relative mt-1.5">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
            <span class="text-sm font-medium text-ink-muted">Rp</span>
        </div>
        <input
            type="number"
            id="normal_price"
            name="normal_price"
            value="{{ old('normal_price', $product->normal_price) }}"
            required
            min="0"
            step="1"
            placeholder="0"
            aria-invalid="{{ $errors->has('normal_price') ? 'true' : 'false' }}"
            aria-describedby="{{ $errors->has('normal_price') ? 'normal_price-error' : '' }}"
            class="block w-full min-h-[44px] rounded-lg border {{ $errors->has('normal_price') ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600' : 'border-surface-border focus:border-brand-700 focus:ring-brand-700' }} bg-surface-panel pl-11 pr-3.5 py-2.5 text-sm tabular-nums text-ink-primary placeholder:text-ink-faint focus:outline-none focus:ring-2 sm:leading-6"
        >
    </div>
    @error('normal_price')
        <p id="normal_price-error" class="mt-1.5 text-xs font-medium text-danger-600">
            {{ $message }}
        </p>
    @enderror
</div>

<div>
    <label for="image" class="block text-sm font-medium text-ink-primary">
        Foto Produk
        <span class="text-xs font-normal text-ink-muted">(Opsional, JPG/PNG maks. 2 MB)</span>
    </label>

    @if ($product->image)
        <div class="mt-2 flex items-center gap-3 rounded-lg border border-surface-border-subtle bg-surface-base/50 p-2.5">
            <img
                src="{{ asset('storage/' . $product->image) }}"
                alt="{{ $product->name }}"
                class="h-14 w-14 rounded-md object-cover border border-surface-border shrink-0"
            >
            <div class="min-w-0 text-xs text-ink-muted">
                <span class="block font-medium text-ink-secondary">Foto produk saat ini</span>
                <span class="truncate block">Unggah foto baru jika ingin mengganti.</span>
            </div>
        </div>
    @endif

    <div class="mt-2">
        <input
            type="file"
            id="image"
            name="image"
            accept="image/*"
            aria-invalid="{{ $errors->has('image') ? 'true' : 'false' }}"
            aria-describedby="{{ $errors->has('image') ? 'image-error' : '' }}"
            class="block w-full min-h-[44px] text-sm text-ink-secondary file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-surface-border-subtle file:text-ink-primary hover:file:bg-surface-border cursor-pointer focus:outline-none"
        >
    </div>
    @error('image')
        <p id="image-error" class="mt-1.5 text-xs font-medium text-danger-600">
            {{ $message }}
        </p>
    @enderror
</div>
