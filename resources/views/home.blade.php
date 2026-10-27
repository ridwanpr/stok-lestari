<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-surface-base">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cuci Gudang — {{ config('app.name', 'Stok Lestari') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans text-ink-primary antialiased bg-surface-base flex flex-col">
    {{-- Header Navbar --}}
    <header class="border-b border-surface-border bg-surface-panel sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full bg-brand-600"></span>
                <span class="font-bold text-base tracking-tight text-ink-primary">{{ config('app.name', 'Stok Lestari') }}</span>
            </a>
            <nav class="flex items-center">
                @if (auth()->check())
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center min-h-[38px] px-3 py-1.5 rounded-lg text-xs sm:text-sm font-semibold bg-brand-50 text-brand-700 hover:bg-brand-100 border border-brand-200 transition-colors">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center min-h-[38px] px-3 py-1.5 rounded-lg text-xs sm:text-sm font-semibold bg-surface-base text-ink-primary hover:bg-surface-border/50 border border-surface-border transition-colors">
                        Masuk Toko
                    </a>
                @endif
            </nav>
        </div>
    </header>

    {{-- Main content --}}
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 w-full">
        {{-- Hero --}}
        <div class="mb-6 sm:mb-8">
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-ink-primary">
                Cuci Gudang
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-ink-secondary">
                Katalog diskon produk mendekati batas simpan.
            </p>
        </div>

        {{-- Products Grid --}}
        @if ($flashSales->isEmpty())
            <div class="rounded-xl border border-dashed border-surface-border bg-surface-panel p-8 sm:p-12 text-center">
                <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
                <h3 class="mt-3 text-sm font-semibold text-ink-primary">Belum Ada Promo Aktif</h3>
                <p class="mt-1 text-xs sm:text-sm text-ink-muted">
                    Belum ada produk dalam program cuci gudang saat ini.
                </p>
            </div>
        @else
            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4 xl:gap-6">
                @foreach ($flashSales as $flashSale)
                    @php
                        $product = $flashSale->batch?->product;
                        $batch = $flashSale->batch;
                        $discountPercent = ($product?->normal_price > 0 && $product->normal_price > $flashSale->discount_price)
                            ? round((($product->normal_price - $flashSale->discount_price) / $product->normal_price) * 100)
                            : 0;
                    @endphp
                    <div class="group flex flex-col overflow-hidden rounded-xl border border-surface-border bg-surface-panel shadow-xs transition-shadow hover:shadow-md">
                        <div class="aspect-square bg-surface-base relative overflow-hidden flex items-center justify-center">
                            @if ($product?->image)
                                <img
                                    src="{{ Storage::url($product->image) }}"
                                    alt="{{ $product->name }}"
                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                >
                            @else
                                <svg class="h-10 w-10 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            @endif
                            @if ($discountPercent > 0)
                                <div class="absolute top-2 right-2">
                                    <span class="rounded bg-danger-700 px-1.5 py-0.5 text-[11px] font-bold text-white shadow-xs">
                                        -{{ $discountPercent }}%
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-1 flex-col p-3 sm:p-4">
                            <h3 class="font-semibold text-xs sm:text-sm text-ink-primary line-clamp-2 min-h-[2rem]" title="{{ $product?->name }}">
                                {{ $product?->name }}
                            </h3>

                            <div class="mt-2 flex flex-wrap items-baseline gap-1.5">
                                <x-price :value="$flashSale->discount_price" class="text-sm sm:text-base font-bold text-danger-700 tabular-nums" />
                                @if ($product?->normal_price)
                                    <del class="text-[11px] sm:text-xs text-ink-muted tabular-nums">
                                        <x-price :value="$product->normal_price" />
                                    </del>
                                @endif
                            </div>

                            <div class="mt-auto pt-2.5 border-t border-surface-border-subtle flex items-center justify-between text-[11px] sm:text-xs text-ink-secondary">
                                <span>Sisa <strong class="text-ink-primary tabular-nums">{{ number_format($batch->stock, 0, ',', '.') }}</strong></span>
                                <span class="text-ink-muted tabular-nums">Exp. {{ $batch->expiry_date->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    {{-- Footer --}}
    <footer class="border-t border-surface-border bg-surface-panel py-6 text-center text-xs text-ink-muted">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Stok Lestari') }}. Seluruh hak cipta dilindungi.</p>
    </footer>
</body>
</html>
