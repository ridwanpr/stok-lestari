<x-layouts.app>
    <x-slot:title>Flash Sale Cuci Gudang — Stok Lestari</x-slot:title>

    <div class="space-y-6">
        {{-- Flash message --}}
        @if (session('success'))
            <div class="rounded-lg border border-brand-100 bg-brand-50 p-4 text-sm font-medium text-brand-900" role="status">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div>
            <h1 class="text-xl font-bold tracking-tight text-ink-primary sm:text-2xl">
                Promo Flash Sale (Cuci Gudang)
            </h1>
        </div>

        {{-- Form Section: Tambah Flash Sale --}}
        <div class="rounded-xl border border-surface-border bg-surface-panel p-5 sm:p-6 shadow-xs">
            <h2 class="text-base font-semibold text-ink-primary mb-4">
                Daftarkan Promo Flash Sale Baru
            </h2>

            @if ($availableBatches->isEmpty())
                <p class="text-sm text-ink-muted">
                    Tidak ada batch aktif yang tersedia untuk flash sale.
                </p>
            @else
                <form action="{{ route('flash-sales.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="batch_id" class="block text-sm font-medium text-ink-primary">
                                Pilih Batch Produk <span class="text-danger-700">*</span>
                            </label>
                            <select
                                id="batch_id"
                                name="batch_id"
                                required
                                class="mt-1.5 block w-full min-h-[44px] rounded-lg border border-surface-border bg-surface-panel px-3 py-2 text-sm text-ink-primary focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-700 @error('batch_id') border-danger-700 @enderror"
                            >
                                <option value="">-- Pilih Batch --</option>
                                @foreach ($availableBatches as $batch)
                                    @php
                                        $selected = old('batch_id', $selectedBatchId) == $batch->id;
                                    @endphp
                                    <option value="{{ $batch->id }}" @selected($selected)>
                                        {{ $batch->product?->name }} (Batch #{{ $batch->id }}) · Stok {{ $batch->stock }} · Exp {{ $batch->expiry_date->format('d/m/Y') }} · Rp {{ number_format($batch->product?->normal_price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('batch_id')
                                <p class="mt-1 text-xs text-danger-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="discount_price" class="block text-sm font-medium text-ink-primary">
                                Harga Diskon Cuci Gudang (Rp) <span class="text-danger-700">*</span>
                            </label>
                            <input
                                type="number"
                                id="discount_price"
                                name="discount_price"
                                min="100"
                                step="100"
                                value="{{ old('discount_price') }}"
                                placeholder="Contoh: 15000"
                                required
                                class="mt-1.5 block w-full min-h-[44px] rounded-lg border border-surface-border bg-surface-panel px-3 py-2 text-sm text-ink-primary focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-700 @error('discount_price') border-danger-700 @enderror"
                            >
                            @error('discount_price')
                                <p class="mt-1 text-xs text-danger-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            class="inline-flex min-h-[44px] items-center justify-center rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white shadow-xs hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-700 focus:ring-offset-2 transition-colors cursor-pointer"
                        >
                            Pasang Flash Sale
                        </button>
                    </div>
                </form>
            @endif
        </div>

        {{-- Tabel Daftar Flash Sale --}}
        <div class="rounded-xl border border-surface-border bg-surface-panel shadow-xs overflow-hidden">
            <div class="border-b border-surface-border px-5 py-4">
                <h2 class="text-base font-semibold text-ink-primary">
                    Daftar Promo Flash Sale
                </h2>
            </div>

            @if ($flashSales->isEmpty())
                <div class="p-8 text-center text-sm text-ink-muted">
                    Belum ada riwayat promo flash sale yang tercatat.
                </div>
            @else
                {{-- Desktop table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-surface-border text-left">
                        <thead class="bg-surface-base text-xs font-semibold uppercase tracking-wider text-ink-muted">
                            <tr>
                                <th scope="col" class="px-5 py-3">Produk & Batch</th>
                                <th scope="col" class="px-5 py-3 text-right">Harga Normal</th>
                                <th scope="col" class="px-5 py-3 text-right">Harga Diskon</th>
                                <th scope="col" class="px-5 py-3 text-right">Sisa Stok</th>
                                <th scope="col" class="px-5 py-3">Kedaluwarsa</th>
                                <th scope="col" class="px-5 py-3 text-center">Status</th>
                                <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border-subtle text-sm text-ink-primary">
                            @foreach ($flashSales as $flashSale)
                                @php
                                    $batch = $flashSale->batch;
                                    $product = $batch?->product;
                                @endphp
                                <tr class="hover:bg-surface-base/50 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="font-medium text-ink-primary">
                                            {{ $product?->name ?? 'Produk #' . $batch?->product_id }}
                                        </div>
                                        <div class="text-xs text-ink-muted">
                                            Batch #{{ $batch?->id }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-right tabular-nums text-ink-secondary">
                                        <x-price :value="$product?->normal_price" />
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-semibold text-danger-700 tabular-nums">
                                        <x-price :value="$flashSale->discount_price" />
                                    </td>
                                    <td class="px-5 py-3.5 text-right tabular-nums">
                                        {{ number_format($batch?->stock ?? 0, 0, ',', '.') }} unit
                                    </td>
                                    <td class="px-5 py-3.5 tabular-nums text-ink-secondary">
                                        {{ $batch?->expiry_date?->format('d/m/Y') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @if ($flashSale->is_active)
                                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-brand-50 text-brand-700 border border-brand-200">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-surface-base text-ink-muted border border-surface-border">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right space-x-2 whitespace-nowrap">
                                        <form action="{{ route('flash-sales.toggle', $flashSale) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="rounded px-2.5 py-1 text-xs font-semibold border transition-colors cursor-pointer {{ $flashSale->is_active ? 'border-surface-border text-ink-secondary hover:bg-surface-base' : 'border-brand-200 bg-brand-50 text-brand-700 hover:bg-brand-100' }}"
                                            >
                                                {{ $flashSale->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        <form
                                            action="{{ route('flash-sales.destroy', $flashSale) }}"
                                            method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus flash sale ini?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="rounded px-2.5 py-1 text-xs font-semibold text-danger-700 hover:bg-danger-50 transition-colors cursor-pointer"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile card view --}}
                <div class="divide-y divide-surface-border-subtle md:hidden">
                    @foreach ($flashSales as $flashSale)
                        @php
                            $batch = $flashSale->batch;
                            $product = $batch?->product;
                        @endphp
                        <div class="p-3.5 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 class="text-sm font-semibold text-ink-primary line-clamp-1">
                                        {{ $product?->name }}
                                    </h3>
                                    <p class="text-xs text-ink-muted">
                                        Batch #{{ $batch?->id }} · Exp {{ $batch?->expiry_date?->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div class="shrink-0">
                                    @if ($flashSale->is_active)
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-brand-50 text-brand-700 border border-brand-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-surface-base text-ink-muted border border-surface-border">
                                            Nonaktif
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-baseline justify-between text-xs">
                                <div class="flex items-baseline gap-1.5">
                                    <x-price :value="$flashSale->discount_price" class="font-bold text-danger-700" />
                                    <del class="text-ink-muted"><x-price :value="$product?->normal_price" /></del>
                                </div>
                                <div class="text-ink-secondary">
                                    Sisa <strong class="text-ink-primary">{{ number_format($batch?->stock ?? 0, 0, ',', '.') }}</strong> unit
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-surface-border-subtle">
                                <form action="{{ route('flash-sales.toggle', $flashSale) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="rounded px-2.5 py-1 text-xs font-semibold border transition-colors cursor-pointer {{ $flashSale->is_active ? 'border-surface-border text-ink-secondary hover:bg-surface-base' : 'border-brand-200 bg-brand-50 text-brand-700 hover:bg-brand-100' }}"
                                    >
                                        {{ $flashSale->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>

                                <form
                                    action="{{ route('flash-sales.destroy', $flashSale) }}"
                                    method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus flash sale ini?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="rounded px-2.5 py-1 text-xs font-semibold text-danger-700 hover:bg-danger-50 transition-colors cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($flashSales->hasPages())
                    <div class="border-t border-surface-border px-4 py-3">
                        {{ $flashSales->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
