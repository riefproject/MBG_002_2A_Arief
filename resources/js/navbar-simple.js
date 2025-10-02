class EnhancedNavbarManager {
    constructor() {
        this.init();
    }

    init() {
        this.updateActiveState();
        console.log('Enhanced Navbar Manager initialized');
    }

    updateActiveState() {
        const currentPath = window.location.pathname;
        const currentUrl = window.location.href;
        
        console.log('Updating navbar active state for:', currentPath);

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
        let activeFound = false;

        links.forEach(link => {
            const href = link.getAttribute('href');
            if (!href) return;

            let linkPath;
            try {
                if (href.startsWith('http')) {
                    linkPath = new URL(href).pathname;
                } else {
                    linkPath = href;
                }
            } catch (e) {
                linkPath = href;
            }

            const cleanCurrentPath = this.cleanPath(currentPath);
            const cleanLinkPath = this.cleanPath(linkPath);

            let isActive = false;

            if (cleanCurrentPath === cleanLinkPath) {
                isActive = true;
            }
            else if (cleanCurrentPath.startsWith('/admin') && cleanLinkPath.includes('admin')) {
                if (cleanCurrentPath.includes('/bahan_baku') && cleanLinkPath.includes('/bahan_baku')) {
                    isActive = true;
                } else if (cleanCurrentPath === '/admin/dashboard' && cleanLinkPath === '/admin/dashboard') {
                    isActive = true;
                } else {
                    // Untuk route admin lainnya, periksa apakah mereka terkait
                    isActive = cleanCurrentPath.startsWith('/admin') && cleanLinkPath.startsWith('/admin');
                }
            }
            else if ((cleanCurrentPath === '/dashboard' || cleanCurrentPath === '/') && cleanLinkPath === '/dashboard') {
                isActive = true;
            }
            else if (cleanCurrentPath === '/profile' && cleanLinkPath === '/profile') {
                isActive = true;
            }

            if (isActive) {
                this.setActiveState(link);
                activeFound = true;
                console.log('Set active:', cleanLinkPath, 'for current:', cleanCurrentPath);
            }
        });

        if (!activeFound) {
            console.log('No active link found for:', currentPath);
        }
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

document.addEventListener('DOMContentLoaded', function() {
    window.enhancedNavbar = new EnhancedNavbarManager();
});

window.debugNavbar = function() {
    console.log('Enhanced Navbar Debug:');
    console.log('Current URL:', window.location.href);
    console.log('Current pathname:', window.location.pathname);
    
    const desktopLinks = document.querySelectorAll('.nav-link');
    const mobileLinks = document.querySelectorAll('.mobile-nav-link');
    
    console.log('Desktop links found:', desktopLinks.length);
    desktopLinks.forEach((link, index) => {
        const isActive = link.classList.contains('active') || link.classList.contains('text-blue-600');
        const href = link.getAttribute('href');
        console.log(`  ${index}: ${href} - "${link.textContent.trim()}" - ${isActive ? 'ACTIVE' : 'inactive'}`);
    });

    console.log('Mobile links found:', mobileLinks.length);
    mobileLinks.forEach((link, index) => {
        const isActive = link.classList.contains('active') || link.classList.contains('text-blue-600');
        const href = link.getAttribute('href');
        console.log(`  ${index}: ${href} - "${link.textContent.trim()}" - ${isActive ? 'ACTIVE' : 'inactive'}`);
    });
    
    if (window.enhancedNavbar) {
        window.enhancedNavbar.refresh();
    }
};

window.refreshNavbar = function() {
    if (window.enhancedNavbar) {
        window.enhancedNavbar.refresh();
        console.log('Navbar manually refreshed');
    }
};