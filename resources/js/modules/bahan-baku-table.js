import { escapeHtml, humanizeValue } from './string-utils';

const bahanBakuTable = {
    tableSelector: '#tabel-bahan-baku',

    // ambil elemen tabel utama
    getTable() {
        return document.querySelector(this.tableSelector);
    },

    // ambil tbody biar gampang nambah baris
    getTbody() {
        const table = this.getTable();
        return table ? table.querySelector('tbody') : null;
    },

    // bikin baris baru di tabel bahan baku
    createRow(data) {
        const tbody = this.getTbody();
        if (!tbody) {
            return null;
        }

        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.dataset.bahanBakuId = data.id;

        this.renderRow(row, data);

        tbody.insertBefore(row, tbody.firstChild);
        document.dispatchEvent(new Event('bahan-baku:table-updated'));

        return row;
    },

    // render ulang isi baris sesuai data
    renderRow(row, data) {
        if (!row || !data) {
            return;
        }

        const tanggalMasuk = escapeHtml(data.tanggal_masuk_label || data.tanggal_masuk || '-');
        const tanggalKadaluarsa = escapeHtml(data.tanggal_kadaluarsa_label || data.tanggal_kadaluarsa || '-');
        const statusRaw = data.status_label || data.status;
        const statusLabel = escapeHtml(humanizeValue(statusRaw));
        const jumlahLabel = escapeHtml(data.jumlah_label || `${data.jumlah} ${data.satuan}`);
        const nama = escapeHtml(data.nama);
        const kategori = escapeHtml(data.kategori);
        const jumlah = escapeHtml(data.jumlah);
        const satuan = escapeHtml(data.satuan);
        const statusValue = escapeHtml(data.status);
        const viewUrl = escapeHtml(data.view_url || '');
        const editUrl = escapeHtml(data.edit_url || '');
        const deleteUrl = escapeHtml(data.delete_url || '');
        const id = escapeHtml(data.id);
        const tanggalKadaluarsaIso = escapeHtml(data.tanggal_kadaluarsa_iso || '');

        const realTimeExpired = (() => {
            if (!tanggalKadaluarsaIso) {
                return false;
            }
            const today = new Date();
            const expiry = new Date(tanggalKadaluarsaIso);
            return today >= expiry;
        })();

        const canDeleteBool = (
            data.can_delete === true ||
            data.can_delete === 'true' ||
            data.status === 'kadaluarsa' ||
            realTimeExpired
        );
        const canDelete = canDeleteBool ? 'true' : 'false';

        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">${nama}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${kategori}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${jumlah}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${satuan}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${tanggalMasuk}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${tanggalKadaluarsa}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="capitalize">${statusLabel}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-1">
                    ${viewUrl ? `
                        <a href="${viewUrl}"
                           class="inline-flex items-center px-2 py-1 border border-gray-300 rounded text-xs font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150">
                            <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Lihat
                        </a>
                    ` : ''}
                    <button type="button"
                            class="tombol-edit-native inline-flex items-center px-2 py-1 border border-blue-300 rounded text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150"
                            data-action="${editUrl}"
                            data-id="${id}"
                            data-nama="${nama}"
                            data-kategori="${kategori}"
                            data-jumlah="${jumlah}"
                            data-satuan="${satuan}"
                            data-jumlah-label="${jumlahLabel}"
                            data-status="${statusValue}"
                            data-status-label="${statusLabel}"
                            data-tanggal-masuk-label="${tanggalMasuk}"
                            data-tanggal-kadaluarsa-label="${tanggalKadaluarsa}">
                        <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652l-1.688 1.687m-2.651-2.651L8.25 15.75M19.5 19.5h-15" />
                        </svg>
                        Edit
                    </button>
                    <button type="button"
                            class="tombol-hapus-native inline-flex items-center px-2 py-1 border border-red-300 rounded text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition ease-in-out duration-150"
                            data-action="${deleteUrl}"
                            data-nama="${nama}"
                            data-id="${id}"
                            data-kategori="${kategori}"
                            data-jumlah="${jumlah}"
                            data-satuan="${satuan}"
                            data-jumlah-label="${jumlahLabel}"
                            data-status="${statusValue}"
                            data-status-label="${statusLabel}"
                            data-tanggal-masuk-label="${tanggalMasuk}"
                            data-tanggal-kadaluarsa-label="${tanggalKadaluarsa}"
                            data-tanggal-kadaluarsa-iso="${tanggalKadaluarsaIso}"
                            data-can-delete="${canDelete}"
                            data-real-time-expired="${realTimeExpired}">
                        <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Hapus
                    </button>
                </div>
            </td>
        `;
    },

    // update baris kalau data berubah
    updateRow(data) {
        const row = document.querySelector(`tr[data-bahan-baku-id="${data.id}"]`);
        if (!row) {
            return this.createRow(data);
        }

        this.renderRow(row, data);
        document.dispatchEvent(new Event('bahan-baku:table-updated'));

        return row;
    },

    // hapus baris bahan baku dari tabel
    removeRow(id) {
        const row = document.querySelector(`tr[data-bahan-baku-id="${id}"]`);
        if (row) {
            row.remove();
            document.dispatchEvent(new Event('bahan-baku:table-updated'));
        }
    },
};

export default bahanBakuTable;
