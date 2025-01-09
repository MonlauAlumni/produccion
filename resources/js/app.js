import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
  id: 'app',
  resolve: name => {
    // Importar explícitamente las páginas
    return import(`./Pages/${name}.vue`);
  },
  setup({ el, App, props }) {
    return createApp({ render: () => h(App, props) }).mount(el);
  },
});
