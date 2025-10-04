import { parseJsonSafe } from './modules/modal-utils';
import initDetailModal from './modules/detail-modal';
import MaterialSelector from './modules/material-selector';

const DETAIL_MODAL_ID = 'permintaan-detail-modal';
const CREATE_MODAL_ID = 'create-permintaan-modal';

// siapin modul detail dan selector pas dom siap
document.addEventListener('DOMContentLoaded', () => {
    initDetailModal({
        modalId: DETAIL_MODAL_ID,
        triggerSelector: '[data-detail-trigger]',
        listSelector: '#detail-list',
        metaSelector: '#detail-meta',
        emptySelector: '#detail-empty',
    });

    const configNode = document.getElementById('permintaan-page-data');
    if (!configNode) {
        return;
    }

    const initialDetails = parseJsonSafe(configNode.dataset.oldDetails, []);
    const shouldOpen = configNode.dataset.openCreate === '1';

    const selector = new MaterialSelector({
        modalId: CREATE_MODAL_ID,
        selectors: {
            openTrigger: 'open-create-modal',
            form: '#permintaan-form',
            tableBody: '#bahan-table-body',
            selectAll: '#select-all-checkbox',
            searchInput: '#bahan-search-input',
            searchButton: '#bahan-search-button',
            resetButton: '#bahan-reset-button',
            hidden: '#details-hidden',
            row: '.bahan-row',
            checkbox: '.detail-checkbox',
            qtyInput: '.qty-input',
            summary: {
                empty: '#summary-empty',
                content: '#summary-content',
                list: '#summary-list',
                counter: '#summary-counter',
                alert: '#summary-alert',
            },
        },
    });

    selector.init(initialDetails, shouldOpen);
});
