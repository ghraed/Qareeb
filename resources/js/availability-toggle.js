import { notify } from './toast.js';

let requestPending = false;

export default (available) => ({
    available,
    infoOpen: false,
    saving: false,
    error: '',

    async save(form) {
        if (this.saving || requestPending) return;

        requestPending = true;
        this.saving = true;
        this.error = '';
        window.dispatchEvent(new CustomEvent('availability-saving', { detail: { saving: true } }));

        try {
            const { data } = await window.axios.patch(form.action, {
                is_available_for_hire: !this.available,
            }, {
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': form.elements.namedItem('_token').value,
                },
            });

            if (typeof data.is_available_for_hire !== 'boolean') {
                throw new Error('Unexpected availability response.');
            }

            this.available = data.is_available_for_hire;
            window.dispatchEvent(new CustomEvent('availability-updated', { detail: { available: this.available } }));
            notify(this.available ? 'You are now marked as available for hire.' : 'You are no longer marked as available for hire.');
        } catch (error) {
            this.error = error.response?.data?.errors?.is_available_for_hire?.[0]
                || error.response?.data?.message
                || 'Availability could not be saved. Please try again.';
            notify(this.error, 'error');
        } finally {
            requestPending = false;
            this.saving = false;
            window.dispatchEvent(new CustomEvent('availability-saving', { detail: { saving: false } }));
        }
    },
});
