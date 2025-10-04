@extends('layouts.app')

@section('content')
<x-session-notifications />
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Permintaan Bahan Menunggu</h1>
                        <p class="text-gray-600">Kelola permintaan bahan dari dapur yang membutuhkan tindakan.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Permintaan</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menu</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemohon</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($permintaan as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ optional($item->created_at)->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->menu_makan }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ optional($item->pemohon)->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <button type="button" onclick="togglePermintaanDetail(this, 'detail-{{ $item->id }}')" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 text-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
                                                Lihat Detail
                                            </button>

                                            <form method="POST" action="{{ route('admin.permintaan.setujui', $item->id) }}">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Yakin ingin menyetujui permintaan ini?')" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md bg-green-600 text-white text-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    Setujui
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.permintaan.tolak', $item->id) }}">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Yakin ingin menolak permintaan ini?')" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md bg-red-600 text-white text-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="detail-{{ $item->id }}" class="hidden bg-gray-50">
                                    <td colspan="5" class="px-4 py-4">
                                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                            <div class="px-4 py-3 border-b border-gray-200">
                                                <h4 class="text-sm font-semibold text-gray-900">Detail Permintaan</h4>
                                            </div>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th scope="col" class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                                                            <th scope="col" class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Bahan</th>
                                                            <th scope="col" class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah Diminta</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-100">
                                                        @forelse ($item->details as $detail)
                                                        {{-- @php dd($item->details) @endphp --}}
                                                            <tr>
                                                                <td class="px-4 py-2 text-sm text-gray-700">{{ $loop->iteration }}</td>
                                                                <td class="px-4 py-2 text-sm text-gray-700">{{ $detail->bahan_nama_label ?? '-' }}</td>
                                                                <td class="px-4 py-2 text-sm text-gray-700">{{ number_format($detail->jumlah_diminta) }} {{ $detail->bahan_satuan_label ?? '' }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">Belum ada detail permintaan.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                                        Belum ada permintaan dengan status menunggu.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePermintaanDetail(button, rowId) {
        const row = document.getElementById(rowId);
        if (!row) {
            return;
        }
        const hidden = row.classList.contains('hidden');
        row.classList.toggle('hidden');
        button.textContent = hidden ? 'Tutup Detail' : 'Lihat Detail';
    }
</script>
@endpush
