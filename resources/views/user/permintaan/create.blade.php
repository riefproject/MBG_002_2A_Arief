@extends('layouts.app')

@section('content')
    <x-session-notifications />

    <div class="max-w-5xl mx-auto px-4 py-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Buat Permintaan Bahan</h1>
                <p class="text-sm text-gray-500 mt-1">Isi formulir berikut untuk mengajukan permintaan bahan baku dapur.</p>
            </div>
            <a href="{{ route('user.permintaan.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Kembali ke Riwayat</a>
        </div>
        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-sm rounded-2xl border border-gray-100">
            <form action="{{ route('user.permintaan.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <label for="tgl_masak" class="block text-sm font-medium text-gray-700">Tanggal Masak</label>
                        <input type="date" id="tgl_masak" name="tgl_masak" value="{{ old('tgl_masak') }}" min="{{ now()->toDateString() }}" class="mt-2 block w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm placeholder:text-xs placeholder:text-gray-400 outline outline-1 outline-gray-200 focus:outline-blue-200" required>
                    </div>
                    <div class="md:col-span-1">
                        <label for="menu_makan" class="block text-sm font-medium text-gray-700">Menu yang akan dibuat</label>
                        <input type="text" id="menu_makan" name="menu_makan" value="{{ old('menu_makan') }}" placeholder="Contoh: Nasi Goreng Spesial" class="mt-2 block w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm placeholder:text-xs placeholder:text-gray-400 outline outline-1 outline-gray-200 focus:outline-blue-200" required>
                    </div>
                    <div class="md:col-span-1">
                        <label for="jumlah_porsi" class="block text-sm font-medium text-gray-700">Jumlah Porsi yang dibuat</label>
                        <input type="number" min="1" id="jumlah_porsi" name="jumlah_porsi" value="{{ old('jumlah_porsi') }}" class="mt-2 block w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm placeholder:text-xs placeholder:text-gray-400 outline outline-1 outline-gray-200 focus:outline-blue-200" required>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Daftar Bahan Baku yang diminta</h2>
                        <button type="button" id="add-material" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition">Tambah Bahan</button>
                    </div>

                    <div id="materials-container" class="space-y-4">
                        @foreach ($detailRows ?? [] as $detail)
                            <div class="material-row grid grid-cols-1 md:grid-cols-12 gap-4 p-4 border border-gray-100 rounded-xl bg-gray-50">
                                <div class="md:col-span-6">
                                    <label class="block text-sm font-medium text-gray-700">Pilih Bahan</label>
                                    <select name="bahan_id[]" class="mt-2 block w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm outline outline-1 outline-gray-200 focus:outline-blue-200" required>
                                        <option value="" disabled {{ ($detail['bahan_id'] ?? null) ? '' : 'selected' }}>Pilih bahan</option>
                                        @foreach ($bahanBaku as $bahan)
                                            <option value="{{ $bahan->id }}" {{ (string) ($detail['bahan_id'] ?? '') === (string) $bahan->id ? 'selected' : '' }}>{{ $bahan->nama }} ({{ $bahan->jumlah }} {{ $bahan->satuan }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-sm font-medium text-gray-700">Jumlah diminta</label>
                                    <input type="number" name="jumlah_diminta[]" min="1" value="{{ $detail['jumlah_diminta'] ?? '' }}" class="mt-2 block w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm placeholder:text-xs placeholder:text-gray-400 outline outline-1 outline-gray-200 focus:outline-blue-200" required>
                                </div>
                                <div class="md:col-span-2 flex items-end">
                                    <button type="button" data-remove class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition">Hapus</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" class="w-full md:w-auto px-8 py-3 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm transition">Kirim Permintaan</button>
                </div>
            </form>
        </div>
    </div>

    <template id="material-row-template">
        <div class="material-row grid grid-cols-1 md:grid-cols-12 gap-4 p-4 border border-gray-100 rounded-xl bg-gray-50">
            <div class="md:col-span-6">
                <label class="block text-sm font-medium text-gray-700">Pilih Bahan</label>
                <select name="bahan_id[]" class="mt-2 block w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm outline outline-1 outline-gray-200 focus:outline-blue-200" required>
                    <option value="" disabled selected>Pilih bahan</option>
                    @foreach ($bahanBaku as $bahan)
                        <option value="{{ $bahan->id }}">{{ $bahan->nama }} ({{ $bahan->jumlah }} {{ $bahan->satuan }})</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4">
                <label class="block text-sm font-medium text-gray-700">Jumlah diminta</label>
                <input type="number" name="jumlah_diminta[]" min="1" class="mt-2 block w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm placeholder:text-xs placeholder:text-gray-400 outline outline-1 outline-gray-200 focus:outline-blue-200" required>
            </div>
            <div class="md:col-span-2 flex items-end">
                <button type="button" data-remove class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition">Hapus</button>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
    <script>
        // setup dinamis daftar bahan pas halaman siap
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('materials-container');
            const template = document.getElementById('material-row-template');
            const addButton = document.getElementById('add-material');

            // tambah baris bahan baru
            addButton.addEventListener('click', function (event) {
                event.preventDefault();
                const clone = template.content.cloneNode(true);
                container.appendChild(clone);
            });

            // hapus baris bahan kalo user klik tombol remove
            container.addEventListener('click', function (event) {
                const button = event.target.closest('[data-remove]');
                if (!button) {
                    return;
                }
                event.preventDefault();
                const row = button.closest('.material-row');
                if (!row) {
                    return;
                }
                if (container.children.length === 1) {
                    row.querySelectorAll('select, input').forEach(function (field) {
                        field.value = '';
                    });
                    return;
                }
                row.remove();
            });
        });
    </script>
@endpush
