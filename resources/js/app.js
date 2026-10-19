import './bootstrap';
import Alpine from 'alpinejs';
import providerFilter from './provider-filter';
import siteNavigation from './site-navigation';
import initInfiniteScroll from './infinite-scroll';
import availabilityToggle from './availability-toggle';
import toast, { notify } from './toast';

window.Alpine = Alpine;
Alpine.data('providerFilter', providerFilter);
Alpine.data('siteNavigation', siteNavigation);
Alpine.data('availabilityToggle', availabilityToggle);
Alpine.data('toast', toast);

Alpine.start();
initInfiniteScroll();

document.addEventListener('invalid', (event) => {
    const field = event.target;
    if (field.form?.querySelector(':invalid') !== field) return;

    const label = field.labels?.[0]?.textContent.trim();
    notify(label ? `${label}: ${field.validationMessage}` : field.validationMessage, 'error');
}, true);
