export default function providerFilter() {
    return {
        enhanced: false,
        open: false,
        options: [],
        value: '',
        active: 0,
        init() {
            this.refreshOptions();
            this.enhanced = true;
        },
        refreshOptions() {
            this.options = Array.from(this.$refs.select.options)
                .filter(option => !option.disabled && !option.parentElement?.disabled)
                .map(option => ({ value: option.value, label: option.text }));
            this.value = this.$refs.select.value;
            this.active = Math.max(0, this.options.findIndex(option => option.value === this.value));
        },
        get label() {
            return this.options.find(option => option.value === this.value)?.label ?? '';
        },
        toggle() {
            this.refreshOptions();
            this.open = !this.open;
            if (this.open) {
                this.active = Math.max(0, this.options.findIndex(option => option.value === this.value));
                this.scrollToActive();
            }
        },
        scrollToActive() {
            this.$nextTick(() => this.$refs.list.querySelectorAll('[role="option"]')[this.active]?.scrollIntoView({ block: 'nearest' }));
        },
        choose(index) {
            this.value = this.options[index].value;
            this.$refs.select.value = this.value;
            this.$refs.select.dispatchEvent(new Event('change', { bubbles: true }));
            this.open = false;
            this.$refs.trigger.focus();
        },
        onKey(event) {
            if (event.key === 'Tab' || event.key === 'Escape') {
                this.open = false;
                return;
            }
            if (['Enter', ' '].includes(event.key)) {
                event.preventDefault();
                if (this.open) this.choose(this.active);
                else this.toggle();
                return;
            }
            if (['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) {
                event.preventDefault();
                if (!this.open) this.toggle();
                else if (event.key === 'ArrowDown') this.active = Math.min(this.options.length - 1, this.active + 1);
                else if (event.key === 'ArrowUp') this.active = Math.max(0, this.active - 1);
                if (event.key === 'Home') this.active = 0;
                if (event.key === 'End') this.active = this.options.length - 1;
                this.scrollToActive();
                return;
            }
            if (event.key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey) {
                if (!this.open) this.toggle();
                const start = this.active + 1;
                for (let offset = 0; offset < this.options.length; offset++) {
                    const index = (start + offset) % this.options.length;
                    if (this.options[index].label.toLowerCase().startsWith(event.key.toLowerCase())) {
                        event.preventDefault();
                        this.active = index;
                        this.scrollToActive();
                        break;
                    }
                }
            }
        },
    };
}
