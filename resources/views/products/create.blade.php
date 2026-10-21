<x-layouts.app>
    <x-slot:title>Tambah Produk — Stok Lestari</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <nav class="flex items-center text-xs text-ink-muted mb-2">
                <a href="{{ route('products.index') }}" class="hover:text-ink-primary transition-colors">Produk</a>
                <span class="mx-2 text-ink-faint">/</span>
                <span class="text-ink-primary font-medium">Tambah Baru</span>
            </nav>
            <h1 class="text-xl font-bold tracking-tight text-ink-primary sm:text-2xl">
                Tambah Produk Baru
            </h1>
            <p class="mt-1 text-sm text-ink-secondary">
                Daftarkan produk ke katalog sebelum menambahkan batch stok.
            </p>
        </div>

        <div class="rounded-xl border border-surface-border bg-surface-panel p-5 sm:p-6 shadow-xs">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5" novalidate>
                @csrf

                @include('products._form')

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-surface-border-subtle">
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
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
