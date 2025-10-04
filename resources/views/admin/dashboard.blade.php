@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <header class="flex flex-col gap-2">
            <h1 class="text-2xl font-semibold text-gray-900">Halo, {{ $userName ?? 'Tim Gudang' }} 👋</h1>
            <p class="text-sm text-gray-500">Ringkasan singkat permintaan dapur dan stok yang perlu perhatian.</p>
        </header>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @forelse($statCards ?? [] as $card)
                <article class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">{{ $card['label'] }}</p>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-3xl font-semibold text-gray-900">{{ number_format((int) ($card['value'] ?? 0)) }}</span>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl border text-sm font-semibold {{ $card['accent'] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                            @if(!empty($card['icon']))
                                <x-dynamic-component :component="$card['icon']" class="h-5 w-5" />
                            @endif
                        </span>
                    </div>
                    @if(!empty($card['description']))
                        <p class="mt-3 text-xs text-gray-500">{{ $card['description'] }}</p>
                    @endif
                </article>
            @empty
                <p class="text-sm text-gray-500">Belum ada data permintaan yang dapat ditampilkan.</p>
            @endforelse
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <article class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Antrian permintaan</h2>
                        <p class="text-sm text-gray-500">{{ ($menungguCount ?? 0) > 0 ? 'Prioritaskan permintaan yang sudah menunggu lama.' : 'Tidak ada permintaan menunggu saat ini.' }}</p>
                    </div>
                    <a href="{{ route('admin.permintaan.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Kelola</a>
                </div>
                <ul class="mt-4 space-y-3 text-sm text-gray-600">
                    @forelse($pendingHighlights ?? [] as $item)
                        <li class="rounded-xl border border-gray-100 px-3 py-2">
                            <div class="flex items-center justify-between gap-3">
                                <span class="font-medium text-gray-900">{{ $item['menu'] }}</span>
                                <span class="text-xs text-gray-500">{{ $item['created_diff'] }}</span>
                            </div>
                            <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                <span>Pemohon: <strong>{{ $item['pemohon'] }}</strong></span>
                                <span>Masak: {{ $item['tgl_masak_label'] }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="rounded-xl border border-dashed border-gray-200 px-3 py-6 text-center text-sm text-gray-500">Belum ada permintaan menunggu.</li>
                    @endforelse
                </ul>
            </article>

            <article class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Stok prioritas</h2>
                        <p class="text-sm text-gray-500">Fokuskan restock atau distribusi ke bahan berikut.</p>
                    </div>
                    <a href="{{ route('admin.bahan_baku.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">Kelola</a>
                </div>
                <ul class="mt-4 space-y-3 text-sm text-gray-600">
                    @forelse($priorityStocks ?? [] as $stock)
                        <li class="rounded-xl border border-gray-100 px-3 py-2">
                            <div class="flex items-center justify-between gap-3">
                                <span class="font-medium text-gray-900">{{ $stock['nama'] }}</span>
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $stock['status_badge_class'] }}">{{ $stock['status_badge_label'] }}</span>
                            </div>
                            <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                <span>Sisa stok: <strong class="text-gray-700">{{ $stock['jumlah_label'] }}</strong></span>
                                <span>Kadaluarsa: {{ $stock['tanggal_kadaluarsa_label'] }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="rounded-xl border border-dashed border-gray-200 px-3 py-6 text-center text-sm text-gray-500">Semua stok aman.</li>
                    @endforelse
                </ul>
            </article>
        </section>

        <section class="rounded-2xl border border-blue-100 bg-blue-50/60 p-5">
            <h2 class="text-base font-semibold text-blue-900">Catatan singkat</h2>
            <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 text-sm text-blue-800">
                <div class="rounded-xl bg-white/70 px-3 py-3">
                    <p class="font-semibold text-blue-900">Permintaan minggu ini</p>
                    <p class="mt-1 text-2xl font-bold text-blue-700">{{ number_format((int) ($permintaanMingguIni ?? 0)) }}</p>
                </div>
                <div class="rounded-xl bg-white/70 px-3 py-3">
                    <p class="font-semibold text-blue-900">Total permintaan</p>
                    <p class="mt-1 text-2xl font-bold text-blue-700">{{ number_format((int) ($totalPermintaan ?? 0)) }}</p>
                </div>
                <div class="rounded-xl bg-white/70 px-3 py-3">
                    <p class="font-semibold text-blue-900">Aksi cepat</p>
                    <ul class="mt-2 space-y-2 text-xs text-blue-800">
                        <li class="flex items-center gap-2">
                            <x-heroicon-o-inbox-arrow-down class="h-4 w-4" />
                            <a href="{{ route('admin.permintaan.index') }}" class="underline">Proses permintaan</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <x-heroicon-o-cube class="h-4 w-4" />
                            <a href="{{ route('admin.bahan_baku.index') }}" class="underline">Kelola stok</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <x-heroicon-o-user class="h-4 w-4" />
                            <a href="{{ route('profile') }}" class="underline">Perbarui profil</a>
                        </li>
                    </ul>
                </div>
                <div class="rounded-xl bg-white/70 px-3 py-3">
                    <p class="font-semibold text-blue-900">Catatan</p>
                    <p class="mt-1 text-xs">Pastikan permintaan menunggu diselesaikan sebelum pergantian shift.</p>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
