<x-layouts.app>
    <x-slot:title>Produk — Stok Lestari</x-slot:title>

    <div class="space-y-6">
        {{-- Flash message --}}
        @if (session('success'))
            <div class="rounded-lg border border-brand-100 bg-brand-50 p-4 text-sm font-medium text-brand-900" role="status">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header & CTA --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-ink-primary sm:text-2xl">
                    Katalog Produk
                </h1>
                <p class="mt-1 text-sm text-ink-secondary">
                    Kelola data produk toko, harga jual, dan pantau stok aktif.
                </p>
            </div>
            <div class="flex items-center">
                <a
                    href="{{ route('products.create') }}"
                    class="inline-flex min-h-[44px] items-center justify-center rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-700 focus:ring-offset-2 transition-colors cursor-pointer"
                >
                    <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Produk
                </a>
            </div>
        </div>

        {{-- Filter & Search --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <form method="GET" action="{{ route('products.index') }}" class="flex-1 flex gap-2">
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-ink-muted" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari nama produk..."
                        class="block w-full min-h-[44px] rounded-lg border border-surface-border bg-surface-panel pl-9 pr-3.5 py-2 text-sm text-ink-primary placeholder:text-ink-faint focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-700"
                    >
                </div>
                <button
                    type="submit"
                    class="flex min-h-[44px] items-center justify-center rounded-lg border border-surface-border bg-surface-panel px-4 py-2 text-sm font-semibold text-ink-primary hover:bg-surface-base focus:outline-none focus:ring-2 focus:ring-brand-700 transition-colors cursor-pointer"
                >
                    Cari
                </button>
                @if ($search)
                    <a
                        href="{{ route('products.index') }}"
                        class="flex min-h-[44px] items-center justify-center rounded-lg border border-surface-border bg-surface-panel px-3 py-2 text-sm font-medium text-ink-muted hover:bg-surface-base hover:text-ink-primary focus:outline-none focus:ring-2 focus:ring-brand-700 transition-colors"
                    >
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Product List Container --}}
        <div class="overflow-hidden rounded-xl border border-surface-border bg-surface-panel shadow-xs">
            @if ($products->isEmpty())
                <div class="px-5 py-12 text-center">
                    @if ($search)
                        <p class="text-sm font-medium text-ink-secondary">
                            Tidak ada produk yang cocok dengan &ldquo;{{ $search }}&rdquo;.
                        </p>
                        <p class="mt-1 text-xs text-ink-muted">
                            Coba kata kunci lain atau bersihkan kotak pencarian.
                        </p>
                        <div class="mt-4">
                            <a
                                href="{{ route('products.index') }}"
                                class="inline-flex min-h-[44px] items-center justify-center rounded-lg border border-surface-border px-4 py-2 text-xs font-semibold text-ink-secondary hover:bg-surface-base focus:outline-none focus:ring-2 focus:ring-brand-700 transition-colors"
                            >
                                Reset Pencarian
                            </a>
                        </div>
                    @else
                        <p class="text-sm font-medium text-ink-secondary">
                            Belum ada produk terdaftar.
                        </p>
                        <p class="mt-1 text-xs text-ink-muted">
                            Tambahkan produk baru untuk mulai mengelola stok dan tanggal kedaluwarsa.
                        </p>
                        <div class="mt-4">
                            <a
                                href="{{ route('products.create') }}"
                                class="inline-flex min-h-[44px] items-center justify-center rounded-lg bg-brand-700 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-700 transition-colors cursor-pointer"
                            >
                                Tambah Produk Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            @else
                {{-- Desktop Table View --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-surface-border text-left">
                        <thead class="bg-surface-base text-xs font-semibold uppercase tracking-wider text-ink-muted">
                            <tr>
                                <th scope="col" class="px-5 py-3">Produk</th>
                                <th scope="col" class="px-5 py-3 text-right">Harga Normal</th>
                                <th scope="col" class="px-5 py-3 text-right">Stok Aktif</th>
                                <th scope="col" class="px-5 py-3">Terdaftar</th>
                                <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border-subtle bg-surface-panel text-sm text-ink-primary">
                            @foreach ($products as $product)
                                <tr class="hover:bg-surface-base/50 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            @if ($product->image)
                                                <img
                                                    src="{{ asset('storage/' . $product->image) }}"
                                                    alt="{{ $product->name }}"
                                                    class="h-10 w-10 shrink-0 rounded-lg object-cover border border-surface-border"
                                                    loading="lazy"
                                                >
                                            @else
                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-surface-border-subtle text-ink-faint border border-surface-border" aria-hidden="true">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <span class="truncate block max-w-xs lg:max-w-md font-medium text-ink-primary" title="{{ $product->name }}">
                                                    {{ $product->name }}
                                                </span>
                                                <span class="text-xs text-ink-faint">ID: #{{ $product->id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-medium tabular-nums text-ink-primary">
                                        <x-price :value="$product->normal_price" />
                                    </td>
                                    @php
                                        $activeStock = (int) ($product->active_stock ?? 0);
                                    @endphp
                                    <td class="px-5 py-3.5 text-right font-medium tabular-nums {{ $activeStock > 0 ? 'text-ink-primary' : 'text-danger-600' }}">
                                        {{ number_format($activeStock, 0, ',', '.') }} unit
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-ink-secondary tabular-nums">
                                        {{ $product->created_at?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <a
                                                href="{{ route('products.edit', $product) }}"
                                                class="flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg px-2.5 py-1 text-xs font-semibold text-ink-secondary hover:bg-surface-base hover:text-ink-primary focus:outline-none focus:ring-2 focus:ring-brand-700 transition-colors"
                                            >
                                                Edit
                                            </a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini? Semua batch terkait juga akan dihapus.');">
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
                    @foreach ($products as $product)
                        <div class="p-4 space-y-3">
                            <div class="flex items-start gap-3">
                                @if ($product->image)
                                    <img
                                        src="{{ asset('storage/' . $product->image) }}"
                                        alt="{{ $product->name }}"
                                        class="h-12 w-12 shrink-0 rounded-lg object-cover border border-surface-border"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-surface-border-subtle text-ink-faint border border-surface-border" aria-hidden="true">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-ink-primary" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </p>
                                    <div class="mt-1 flex items-center justify-between text-xs">
                                        <x-price :value="$product->normal_price" class="font-medium text-ink-primary tabular-nums" />
                                        <span class="text-ink-secondary">Stok: <strong class="font-medium {{ ($product->active_stock ?? 0) > 0 ? 'text-ink-primary' : 'text-danger-600' }} tabular-nums">{{ number_format((int) ($product->active_stock ?? 0), 0, ',', '.') }}</strong></span>
                                    </div>
                                    <p class="mt-1 text-xs text-ink-muted">
                                        Terdaftar: {{ $product->created_at?->format('d/m/Y') ?? '-' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-surface-border-subtle">
                                <a
                                    href="{{ route('products.edit', $product) }}"
                                    class="flex min-h-[44px] items-center justify-center rounded-lg px-3 py-1.5 text-xs font-semibold text-ink-secondary hover:bg-surface-base hover:text-ink-primary focus:outline-none focus:ring-2 focus:ring-brand-700 transition-colors"
                                >
                                    Edit
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini? Semua batch terkait juga akan dihapus.');">
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

                {{-- Pagination Links --}}
                @if ($products->hasPages())
                    <div class="border-t border-surface-border px-5 py-4">
                        {{ $products->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
