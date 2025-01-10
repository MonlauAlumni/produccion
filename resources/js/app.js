import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import '../css/app.css';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
  id: 'app',
  resolve: name => {

    return import(`./Pages/${name}.vue`);
  },
  setup({ el, App, props }) {
    return createApp({ render: () => h(App, props) }).mount(el);
  },
});
