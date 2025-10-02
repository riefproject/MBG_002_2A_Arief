class InPageSPA {
    constructor() {
        this.init();
    }

    init() {
        console.log('InPageSPA: Initializing');
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    setup() {
        console.log('InPageSPA: Ready for CRUD operations');
    }

    updateContentSection(selector, newContent) {
        const element = document.querySelector(selector);
        if (element) {
            element.innerHTML = newContent;
            
            if (window.Alpine) {
                window.Alpine.initTree(element);
            }
            
            console.log('Content section updated:', selector);
        }
    }

    refreshTable(tableId) {
        const table = document.getElementById(tableId);
        if (table) {
            table.dispatchEvent(new CustomEvent('table:refresh'));
        }
    }
}

window.inPageSPA = new InPageSPA();

export default InPageSPA;