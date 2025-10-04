@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-10 space-y-10">
        <div class="space-y-3">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Halo, {{ $userName ?? 'Tim Dapur' }} 👋</h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola permintaan bahan baku dapur dengan cepat dan mudah.</p>
                </div>
            </div>
        </div>

        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Permintaan</h2>
                    <p class="text-sm text-gray-500">Snapshot status terbaru permintaan bahan baku milikmu.</p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                @foreach ($statCards ?? [] as $card)
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-gray-200">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ $card['label'] }}</p>
                            </div>
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl border text-sm font-semibold {{ $card['accent'] }}">
                                {{ $card['value'] ?? 0 }}
                            </span>
                        </div>
                        <p class="mt-4 text-sm text-gray-500">{{ $card['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2">
            <article class="rounded-2xl border border-blue-100 bg-blue-50/60 p-6">
                <h3 class="text-base font-semibold text-blue-900">Quick Action</h3>
                <p class="mt-2 text-sm text-blue-800">Mulai permintaan baru kapan saja dan pantau statusnya dari daftar riwayat.</p>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('user.permintaan.index', ['open' => 'create']) }}" class="flex items-center justify-between rounded-xl bg-white px-4 py-3 text-sm font-medium text-blue-700 shadow-sm transition hover:bg-blue-100">
                        <span>Buat permintaan bahan</span>
                        <span class="text-lg">→</span>
                    </a>
                    <a href="{{ route('user.permintaan.index') }}" class="flex items-center justify-between rounded-xl bg-white px-4 py-3 text-sm font-medium text-blue-700 shadow-sm transition hover:bg-blue-100">
                        <span>Lihat riwayat permintaan</span>
                        <span class="text-lg">→</span>
                    </a>
                </div>
            </article>

            <article class="rounded-2xl border border-gray-100 bg-white p-6">
                <h3 class="text-base font-semibold text-gray-900">Tips Cepat</h3>
                <ul class="mt-4 space-y-3 text-sm text-gray-600">
                    <li class="flex gap-3">Pastikan jumlah permintaan sesuai kebutuhan harian agar stok gudang tetap seimbang.</li>
                    <li class="flex gap-3">Ajukan permintaan minimal satu hari sebelum bahan dibutuhkan untuk memastikan ketersediaan.</li>
                    <li class="flex gap-3">Jika permintaan ditolak, cek catatan gudang di detail permintaan untuk melakukan revisi dengan cepat.</li>
                </ul>
            </article>
        </section>
    </div>
@endsection
