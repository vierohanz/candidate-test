import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('ui', {
    sidebarCollapsed: true,

    mobileSidebarOpen: false,

    dark: localStorage.getItem('clt_dark') === '1',

    init() {
        document.documentElement.classList.toggle('dark', this.dark);
        const savedCollapsed = localStorage.getItem('clt_sc');
        this.sidebarCollapsed = savedCollapsed === null ? true : savedCollapsed === '1';
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
