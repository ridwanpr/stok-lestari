<x-layouts.guest>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <span class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-semibold tracking-wider text-brand-900 bg-brand-100 rounded">
                PORTAL PENJUAL
            </span>
            <h1 class="mt-3 text-2xl font-bold tracking-tight text-ink-primary">
                Masuk ke Stok Lestari
            </h1>
            <p class="mt-1 text-sm text-ink-muted">
                Kelola stok toko dan pantau barang kedaluwarsa.
            </p>
        </div>

        <div class="mt-8 bg-surface-panel px-6 py-8 shadow-sm ring-1 ring-surface-border rounded-xl sm:px-8">
            <form action="{{ route('login.store') }}" method="POST" class="space-y-5" novalidate>
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-ink-primary">
                        Email
                    </label>
                    <div class="mt-1.5">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="nama@toko.com"
                            aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                            aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}"
                            class="block w-full min-h-[44px] rounded-lg border {{ $errors->has('email') ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600' : 'border-surface-border focus:border-brand-700 focus:ring-brand-700' }} bg-surface-panel px-3.5 py-2.5 text-sm text-ink-primary placeholder:text-ink-faint focus:outline-none focus:ring-2 sm:leading-6"
                        >
                    </div>
                    @error('email')
                        <p id="email-error" class="mt-1.5 text-xs font-medium text-danger-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-ink-primary">
                            Kata Sandi
                        </label>
                    </div>
                    <div class="relative mt-1.5">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                            aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
                            class="block w-full min-h-[44px] rounded-lg border {{ $errors->has('password') ? 'border-danger-600 focus:border-danger-600 focus:ring-danger-600' : 'border-surface-border focus:border-brand-700 focus:ring-brand-700' }} bg-surface-panel px-3.5 py-2.5 pr-24 text-sm text-ink-primary placeholder:text-ink-faint focus:outline-none focus:ring-2 sm:leading-6"
                        >
                        <button
                            type="button"
                            data-toggle-password="password"
                            aria-label="Tampilkan kata sandi"
                            aria-pressed="false"
                            class="absolute inset-y-0 right-0 flex items-center px-3.5 text-xs font-medium text-ink-muted hover:text-ink-primary focus:outline-none focus:ring-2 focus:ring-brand-700 rounded-r-lg"
                        >
                            <span class="password-toggle-text">Tampilkan</span>
                        </button>
                    </div>
                    @error('password')
                        <p id="password-error" class="mt-1.5 text-xs font-medium text-danger-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        value="1"
                        {{ old('remember') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-surface-border text-brand-700 focus:ring-brand-700 focus:ring-2"
                    >
                    <label for="remember" class="ml-2.5 block text-sm text-ink-secondary select-none">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                <div>
                    <button
                        type="submit"
                        class="flex w-full min-h-[44px] items-center justify-center rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-700 focus:ring-offset-2 transition-colors cursor-pointer"
                    >
                        Masuk
                    </button>
                </div>
            </form>

            @if (app()->isLocal())
                <div class="mt-6 border-t border-surface-border-subtle pt-4">
                    <div class="rounded-md bg-surface-base p-3 border border-surface-border">
                        <p class="text-xs font-semibold text-ink-secondary uppercase tracking-wider">
                            Akun Demo Lokal
                        </p>
                        <dl class="mt-2 space-y-1 text-xs">
                            <div class="flex justify-between">
                                <dt class="text-ink-muted">Email:</dt>
                                <dd class="font-mono text-ink-primary font-medium select-all break-all text-right">seller@stoklestari.test</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-ink-muted">Sandi:</dt>
                                <dd class="font-mono text-ink-primary font-medium select-all">password</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.guest>
