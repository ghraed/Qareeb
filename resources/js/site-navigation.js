export default () => ({
    menuOpen: false,
    isDesktop: window.matchMedia('(min-width: 64rem)').matches,

    init() {
        this.$watch('menuOpen', (open) => {
            document.body.style.overflow = open && !this.isDesktop ? 'hidden' : '';
        });
    },

    openMenu() {
        this.menuOpen = true;
        this.$nextTick(() => requestAnimationFrame(() => {
            if (this.menuOpen) this.$refs.menuClose.focus({ preventScroll: true });
        }));
    },

    closeMenu(restoreFocus = true) {
        this.menuOpen = false;
        if (restoreFocus && !this.isDesktop) this.$refs.menuToggle.focus();
    },

    syncViewport() {
        this.isDesktop = window.matchMedia('(min-width: 64rem)').matches;
        if (this.isDesktop) this.closeMenu(false);
    },

    trapFocus(event) {
        if (this.isDesktop || !this.menuOpen) return;

        const controls = this.$refs.menuPanel.querySelectorAll('button, a[href]');
        const first = controls[0];
        const last = controls[controls.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    },

    destroy() {
        document.body.style.overflow = '';
    },
});
