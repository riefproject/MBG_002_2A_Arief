@props([
    'bahanBaku' => collect(),
])

<form id="permintaan-form" action="{{ route('user.permintaan.store') }}" method="POST" class="space-y-6">
    @csrf

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="tgl_masak" class="block text-sm font-medium text-gray-700">Tanggal Masak</label>
            <input type="date" id="tgl_masak" name="tgl_masak" value="{{ old('tgl_masak') }}" min="{{ now()->toDateString() }}" class="mt-2 block w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm placeholder:text-xs placeholder:text-gray-400 outline outline-1 outline-gray-200 focus:outline-blue-200" required>
        </div>
        <div>
            <label for="menu_makan" class="block text-sm font-medium text-gray-700">Menu Masakan</label>
            <input type="text" id="menu_makan" name="menu_makan" value="{{ old('menu_makan') }}" placeholder="Contoh: Nasi Goreng Spesial" class="mt-2 block w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm placeholder:text-xs placeholder:text-gray-400 outline outline-1 outline-gray-200 focus:outline-blue-200" required>
        </div>
        <div>
            <label for="jumlah_porsi" class="block text-sm font-medium text-gray-700">Jumlah Porsi</label>
            <input type="number" min="1" id="jumlah_porsi" name="jumlah_porsi" value="{{ old('jumlah_porsi') }}" class="mt-2 block w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 px-4 py-2 text-sm placeholder:text-xs placeholder:text-gray-400 outline outline-1 outline-gray-200 focus:outline-blue-200" required>
        </div>
    </div>

    <div class="space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <h4 class="text-base font-semibold text-gray-900">Pilih Bahan Baku</h4>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <div class="flex items-center gap-2">
                    <label for="bahan-search-input" class="sr-only">Cari bahan</label>
                    <input type="search" id="bahan-search-input" class="h-10 w-full sm:w-56 rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 px-4 text-sm placeholder:text-xs placeholder:text-gray-400 outline outline-1 outline-gray-200 focus:outline-blue-200" placeholder="Cari nama bahan">
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="bahan-search-button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition">Cari</button>
                    <button type="button" id="bahan-reset-button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-100 transition">Reset</button>
                </div>
            </div>
        </div>

        <div class="border border-gray-100 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm" id="bahan-table">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                        <tr>
                            <th scope="col" class="px-4 py-3 w-12 text-center">
                                <input type="checkbox" id="select-all-checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th scope="col" class="px-4 py-3 text-left">Qty diminta</th>
                            <th scope="col" class="px-4 py-3 text-left">Nama Bahan</th>
                            <th scope="col" class="px-4 py-3 text-left">Stok tersedia</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="bahan-table-body">
                        @forelse ($bahanBaku as $bahan)
                            <tr class="bahan-row" data-bahan-id="{{ $bahan->id }}" data-bahan-name="{{ strtolower($bahan->nama) }}" data-bahan-satuan="{{ $bahan->satuan }}" data-bahan-nama="{{ $bahan->nama }}">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" class="detail-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" aria-label="Pilih {{ $bahan->nama }}">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <input type="number" min="1" class="qty-input block w-24 rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100 px-3 py-2 text-sm placeholder:text-xs placeholder:text-gray-400 outline outline-1 outline-gray-200 focus:outline-blue-200" placeholder="0" disabled>
                                        <span class="text-xs text-gray-500">{{ $bahan->satuan }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-800 font-medium">{{ $bahan->nama }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ (int) $bahan->jumlah }} {{ $bahan->satuan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada stok bahan baku yang tersedia untuk dipilih.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h4 class="text-base font-semibold text-gray-900">Ringkasan Permintaan</h4>
            <span class="text-sm text-gray-500" id="summary-counter">0 bahan dipilih</span>
        </div>
        <div id="summary-alert" class="hidden rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>
        <div id="summary-empty" class="text-sm text-gray-500">Belum ada bahan yang dipilih. Pilih bahan di tabel untuk melihat ringkasan.</div>
        <div id="summary-content" class="hidden">
            <ul id="summary-list" class="space-y-2 text-sm text-gray-700"></ul>
        </div>
    </div>

    <div id="details-hidden"></div>

    <div class="flex flex-col md:flex-row md:items-center md:justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button" class="tutup-modal inline-flex justify-center items-center px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Batal</button>
        <button type="submit" class="inline-flex justify-center items-center px-6 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">Kirim Permintaan</button>
    </div>
</form>
