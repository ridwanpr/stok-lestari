<x-layouts.app>
    <x-slot:title>Edit {{ $product->name }} — Stok Lestari</x-slot:title>

    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <nav class="flex items-center text-xs text-ink-muted mb-2">
                <a href="{{ route('products.index') }}" class="hover:text-ink-primary transition-colors">Produk</a>
                <span class="mx-2 text-ink-faint">/</span>
                <span class="text-ink-primary font-medium">Edit Produk</span>
            </nav>
            <h1 class="text-xl font-bold tracking-tight text-ink-primary sm:text-2xl">
                Edit Produk
            </h1>
            <p class="mt-1 text-sm text-ink-secondary">
                Perbarui detail informasi produk ini.
            </p>
        </div>

        {{-- Flash message --}}
        @if (session('success'))
            <div class="rounded-lg border border-brand-100 bg-brand-50 p-4 text-sm font-medium text-brand-900" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-danger-100 bg-danger-50 p-4 text-sm font-medium text-danger-700" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-xl border border-surface-border bg-surface-panel p-5 sm:p-6 shadow-xs">
            <form id="edit-product-form" action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-5" novalidate>
                @csrf
                @method('PUT')

                @include('products._form')

                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-surface-border-subtle">
                    <button
                        type="button"
                        onclick="if (confirm('Hapus produk ini? Semua batch terkait juga akan dihapus.')) document.getElementById('delete-product-form').submit();"
                        class="flex min-h-[44px] w-full sm:w-auto items-center justify-center rounded-lg px-3.5 py-2 text-sm font-semibold text-danger-700 hover:bg-danger-50 focus:outline-none focus:ring-2 focus:ring-danger-600 transition-colors cursor-pointer"
                    >
                        Hapus Produk
                    </button>
                    <div class="flex items-center justify-end gap-3">
                        <a
                            href="{{ route('products.index') }}"
                            class="flex min-h-[44px] items-center justify-center rounded-lg border border-surface-border px-4 py-2.5 text-sm font-semibold text-ink-secondary hover:bg-surface-base focus:outline-none focus:ring-2 focus:ring-brand-700 transition-colors"
                        >
                            Batal
                        </a>
                        <button
                            type="submit"
                            class="flex min-h-[44px] items-center justify-center rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-700 focus:ring-offset-2 transition-colors cursor-pointer"
                        >
                            Perbarui Produk
                        </button>
                    </div>
                </div>
            </form>

            <form id="delete-product-form" action="{{ route('products.destroy', $product) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>

        {{-- Batch & Stok Section --}}
        @php
            $batches = $product->batches()
                ->withExists(['flashSales as has_active_flash_sale' => fn ($query) => $query->where('is_active', true)])
                ->orderBy('expiry_date', 'asc')
                ->get();
        @endphp

        <div class="overflow-hidden rounded-xl border border-surface-border bg-surface-panel shadow-xs">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-surface-border px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-ink-primary">
                        Batch &amp; Stok
                    </h2>
                    <p class="mt-0.5 text-xs text-ink-muted">
                        Daftar batch kedaluwarsa dan ketersediaan stok fisik produk ini.
                    </p>
                </div>
            </div>

            {{-- Collapsible inline add batch form --}}
            <details class="border-b border-surface-border bg-surface-base/40 group" {{ $errors->hasAny(['stock', 'expiry_date', 'status']) ? 'open' : '' }}>
                <summary class="flex min-h-[44px] cursor-pointer items-center justify-between px-5 py-3 text-sm font-semibold text-brand-700 hover:bg-surface-base transition-colors select-none">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Batch
                    </span>
                    <svg class="h-4 w-4 text-ink-muted transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </summary>

                <form action="{{ route('products.batches.store', $product) }}" method="POST" class="border-t border-surface-border-subtle bg-surface-panel p-5 space-y-4" novalidate>
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="create_batch_stock" class="block text-sm font-medium text-ink-primary">
                                Stok <span class="text-danger-600">*</span>
                            </label>
                            <div class="mt-1.5">
                                <input
                                    type="number"
                                    id="create_batch_stock"
                                    name="stock"
                                    value="{{ old('stock') }}"
                                    required
                                    min="0"
                                    step="1"
                                    placeholder="0"
                                    aria-invalid="{{ $errors->has('stock') ? 'true' : 'false' }}"
                                    aria-describedby="{{ $errors->has('stock') ? 'create-batch-stock-error' : '' }}"
                                    class="block w-full min-h-[44px] rounded-lg border {{ $errors->has('stock') ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600' : 'border-surface-border focus:border-brand-700 focus:ring-brand-700' }} bg-surface-panel px-3.5 py-2.5 text-sm tabular-nums text-ink-primary placeholder:text-ink-faint focus:outline-none focus:ring-2 sm:leading-6"
                                >
                            </div>
                            @error('stock')
                                <p id="create-batch-stock-error" class="mt-1.5 text-xs font-medium text-danger-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="create_batch_expiry_date" class="block text-sm font-medium text-ink-primary">
                                Tanggal Kedaluwarsa <span class="text-danger-600">*</span>
                            </label>
                            <div class="mt-1.5">
                                <input
                                    type="date"
                                    id="create_batch_expiry_date"
                                    name="expiry_date"
                                    value="{{ old('expiry_date') }}"
                                    required
                                    min="{{ now()->toDateString() }}"
                                    aria-invalid="{{ $errors->has('expiry_date') ? 'true' : 'false' }}"
                                    aria-describedby="{{ $errors->has('expiry_date') ? 'create-batch-expiry-date-error' : '' }}"
                                    class="block w-full min-h-[44px] rounded-lg border {{ $errors->has('expiry_date') ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600' : 'border-surface-border focus:border-brand-700 focus:ring-brand-700' }} bg-surface-panel px-3.5 py-2.5 text-sm text-ink-primary placeholder:text-ink-faint focus:outline-none focus:ring-2 sm:leading-6"
                                >
                            </div>
                            @error('expiry_date')
                                <p id="create-batch-expiry-date-error" class="mt-1.5 text-xs font-medium text-danger-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="create_batch_status" class="block text-sm font-medium text-ink-primary">
                                Status <span class="text-danger-600">*</span>
                            </label>
                            <div class="mt-1.5">
                                <select
                                    id="create_batch_status"
                                    name="status"
                                    required
                                    aria-invalid="{{ $errors->has('status') ? 'true' : 'false' }}"
                                    aria-describedby="{{ $errors->has('status') ? 'create-batch-status-error' : '' }}"
                                    class="block w-full min-h-[44px] rounded-lg border {{ $errors->has('status') ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600' : 'border-surface-border focus:border-brand-700 focus:ring-brand-700' }} bg-surface-panel px-3.5 py-2.5 text-sm text-ink-primary focus:outline-none focus:ring-2 sm:leading-6"
                                >
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="expired" {{ old('status') === 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                                    <option value="sold_out" {{ old('status') === 'sold_out' ? 'selected' : '' }}>Habis Terjual</option>
                                </select>
                            </div>
                            @error('status')
                                <p id="create-batch-status-error" class="mt-1.5 text-xs font-medium text-danger-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <button
                            type="submit"
                            class="flex min-h-[44px] items-center justify-center rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-700 focus:ring-offset-2 transition-colors cursor-pointer"
                        >
                            Simpan Batch
                        </button>
                    </div>
                </form>
            </details>

            {{-- Batch List --}}
            @if ($batches->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-sm font-medium text-ink-secondary">
                        Belum ada batch untuk produk ini.
                    </p>
                    <p class="mt-1 text-xs text-ink-muted">
                        Buka form di atas untuk menambahkan batch stok pertama.
                    </p>
                </div>
            @else
                {{-- Desktop Table View --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-surface-border text-left">
                        <thead class="bg-surface-base text-xs font-semibold uppercase tracking-wider text-ink-muted">
                            <tr>
                                <th scope="col" class="px-5 py-3">Stok</th>
                                <th scope="col" class="px-5 py-3">Tanggal Kedaluwarsa</th>
                                <th scope="col" class="px-5 py-3">Status</th>
                                <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border-subtle bg-surface-panel text-sm text-ink-primary">
                            @foreach ($batches as $batch)
                                @php
                                    $isExpired = $batch->status === 'expired'
                                        || $batch->expiry_date->toDateString() < now()->toDateString();
                                    $isExpiringSoon = ! $isExpired
                                        && $batch->status === 'active'
                                        && $batch->expiry_date->toDateString() <= now()->addDays(7)->toDateString();
                                @endphp
                                <tr class="hover:bg-surface-base/50 transition-colors">
                                    <td class="px-5 py-3.5 font-medium tabular-nums text-ink-primary">
                                        {{ number_format($batch->stock, 0, ',', '.') }} unit
                                    </td>
                                    <td class="px-5 py-3.5 text-xs tabular-nums text-ink-secondary">
                                        {{ $batch->expiry_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            @if ($isExpired)
                                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold bg-danger-50 text-danger-700 border border-danger-100">
                                                    Kedaluwarsa
                                                </span>
                                            @elseif ($batch->status === 'active')
                                                @if ($isExpiringSoon)
                                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold bg-warning-50 text-warning-800 border border-warning-100">
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-surface-base text-ink-muted border border-surface-border">
                                                        Aktif
                                                    </span>
                                                @endif
                                            @elseif ($batch->status === 'sold_out')
                                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-surface-border-subtle text-ink-secondary border border-surface-border">
                                                    Habis Terjual
                                                </span>
                                            @endif

                                            @if ($batch->has_active_flash_sale)
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-warning-50 text-warning-800 border border-warning-100">
                                                    Flash Sale
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <a
                                                href="{{ route('batches.edit', $batch) }}"
                                                class="flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg px-2.5 py-1 text-xs font-semibold text-ink-secondary hover:bg-surface-base hover:text-ink-primary focus:outline-none focus:ring-2 focus:ring-brand-700 transition-colors"
                                            >
                                                Edit
                                            </a>
                                            <form action="{{ route('batches.destroy', $batch) }}" method="POST" onsubmit="return confirm('Hapus batch ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg px-2.5 py-1 text-xs font-semibold text-danger-700 hover:bg-danger-50 focus:outline-none focus:ring-2 focus:ring-danger-600 transition-colors cursor-pointer"
                                                >
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Stacked View --}}
                <div class="divide-y divide-surface-border-subtle md:hidden">
                    @foreach ($batches as $batch)
                        @php
                            $isExpired = $batch->status === 'expired'
                                || $batch->expiry_date->toDateString() < now()->toDateString();
                            $isExpiringSoon = ! $isExpired
                                && $batch->status === 'active'
                                && $batch->expiry_date->toDateString() <= now()->addDays(7)->toDateString();
                        @endphp
                        <div class="p-4 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="space-y-1">
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-sm font-semibold tabular-nums text-ink-primary">
                                            {{ number_format($batch->stock, 0, ',', '.') }} unit
                                        </span>
                                        <span class="text-xs text-ink-muted">
                                            Batch #{{ $batch->id }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-ink-secondary">
                                        Kedaluwarsa: <strong class="font-medium text-ink-primary tabular-nums">{{ $batch->expiry_date->format('d/m/Y') }}</strong>
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    @if ($isExpired)
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold bg-danger-50 text-danger-700 border border-danger-100">
                                            Kedaluwarsa
                                        </span>
                                    @elseif ($batch->status === 'active')
                                        @if ($isExpiringSoon)
                                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold bg-warning-50 text-warning-800 border border-warning-100">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-surface-base text-ink-muted border border-surface-border">
                                                Aktif
                                            </span>
                                        @endif
                                    @elseif ($batch->status === 'sold_out')
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-surface-border-subtle text-ink-secondary border border-surface-border">
                                            Habis Terjual
                                        </span>
                                    @endif

                                    @if ($batch->has_active_flash_sale)
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-warning-50 text-warning-800 border border-warning-100">
                                            Flash Sale
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-surface-border-subtle">
                                <a
                                    href="{{ route('batches.edit', $batch) }}"
                                    class="flex min-h-[44px] items-center justify-center rounded-lg px-3 py-1.5 text-xs font-semibold text-ink-secondary hover:bg-surface-base hover:text-ink-primary focus:outline-none focus:ring-2 focus:ring-brand-700 transition-colors"
                                >
                                    Edit
                                </a>
                                <form action="{{ route('batches.destroy', $batch) }}" method="POST" onsubmit="return confirm('Hapus batch ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="flex min-h-[44px] items-center justify-center rounded-lg px-3 py-1.5 text-xs font-semibold text-danger-700 hover:bg-danger-50 focus:outline-none focus:ring-2 focus:ring-danger-600 transition-colors cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

