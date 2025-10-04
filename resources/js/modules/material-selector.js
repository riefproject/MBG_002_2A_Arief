import SummaryManager from './summary-manager';
import { openModalSafely } from './modal-utils';

// atur interaksi pilih bahan di form permintaan
export default class MaterialSelector {
    // catet elemen penting dan siapin flag
    constructor({ modalId, selectors }) {
        this.modalId = modalId;
        this.modal = document.getElementById(modalId);
        this.selectors = selectors;

        if (!this.modal) {
            this.enabled = false;
            return;
        }

        this.openTrigger = document.getElementById(selectors.openTrigger);
        this.form = this.modal.querySelector(selectors.form);
        this.tableBody = this.modal.querySelector(selectors.tableBody);
        this.selectAllCheckbox = this.modal.querySelector(selectors.selectAll);
        this.searchInput = this.modal.querySelector(selectors.searchInput);
        this.searchButton = this.modal.querySelector(selectors.searchButton);
        this.resetButton = this.modal.querySelector(selectors.resetButton);
        this.summaryEmpty = this.modal.querySelector(selectors.summary.empty);
        this.summaryContent = this.modal.querySelector(selectors.summary.content);
        this.summaryList = this.modal.querySelector(selectors.summary.list);
        this.summaryCounter = this.modal.querySelector(selectors.summary.counter);
        this.summaryAlert = this.modal.querySelector(selectors.summary.alert);
        this.hiddenContainer = this.modal.querySelector(selectors.hidden);

        this.rowSelector = selectors.row;
        this.checkboxSelector = selectors.checkbox;
        this.qtyInputSelector = selectors.qtyInput;

        this.enabled = Boolean(
            this.form &&
            this.tableBody &&
            this.summaryEmpty &&
            this.summaryContent &&
            this.summaryList &&
            this.summaryCounter &&
            this.hiddenContainer
        );
    }

    // mulai selector pake data awal plus opsi modal kebuka
    init(initialDetails = [], shouldOpen = false) {
        if (!this.enabled) {
            return;
        }

        this.summary = new SummaryManager({
            list: this.summaryList,
            emptyState: this.summaryEmpty,
            content: this.summaryContent,
            counter: this.summaryCounter,
            alert: this.summaryAlert,
        });

        this.bindEvents();
        this.applyInitialDetails(initialDetails);
        this.refreshState();

        if ((Array.isArray(initialDetails) && initialDetails.length) || shouldOpen) {
            this.openModal();
        }
    }

