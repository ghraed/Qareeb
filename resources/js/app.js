import './bootstrap';
import Alpine from 'alpinejs';
import providerFilter from './provider-filter';
import siteNavigation from './site-navigation';
import initInfiniteScroll from './infinite-scroll';
import availabilityToggle from './availability-toggle';

window.Alpine = Alpine;
Alpine.data('providerFilter', providerFilter);
Alpine.data('siteNavigation', siteNavigation);
Alpine.data('availabilityToggle', availabilityToggle);

Alpine.start();
initInfiniteScroll();
