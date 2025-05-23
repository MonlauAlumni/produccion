import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { Inertia } from '@inertiajs/inertia';
import NProgress from 'nprogress';

import 'boxicons';
import 'axios';
import 'boxicons/css/boxicons.min.css'
import '../css/app.css';


window.Inertia = Inertia;


createInertiaApp({
  resolve: name =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue')
    ),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el);
  },
});

// const style = document.createElement('style')
// style.textContent = `
//   #nprogress { 
//     display: none; 
//     pointer-events: none;
//   }
//   #nprogress .bar, 
//   #nprogress .spinner,
//   #nprogress .peg {
//     display: none;
//   }
// `
// document.head.appendChild(style)
 