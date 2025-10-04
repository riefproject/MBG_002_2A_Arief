class EnhancedNavbarManager {
    constructor() {
        this.init();
    }

    init() {
        this.updateActiveState();
    }

    updateActiveState() {
        const currentPath = window.location.pathname;

        const desktopLinks = document.querySelectorAll('.nav-link');
        const mobileLinks = document.querySelectorAll('.mobile-nav-link');
        this.clearAllActiveStates([...desktopLinks, ...mobileLinks]);

        this.setActiveStateByRoute(currentPath, [...desktopLinks, ...mobileLinks]);
    }

    clearAllActiveStates(links) {
        links.forEach(link => {
            link.classList.remove('text-blue-600', 'border-blue-500', 'bg-blue-50', 'border-r-4', 'active');
            link.classList.add('text-gray-600');
        });
    }

    setActiveStateByRoute(currentPath, links) {
        links.forEach(link => {
            const href = link.getAttribute('href');
            if (!href) return;

            let linkPath;
            try {
                linkPath = href.startsWith('http') ? new URL(href).pathname : href;
            } catch (e) {
                linkPath = href;
            }

            const cleanCurrentPath = this.cleanPath(currentPath);
            const cleanLinkPath = this.cleanPath(linkPath);

            const isActive =
                cleanCurrentPath === cleanLinkPath ||
                (cleanLinkPath !== '/' && cleanCurrentPath.startsWith(cleanLinkPath + '/'));

            if (isActive) {
                this.setActiveState(link);
            }
        });
    }

    cleanPath(path) {
        return path.replace(/\/$/, '') || '/';
    }

    setActiveState(link) {
        if (link.classList.contains('nav-link')) {
            link.classList.remove('text-gray-600');
            link.classList.add('text-blue-600', 'border-blue-500', 'bg-blue-50', 'active');
        }
        else if (link.classList.contains('mobile-nav-link')) {
            link.classList.remove('text-gray-600');
            link.classList.add('text-blue-600', 'bg-blue-50', 'border-r-4', 'border-blue-600', 'active');
        }
    }

    refresh() {
        this.updateActiveState();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.enhancedNavbar = new EnhancedNavbarManager();
});
