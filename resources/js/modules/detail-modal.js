import { parseJsonSafe, openModalSafely } from './modal-utils';

// siapin modal detail permintaan plus triggernya
export default function initDetailModal({
    modalId,
    triggerSelector,
    listSelector,
    metaSelector,
    emptySelector,
}) {
    const modal = document.getElementById(modalId);
    if (!modal) {
        return;
    }

    const detailList = modal.querySelector(listSelector);
    const detailMeta = modal.querySelector(metaSelector);
    const detailEmptyState = modal.querySelector(emptySelector);

    if (!detailList || !detailMeta || !detailEmptyState) {
        return;
    }

    document.querySelectorAll(triggerSelector).forEach((button) => {
        button.addEventListener('click', () => {
            const detailPayload = parseJsonSafe(button.dataset.detail, []);
            const menu = button.dataset.menu || '';
            const tanggal = button.dataset.tanggal || '';

            detailList.innerHTML = '';

            if (!detailPayload.length) {
                detailEmptyState.classList.remove('hidden');
            } else {
                detailEmptyState.classList.add('hidden');

                detailPayload.forEach((item) => {
                    const row = document.createElement('tr');
                    row.className = 'text-gray-700';

                    const bahanCell = document.createElement('td');
                    bahanCell.className = 'py-2';
                    bahanCell.textContent = item.bahan || '-';

                    const jumlahCell = document.createElement('td');
                    jumlahCell.className = 'py-2 text-right';
                    const satuan = item.satuan || '';
                    jumlahCell.textContent = `${item.jumlah} ${satuan}`.trim();

                    row.appendChild(bahanCell);
                    row.appendChild(jumlahCell);
                    detailList.appendChild(row);
                });
            }

            detailMeta.textContent = [menu, tanggal].filter(Boolean).join(' • ');

            openModalSafely(modalId);
        });
    });
}
