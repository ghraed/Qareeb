import './bootstrap';
import Alpine from 'alpinejs';
import providerFilter from './provider-filter';
import siteNavigation from './site-navigation';
import initInfiniteScroll from './infinite-scroll';

window.Alpine = Alpine;
Alpine.data('providerFilter', providerFilter);
Alpine.data('siteNavigation', siteNavigation);

Alpine.start();
initInfiniteScroll();
