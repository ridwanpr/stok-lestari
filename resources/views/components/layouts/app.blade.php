<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-surface-base">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Stok Lestari') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-ink-primary antialiased bg-surface-base">
    <div class="min-h-full">
        {{-- Mobile slide-over drawer --}}
        <div id="mobile-nav" class="relative z-50 md:hidden hidden" role="dialog" aria-modal="true" aria-hidden="true">
            <div id="mobile-nav-backdrop" class="fixed inset-0 bg-ink-primary/50 transition-opacity"></div>

            <div class="fixed inset-y-0 left-0 flex w-full max-w-xs flex-col bg-surface-panel shadow-xl">
                <div class="flex items-center justify-between border-b border-surface-border px-4 py-4">
                    <div class="flex items-center gap-2">
                        <span class="inline-block h-3 w-3 rounded-full bg-brand-700"></span>
                        <span class="text-base font-bold text-ink-primary tracking-tight">Stok Lestari</span>
                    </div>
                    <button
                        type="button"
                        id="close-mobile-nav"
                        aria-label="Tutup navigasi"
                        class="flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg text-ink-muted hover:bg-surface-border-subtle hover:text-ink-primary focus:outline-none focus:ring-2 focus:ring-brand-700"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 space-y-1 px-4 py-4" aria-label="Navigasi Seluler">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        <span>Dashboard</span>
                    </x-nav-link>

                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <span>Produk</span>
                    </x-nav-link>

                    <x-nav-link :href="route('flash-sales.index')" :active="request()->routeIs('flash-sales.*')">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                        <span>Flash Sale</span>
                    </x-nav-link>
                </nav>

                <div class="border-t border-surface-border p-4">
                    <div class="flex items-center justify-between">
                        <div class="truncate">
                            <p class="text-xs text-ink-muted">Toko</p>
                            <p class="truncate text-sm font-semibold text-ink-primary">{{ auth()->user()->name }}</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="flex min-h-[44px] items-center rounded-lg px-3 py-2 text-xs font-semibold text-danger-700 hover:bg-danger-50 focus:outline-none focus:ring-2 focus:ring-danger-600 transition-colors cursor-pointer"
                            >
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Desktop fixed sidebar --}}
        <aside class="hidden md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col md:border-r md:border-surface-border md:bg-surface-panel">
            <div class="flex h-16 shrink-0 items-center gap-2.5 border-b border-surface-border px-6">
                <span class="inline-block h-3.5 w-3.5 rounded-full bg-brand-700"></span>
                <span class="text-base font-bold text-ink-primary tracking-tight">Stok Lestari</span>
            </div>

            <nav class="flex flex-1 flex-col justify-between overflow-y-auto px-4 py-5" aria-label="Navigasi Utama">
                <div class="space-y-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        <span>Dashboard</span>
                    </x-nav-link>

                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <span>Produk</span>
                    </x-nav-link>

                    <x-nav-link :href="route('flash-sales.index')" :active="request()->routeIs('flash-sales.*')">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                        <span>Flash Sale</span>
                    </x-nav-link>
                </div>

                <div class="border-t border-surface-border pt-4">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 pr-2">
                            <p class="text-xs text-ink-muted">Toko</p>
                            <p class="truncate text-sm font-semibold text-ink-primary">{{ auth()->user()->name }}</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="flex min-h-[44px] items-center rounded-lg px-2.5 py-1.5 text-xs font-semibold text-danger-700 hover:bg-danger-50 focus:outline-none focus:ring-2 focus:ring-danger-600 transition-colors cursor-pointer"
                            >
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </nav>
        </aside>

        {{-- Mobile sticky top bar --}}
        <div class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-surface-border bg-surface-panel px-4 md:hidden">
            <div class="flex items-center gap-2">
                <span class="inline-block h-3 w-3 rounded-full bg-brand-700"></span>
                <span class="text-sm font-bold text-ink-primary tracking-tight">Stok Lestari</span>
            </div>
            <button
                type="button"
                id="open-mobile-nav"
                aria-controls="mobile-nav"
                aria-expanded="false"
                aria-label="Buka navigasi"
                class="flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg text-ink-muted hover:bg-surface-border-subtle hover:text-ink-primary focus:outline-none focus:ring-2 focus:ring-brand-700"
            >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>

        {{-- Main content --}}
        <div class="md:pl-64 flex min-h-screen flex-col">
            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
