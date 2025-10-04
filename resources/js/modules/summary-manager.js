// ngurus tampilan ringkasan pilihan bahan
export default class SummaryManager {
    // simpen elemen yg dibutuhin ringkasan
    constructor({ list, emptyState, content, counter, alert }) {
        this.list = list;
        this.emptyState = emptyState;
        this.content = content;
        this.counter = counter;
        this.alert = alert;
    }

    // sembunyiin alert ringkasan
    clearAlert() {
        if (!this.alert) {
            return;
        }
        this.alert.textContent = '';
        this.alert.classList.add('hidden');
    }

    // nampilin alert ringkasan singkat
    showAlert(message) {
        if (!this.alert) {
            return;
        }
        this.alert.textContent = message;
        this.alert.classList.remove('hidden');
    }

    // render ulang ringkasan sesuai baris kepilih
    render(rows) {
        if (!this.list || !this.emptyState || !this.content || !this.counter) {
            return;
        }

        this.list.innerHTML = '';

        if (!rows.length) {
            this.emptyState.classList.remove('hidden');
            this.content.classList.add('hidden');
            this.counter.textContent = '0 bahan dipilih';
            return;
        }

        this.emptyState.classList.add('hidden');
        this.content.classList.remove('hidden');

        rows.forEach((row) => {
            const qtyInput = row.querySelector('.qty-input');
            const qtyValue = qtyInput && qtyInput.value ? parseInt(qtyInput.value, 10) : 0;
            const satuan = row.dataset.bahanSatuan || '';
            const nama = row.dataset.bahanNama || '';

            const listItem = document.createElement('li');
            listItem.className = 'flex items-center justify-between gap-3 bg-gray-50 border border-gray-100 rounded-lg px-3 py-2';

            const title = document.createElement('span');
            title.className = 'font-medium text-gray-800';
            title.textContent = nama;

            const qtyText = document.createElement('span');
            qtyText.className = 'text-sm text-gray-600';
            qtyText.textContent = qtyValue > 0 ? `${qtyValue} ${satuan}`.trim() : 'Jumlah belum diisi';

            listItem.appendChild(title);
            listItem.appendChild(qtyText);
            this.list.appendChild(listItem);
        });

        this.counter.textContent = `${rows.length} bahan dipilih`;
    }
}
