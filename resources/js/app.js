import './bootstrap';
import '../scss/config/default/app.scss';
import '@vueform/slider/themes/default.css';
import '../scss/mermaid.min.css';
import 'leaflet/dist/leaflet.css';


import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import BootstrapVueNext from 'bootstrap-vue-next';
import vClickOutside from "click-outside-vue3";
import VueApexCharts from "vue3-apexcharts";
import VueFeather from 'vue-feather';
import VueTheMask from 'vue-the-mask';
import { ref, onMounted } from "vue";
import AOS from 'aos';
import 'aos/dist/aos.css';

import store from "./state/store";
import i18n, { initI18n } from './i18n';

AOS.init({
    easing: 'ease-out-back',
    duration: 1000
});


async function bootstrap() {

    const selectedLanguageCode = ref(i18n.global.locale);
    const currentLocale = localStorage.getItem('locale') || window.defaultLocale;
    selectedLanguageCode.value = currentLocale;
    localStorage.setItem('locale', currentLocale);
    
    const body = document.body;

    // Fetch permissions before initializing the app
    // await store.dispatch('fetchPermissions');
    await initI18n(currentLocale);

    createInertiaApp({
        title: title => title ? `${title} | Admin-Panel` : 'Admin-Panel',
        resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
        setup({ el, App, props, plugin }) {
            return createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(store)
                .use(i18n)
                .use(ZiggyVue)
                .use(BootstrapVueNext)
                .use(VueApexCharts)
                .use(VueTheMask)
                .use(vClickOutside)
                .component(VueFeather.type, VueFeather)
                .mount(el);
        },
        progress: {
            color: '#4B5563',
        },
    });
}

bootstrap();
