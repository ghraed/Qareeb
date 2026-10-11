import './bootstrap';
import Alpine from 'alpinejs';
import providerFilter from './provider-filter';

window.Alpine = Alpine;
Alpine.data('providerFilter', providerFilter);

Alpine.start();
