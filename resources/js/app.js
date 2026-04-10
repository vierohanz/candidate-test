import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// ─── Global UI store ────────────────────────────────────────────────────────
Alpine.store('ui', {
    // Desktop sidebar: collapsed = icon-only rail
    sidebarCollapsed: false,

    // Mobile sidebar: full overlay
    mobileSidebarOpen: false,

    // Dark mode
    dark: localStorage.getItem('clt_dark') === '1',

    init() {
        // Apply dark class immediately (store init runs before first paint)
        document.documentElement.classList.toggle('dark', this.dark);
        // Reset old persisted collapsed state so layout starts in full mode.
        localStorage.setItem('clt_sc', '0');
    },

    toggleSidebar() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
        localStorage.setItem('clt_sc', this.sidebarCollapsed ? '1' : '0');
    },

    toggleMobile() {
        this.mobileSidebarOpen = !this.mobileSidebarOpen;
    },

    closeMobile() {
        this.mobileSidebarOpen = false;
    },

    toggleDark() {
        this.dark = !this.dark;
        localStorage.setItem('clt_dark', this.dark ? '1' : '0');
        document.documentElement.classList.toggle('dark', this.dark);
    },
});

Alpine.start();
