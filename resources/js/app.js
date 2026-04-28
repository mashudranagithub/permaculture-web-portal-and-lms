import 'bootstrap/dist/css/bootstrap.min.css';
import axios from 'axios';
import $ from 'jquery';
import * as bootstrap from 'bootstrap';

window.axios = axios;
window.$ = window.jQuery = $;
window.bootstrap = bootstrap;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Libraries that depend on jQuery/Global scope
import select2 from 'select2';
select2(); // Initialize select2 on the global jQuery instance

import 'select2/dist/css/select2.min.css';
import 'select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css';
import 'flatpickr/dist/flatpickr.css';
import flatpickr from 'flatpickr';
window.flatpickr = flatpickr;

import { createInertiaApp, router, usePage } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import DateInput from './Components/DateInput.vue';
import Select2 from './Components/Select2.vue';

// Global Tooltip Management for Inertia transitions
router.on('before', () => {
    const tooltips = document.querySelectorAll('.tooltip');
    tooltips.forEach(tooltip => tooltip.remove());
});

router.on('finish', () => {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        if (window.bootstrap && window.bootstrap.Tooltip) {
            return new window.bootstrap.Tooltip(tooltipTriggerEl);
        }
    });
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('BilingualDateInput', DateInput)
            .component('Select2', Select2);

        vueApp.config.globalProperties.__ = (key) => {
            const translations = usePage().props.translations || {};
            return translations[key] || key;
        };

        // Global Permission Helpers
        vueApp.config.globalProperties.can = function(permission) {
            return this.$page.props.auth.user?.permissions?.includes(permission) || false;
        };

        vueApp.config.globalProperties.hasRole = function(role) {
            return this.$page.props.auth.user?.roles?.includes(role) || false;
        };

        return vueApp.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
