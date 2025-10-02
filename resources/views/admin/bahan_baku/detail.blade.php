@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Detail Bahan Baku</h2>
            <div class="mb-6">
                <dl class="divide-y divide-gray-200">
                    <div class="py-2 flex justify-between">
                        <dt class="font-medium text-gray-700">Nama</dt>
                        <dd class="text-gray-900">{{ $bahanBaku->nama }}</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="font-medium text-gray-700">Kategori</dt>
                        <dd class="text-gray-900">{{ $bahanBaku->kategori }}</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="font-medium text-gray-700">Jumlah</dt>
                        <dd class="text-gray-900">{{ $bahanBaku->jumlah }} {{ $bahanBaku->satuan }}</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="font-medium text-gray-700">Tanggal Masuk</dt>
                        <dd class="text-gray-900">{{ $bahanBaku->tanggal_masuk ? $bahanBaku->tanggal_masuk->format('d M Y') : '-' }}</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="font-medium text-gray-700">Tanggal Kadaluarsa</dt>
                        <dd class="text-gray-900">{{ $bahanBaku->tanggal_kadaluarsa ? $bahanBaku->tanggal_kadaluarsa->format('d M Y') : '-' }}</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="font-medium text-gray-700">Status</dt>
                        <dd class="text-gray-900 capitalize">{{ str_replace('_', ' ', $bahanBaku->status) }}</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="font-medium text-gray-700">Dibuat pada</dt>
                        <dd class="text-gray-900">{{ $bahanBaku->created_at ? $bahanBaku->created_at->format('d M Y H:i') : '-' }}</dd>
                    </div>
                </dl>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.bahan_baku.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Kembali
                </a>
                <form method="POST" action="{{ route('admin.bahan_baku.destroy', $bahanBaku) }}" onsubmit="return confirm('Yakin ingin menghapus bahan baku ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
