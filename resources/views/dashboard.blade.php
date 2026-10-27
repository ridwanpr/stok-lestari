<x-layouts.app>
    <x-slot:title>Dashboard — Stok Lestari</x-slot:title>

    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-ink-primary sm:text-2xl">
                Ringkasan Inventaris
            </h1>
            <p class="mt-1 text-sm text-ink-secondary">
                Kondisi stok dan peringatan kedaluwarsa toko Anda saat ini.
            </p>
        </div>

        {{-- 4 Stat Cards --}}
        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
            <x-stat-card
                label="Total Produk"
                :value="number_format($totalProducts, 0, ',', '.')"
            />

            <x-stat-card
                label="Total Stok Aktif"
                :value="number_format($totalStock, 0, ',', '.')"
                hint="Unit di semua batch aktif"
            />

            <x-stat-card
                label="Kedaluwarsa"
                :value="number_format($expiringBatchesCount, 0, ',', '.')"
                hint="≤ 7 hari"
                :tone="$expiringBatchesCount > 0 ? 'warning' : 'default'"
            />

            <x-stat-card
                label="Flash Sale"
                :value="number_format($activeFlashSalesCount, 0, ',', '.')"
                hint="Sedang tayang"
                :tone="$activeFlashSalesCount > 0 ? 'brand' : 'default'"
            />
        </div>

        {{-- Expiring soon section --}}
        <div class="overflow-hidden rounded-xl border border-surface-border bg-surface-panel shadow-xs">
            <div class="border-b border-surface-border px-5 py-4">
                <h2 class="text-base font-semibold text-ink-primary">
                    Batch Kedaluwarsa Segera
                </h2>
                <p class="mt-0.5 text-xs text-ink-muted">
                    Maksimal 5 batch dengan masa simpan ≤ 7 hari untuk tindakan cepat.
                </p>
            </div>

            @if ($expiringBatches->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-sm font-medium text-ink-secondary">
                        Aman, tidak ada batch kedaluwarsa segera.
                    </p>
                    <p class="mt-1 text-xs text-ink-muted">
                        Semua batch produk aktif memiliki masa kedaluwarsa lebih dari 7 hari.
                    </p>
                </div>
            @else
                {{-- Desktop table view --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-surface-border text-left">
                        <thead class="bg-surface-base text-xs font-semibold uppercase tracking-wider text-ink-muted">
                            <tr>
                                <th scope="col" class="px-5 py-3">Produk</th>
                                <th scope="col" class="px-5 py-3 text-right">Sisa Stok</th>
                                <th scope="col" class="px-5 py-3">Tanggal Kedaluwarsa</th>
                                <th scope="col" class="px-5 py-3 text-right">Sisa Waktu</th>
                                <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border-subtle bg-surface-panel text-sm text-ink-primary">
                            @foreach ($expiringBatches as $batch)
                                @php
                                    $daysLeft = (int) now()->startOfDay()->diffInDays($batch->expiry_date->startOfDay(), false);
                                    $hasActiveFlashSale = $batch->flashSales->isNotEmpty();
                                @endphp
                                <tr class="hover:bg-surface-base/50 transition-colors">
                                    <td class="px-5 py-3.5 font-medium text-ink-primary">
                                        {{ $batch->product?->name ?? 'Produk #' . $batch->product_id }}
                                        <span class="ml-1 text-xs font-normal text-ink-faint">Batch #{{ $batch->id }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-medium tabular-nums text-ink-primary">
                                        {{ number_format($batch->stock, 0, ',', '.') }} unit
                                    </td>
                                    <td class="px-5 py-3.5 tabular-nums text-ink-secondary">
                                        {{ $batch->expiry_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        @if ($daysLeft === 0)
                                            <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-semibold bg-danger-50 text-danger-700 border border-danger-100">
                                                Hari ini
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-semibold bg-warning-50 text-warning-800 border border-warning-100">
                                                {{ $daysLeft }} hari lagi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        @if ($hasActiveFlashSale)
                                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-brand-50 text-brand-700 border border-brand-200">
                                                Flash Sale Aktif
                                            </span>
                                        @elseif ($batch->stock > 0)
                                            <a
                                                href="{{ route('flash-sales.index', ['batch_id' => $batch->id]) }}"
                                                class="inline-flex items-center rounded-md bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700 hover:bg-brand-100 border border-brand-200 transition-colors"
                                            >
                                                + Flash Sale
                                            </a>
                                        @else
                                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-surface-base text-ink-muted border border-surface-border">
                                                Stok Habis
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile stacked card view --}}
                <div class="divide-y divide-surface-border-subtle md:hidden">
                    @foreach ($expiringBatches as $batch)
                        @php
                            $daysLeft = (int) now()->startOfDay()->diffInDays($batch->expiry_date->startOfDay(), false);
                            $hasActiveFlashSale = $batch->flashSales->isNotEmpty();
                        @endphp
                        <div class="p-4 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <p class="min-w-0 break-words text-sm font-semibold text-ink-primary">
                                    {{ $batch->product?->name ?? 'Produk #' . $batch->product_id }}
                                    <span class="ml-1 text-xs font-normal text-ink-faint">Batch #{{ $batch->id }}</span>
                                </p>
                                @if ($daysLeft === 0)
                                    <span class="inline-flex shrink-0 items-center rounded px-2 py-0.5 text-xs font-semibold bg-danger-50 text-danger-700 border border-danger-100">
                                        Hari ini
                                    </span>
                                @else
                                    <span class="inline-flex shrink-0 items-center rounded px-2 py-0.5 text-xs font-semibold bg-warning-50 text-warning-800 border border-warning-100">
                                        {{ $daysLeft }} hari lagi
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between text-xs text-ink-muted">
                                <span>Stok: <strong class="font-medium text-ink-primary tabular-nums">{{ number_format($batch->stock, 0, ',', '.') }} unit</strong></span>
                                <span>Kedaluwarsa: <strong class="font-medium text-ink-secondary tabular-nums">{{ $batch->expiry_date->format('d/m/Y') }}</strong></span>
                            </div>
                            <div class="pt-1 flex justify-end">
                                @if ($hasActiveFlashSale)
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-brand-50 text-brand-700 border border-brand-200">
                                        Flash Sale Aktif
                                    </span>
                                @elseif ($batch->stock > 0)
                                    <a
                                        href="{{ route('flash-sales.index', ['batch_id' => $batch->id]) }}"
                                        class="inline-flex items-center rounded-md bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700 hover:bg-brand-100 border border-brand-200 transition-colors"
                                    >
                                        + Flash Sale
                                    </a>
                                @else
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-surface-base text-ink-muted border border-surface-border">
                                        Stok Habis
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
