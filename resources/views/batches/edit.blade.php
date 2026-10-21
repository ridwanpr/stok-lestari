<x-layouts.app>
    <x-slot:title>Edit Batch #{{ $batch->id }} — {{ $product->name }} — Stok Lestari</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <nav class="flex items-center text-xs text-ink-muted mb-2">
                <a href="{{ route('products.index') }}" class="hover:text-ink-primary transition-colors">Produk</a>
                <span class="mx-2 text-ink-faint">/</span>
                <a href="{{ route('products.edit', $product) }}" class="hover:text-ink-primary transition-colors truncate max-w-xs">{{ $product->name }}</a>
                <span class="mx-2 text-ink-faint">/</span>
                <span class="text-ink-primary font-medium">Edit Batch #{{ $batch->id }}</span>
            </nav>
            <h1 class="text-xl font-bold tracking-tight text-ink-primary sm:text-2xl">
                Edit Batch #{{ $batch->id }}
            </h1>
            <p class="mt-1 text-sm text-ink-secondary">
                Perbarui jumlah stok, tanggal kedaluwarsa, atau status untuk produk <strong class="font-semibold text-ink-primary">{{ $product->name }}</strong>.
            </p>
        </div>

        @if (session('error'))
            <div class="rounded-lg border border-danger-100 bg-danger-50 p-4 text-sm font-medium text-danger-700" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-xl border border-surface-border bg-surface-panel p-5 sm:p-6 shadow-xs">
            <form id="edit-batch-form" action="{{ route('batches.update', $batch) }}" method="POST" class="space-y-5" novalidate>
                @csrf
                @method('PUT')

                <div>
                    <label for="stock" class="block text-sm font-medium text-ink-primary">
                        Jumlah Stok <span class="text-danger-600">*</span>
                    </label>
                    <div class="mt-1.5">
                        <input
                            type="number"
                            id="stock"
                            name="stock"
                            value="{{ old('stock', $batch->stock) }}"
                            required
                            min="0"
                            step="1"
                            placeholder="0"
                            aria-invalid="{{ $errors->has('stock') ? 'true' : 'false' }}"
                            aria-describedby="{{ $errors->has('stock') ? 'stock-error' : '' }}"
                            class="block w-full min-h-[44px] rounded-lg border {{ $errors->has('stock') ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600' : 'border-surface-border focus:border-brand-700 focus:ring-brand-700' }} bg-surface-panel px-3.5 py-2.5 text-sm tabular-nums text-ink-primary placeholder:text-ink-faint focus:outline-none focus:ring-2 sm:leading-6"
                        >
                    </div>
                    @error('stock')
                        <p id="stock-error" class="mt-1.5 text-xs font-medium text-danger-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-ink-primary">
                        Tanggal Kedaluwarsa <span class="text-danger-600">*</span>
                    </label>
                    <div class="mt-1.5">
                        <input
                            type="date"
                            id="expiry_date"
                            name="expiry_date"
                            value="{{ old('expiry_date', $batch->expiry_date?->format('Y-m-d')) }}"
                            required
                            aria-invalid="{{ $errors->has('expiry_date') ? 'true' : 'false' }}"
                            aria-describedby="{{ $errors->has('expiry_date') ? 'expiry_date-error' : '' }}"
                            class="block w-full min-h-[44px] rounded-lg border {{ $errors->has('expiry_date') ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600' : 'border-surface-border focus:border-brand-700 focus:ring-brand-700' }} bg-surface-panel px-3.5 py-2.5 text-sm text-ink-primary placeholder:text-ink-faint focus:outline-none focus:ring-2 sm:leading-6"
                        >
                    </div>
                    @error('expiry_date')
                        <p id="expiry_date-error" class="mt-1.5 text-xs font-medium text-danger-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-ink-primary">
                        Status Batch <span class="text-danger-600">*</span>
                    </label>
                    <div class="mt-1.5">
                        <select
                            id="status"
                            name="status"
                            required
                            aria-invalid="{{ $errors->has('status') ? 'true' : 'false' }}"
                            aria-describedby="{{ $errors->has('status') ? 'status-error' : '' }}"
                            class="block w-full min-h-[44px] rounded-lg border {{ $errors->has('status') ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600' : 'border-surface-border focus:border-brand-700 focus:ring-brand-700' }} bg-surface-panel px-3.5 py-2.5 text-sm text-ink-primary focus:outline-none focus:ring-2 sm:leading-6"
                        >
                            <option value="active" {{ old('status', $batch->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="expired" {{ old('status', $batch->status) === 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                            <option value="sold_out" {{ old('status', $batch->status) === 'sold_out' ? 'selected' : '' }}>Habis Terjual</option>
                        </select>
                    </div>
                    @error('status')
                        <p id="status-error" class="mt-1.5 text-xs font-medium text-danger-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-surface-border-subtle">
                    <button
                        type="button"
                        onclick="if (confirm('Hapus batch ini?')) document.getElementById('delete-batch-form').submit();"
                        class="flex min-h-[44px] w-full sm:w-auto items-center justify-center rounded-lg px-3.5 py-2 text-sm font-semibold text-danger-700 hover:bg-danger-50 focus:outline-none focus:ring-2 focus:ring-danger-600 transition-colors cursor-pointer"
                    >
                        Hapus Batch
                    </button>
                    <div class="flex items-center justify-end gap-3">
                        <a
                            href="{{ route('products.edit', $product) }}"
                            class="flex min-h-[44px] items-center justify-center rounded-lg border border-surface-border px-4 py-2.5 text-sm font-semibold text-ink-secondary hover:bg-surface-base focus:outline-none focus:ring-2 focus:ring-brand-700 transition-colors"
                        >
                            Batal
                        </a>
                        <button
                            type="submit"
                            class="flex min-h-[44px] items-center justify-center rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-700 focus:ring-offset-2 transition-colors cursor-pointer"
                        >
                            Perbarui Batch
                        </button>
                    </div>
                </div>
            </form>

            <form id="delete-batch-form" action="{{ route('batches.destroy', $batch) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</x-layouts.app>
