  
  <script>
    import { router } from '@inertiajs/vue3';
    import Layout from '@/Components/Layout.vue';
    
    export default {
      components: {
        Layout
      },
      methods: {
        goHome() {
          router.visit('/home'); 
        }
      }
    };
    </script>
<template>

  <Layout v-if="$page.props.auth.user">
    
    <div class="flex flex-col items-center justify-center min-h-[90%]   text-center">
      <h1 class="text-6xl font-bold text-gray-500">Error 404</h1>
      <img src="/public/images/404-error.png" alt="404 Error" class="w-1/3 max-w-sm my-6" />
      <p class="text-lg text-gray-700">Oops! La página que buscas no existe.</p>
      <button @click="goHome" class="mt-4 cursor-pointer px-4 py-2 bg-blue-500 text-white rounded">Ir al inicio</button>
    </div>
  </Layout>
  </template>

  