export function notify(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }));
}

export default (initial = {}) => ({
    visible: false,
    message: '',
    type: 'success',
    timer: null,

    init() {
        if (initial.message) this.show(initial);
    },

    show({ message, type = 'success' } = {}) {
        if (typeof message !== 'string' || !message.trim()) return;

        this.dismiss();
        this.message = message;
        this.type = type === 'error' ? 'error' : 'success';
        this.visible = true;

        if (this.type === 'success') {
            this.timer = setTimeout(() => this.dismiss(), 5000);
        }
    },

    dismiss() {
        clearTimeout(this.timer);
        this.timer = null;
        this.visible = false;
    },

    destroy() {
        this.dismiss();
    },
});
