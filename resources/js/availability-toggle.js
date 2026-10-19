export default (available) => ({
    available,
    infoOpen: false,
    saving: false,
    error: '',

    async save(form) {
        if (this.saving) return;

        this.saving = true;
        this.error = '';

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
        } catch (error) {
            this.error = error.response?.data?.errors?.is_available_for_hire?.[0]
                || error.response?.data?.message
                || 'Availability could not be saved. Please try again.';
        } finally {
            this.saving = false;
        }
    },
});
