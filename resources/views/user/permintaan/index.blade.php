@extends('layouts.app')

@section('content')
    <x-session-notifications />

    <div id="permintaan-page-data" class="hidden" data-old-details='@json($oldDetails ?? [])' data-open-create="{{ !empty($shouldOpenCreateModal) ? '1' : '0' }}"></div>

    <div class="max-w-6xl mx-auto px-4 py-10 space-y-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Riwayat Permintaan Bahan</h1>
                <p class="text-sm text-gray-500 mt-1">Pantau status pengajuan bahan baku yang pernah kamu buat.</p>
            </div>
            <button type="button" id="open-create-modal" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">Buat Permintaan Baru</button>
        </div>

        <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Masak</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($permintaan as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item['tgl_masak_label'] ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['menu'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full border {{ $item['status_badge_class'] }}">{{ $item['status_label'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    <div class="flex items-center gap-2 justify-start">
                                        <button type="button" data-detail-trigger data-detail='@json($item['detail_payload'])' data-menu="{{ $item['menu'] }}" data-tanggal="{{ $item['tgl_masak_label'] ?? '' }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition">Detail</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">Belum ada permintaan yang tercatat. Mulai dengan membuat permintaan bahan pertama kamu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modal.base id="create-permintaan-modal" title="Buat Permintaan Bahan" size="2xl">
        <div class="px-2 sm:px-0 space-y-6">
            @include('user.permintaan.partials.create-form', ['bahanBaku' => $bahanBaku])
        </div>
    </x-modal.base>

    <x-modal.base id="permintaan-detail-modal" title="Detail Permintaan" size="xl">
        <div class="space-y-5">
            <p class="text-xs text-gray-500" id="detail-meta"></p>
            <div>
                <div id="detail-empty" class="hidden text-sm text-gray-500">Tidak ada detail bahan.</div>
                <div id="detail-content" class="space-y-4">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 uppercase text-xs tracking-wide">
                                <th class="pb-2">Bahan</th>
                                <th class="pb-2">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody id="detail-list" class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="button" class="tutup-modal inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Tutup</button>
            </div>
        </div>
    </x-modal.base>
@endsection