    // pasang semua event handler yg dibutuhin
    bindEvents() {
        if (this.openTrigger) {
            this.openTrigger.addEventListener('click', () => this.openModal());
        }

        this.tableBody.addEventListener('change', (event) => {
            const checkbox = event.target.closest(this.checkboxSelector);
            if (checkbox) {
                const row = checkbox.closest(this.rowSelector);
                if (row) {
                    this.setRowState(row, checkbox.checked, { focus: checkbox.checked });
                }
                this.summary.clearAlert();
                this.refreshState();
                return;
            }

            if (event.target.matches(this.qtyInputSelector)) {
                this.summary.clearAlert();
                this.refreshSummary();
            }
        });

        this.tableBody.addEventListener('input', (event) => {
            if (event.target.matches(this.qtyInputSelector)) {
                this.summary.clearAlert();
                this.refreshSummary();
            }
        });

        if (this.selectAllCheckbox) {
            this.selectAllCheckbox.addEventListener('change', () => {
                this.getVisibleRows().forEach((row) => {
                    const checkbox = row.querySelector(this.checkboxSelector);
                    if (checkbox) {
                        checkbox.checked = this.selectAllCheckbox.checked;
                        this.setRowState(row, checkbox.checked);
                    }
                });

                this.summary.clearAlert();
                this.refreshState();
            });
        }

        if (this.searchButton) {
            this.searchButton.addEventListener('click', (event) => {
                event.preventDefault();
                this.applySearch();
            });
        }

        if (this.resetButton) {
            this.resetButton.addEventListener('click', (event) => {
                event.preventDefault();
                this.resetSearch();
                this.summary.clearAlert();
                this.refreshSummary();
            });
        }

        if (this.searchInput) {
            this.searchInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    this.applySearch();
                }
            });
        }

        this.form.addEventListener('submit', (event) => this.handleSubmit(event));
        this.form.addEventListener('reset', () => this.handleReset());
    }

    // buka modal permintaan sambil reset alert
    openModal() {
        this.summary.clearAlert();
        openModalSafely(this.modalId);
    }

    // validasi data dan siapin input hidden pas submit
    handleSubmit(event) {
        this.summary.clearAlert();
        this.hiddenContainer.innerHTML = '';

        const selectedRows = this.getSelectedRows();
        if (!selectedRows.length) {
            event.preventDefault();
            this.summary.showAlert('Pilih minimal satu bahan sebelum mengirim permintaan.');
            return;
        }

        let hasInvalidQty = false;
        selectedRows.forEach((row) => {
            const qtyInput = row.querySelector(this.qtyInputSelector);
            const qtyValue = qtyInput ? parseInt(qtyInput.value, 10) : 0;
            if (!qtyValue || qtyValue <= 0) {
                hasInvalidQty = true;
            }
        });

        if (hasInvalidQty) {
            event.preventDefault();
            this.summary.showAlert('Isi jumlah diminta untuk setiap bahan yang dipilih.');
            return;
        }

        selectedRows.forEach((row, index) => {
            const bahanId = row.dataset.bahanId;
            const qtyInput = row.querySelector(this.qtyInputSelector);
            const qtyValue = qtyInput ? qtyInput.value : '';

            this.hiddenContainer.appendChild(this.createHiddenField(`details[${index}][bahan_id]`, bahanId));
            this.hiddenContainer.appendChild(this.createHiddenField(`details[${index}][jumlah_diminta]`, qtyValue));
        });
    }

    // reset pilihan balik ke kondisi awal
    handleReset() {
        setTimeout(() => {
            this.hiddenContainer.innerHTML = '';
            this.getAllRows().forEach((row) => this.setRowState(row, false));
            this.resetSearch();
            this.summary.clearAlert();
            this.refreshState();
        });
    }

    // filter baris bahan berdasar teks cari
    applySearch() {
        const term = this.searchInput && this.searchInput.value ? this.searchInput.value.trim().toLowerCase() : '';

        this.getAllRows().forEach((row) => {
            const name = (row.dataset.bahanName || '').toLowerCase();
            const isMatch = term === '' || name.includes(term);
            row.classList.toggle('hidden', !isMatch);
        });

        this.refreshSelectAllState();
    }

    // balikin pencarian biar semua baris nongol lagi
    resetSearch() {
        if (this.searchInput) {
            this.searchInput.value = '';
        }

        this.getAllRows().forEach((row) => row.classList.remove('hidden'));
        this.refreshSelectAllState();
    }

    // tandain baris awal sesuai data lama
    applyInitialDetails(initialDetails) {
        if (!Array.isArray(initialDetails) || !initialDetails.length) {
            return;
        }

        const rows = this.getAllRows();
        initialDetails.forEach((detail) => {
            const matchedRow = rows.find((row) => parseInt(row.dataset.bahanId, 10) === Number(detail.bahan_id));
            if (!matchedRow) {
                return;
            }

            const checkbox = matchedRow.querySelector(this.checkboxSelector);
            const qtyInput = matchedRow.querySelector(this.qtyInputSelector);

            if (checkbox) {
                checkbox.checked = true;
            }

            if (qtyInput) {
                qtyInput.value = detail.jumlah_diminta;
            }

            this.setRowState(matchedRow, true);
        });
    }

    // update checkbox select all plus ringkasan
    refreshState() {
        this.refreshSelectAllState();
        this.refreshSummary();
    }

    // sesuaikan status checkbox select all
    refreshSelectAllState() {
        if (!this.selectAllCheckbox) {
            return;
        }

        const visibleRows = this.getVisibleRows();
        if (!visibleRows.length) {
            this.selectAllCheckbox.indeterminate = false;
            this.selectAllCheckbox.checked = false;
            return;
        }

        const selectedVisible = visibleRows.filter((row) => {
            const checkbox = row.querySelector(this.checkboxSelector);
            return checkbox && checkbox.checked;
        });

        if (selectedVisible.length === visibleRows.length) {
            this.selectAllCheckbox.checked = true;
            this.selectAllCheckbox.indeterminate = false;
        } else if (selectedVisible.length === 0) {
            this.selectAllCheckbox.checked = false;
            this.selectAllCheckbox.indeterminate = false;
        } else {
            this.selectAllCheckbox.checked = false;
            this.selectAllCheckbox.indeterminate = true;
        }
    }

    // render ulang ringkasan pilihan
    refreshSummary() {
        this.summary.render(this.getSelectedRows());
    }

    // nyalain atau matiin input jumlah sesuai pilihan
    setRowState(row, isChecked, { focus = false } = {}) {
        const qtyInput = row.querySelector(this.qtyInputSelector);
        if (!qtyInput) {
            return;
        }

        qtyInput.disabled = !isChecked;
        if (!isChecked) {
            qtyInput.value = '';
        }

        if (isChecked && focus) {
            qtyInput.focus({ preventScroll: true });
        }
    }

    // ambil semua baris bahan
    getAllRows() {
        return Array.from(this.tableBody.querySelectorAll(this.rowSelector));
    }

    // ambil baris yg lagi keliatan
    getVisibleRows() {
        return this.getAllRows().filter((row) => !row.classList.contains('hidden'));
    }

    // ambil baris yg dicentang user
    getSelectedRows() {
        return this.getAllRows().filter((row) => {
            const checkbox = row.querySelector(this.checkboxSelector);
            return checkbox && checkbox.checked;
        });
    }

    // bikin input hidden buat dikirim ke server
    createHiddenField(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    }
}
