<template>
  <div class="min-h-screen">
    <!-- Navbar arriba (posición fija) -->
    <header class="h-16 w-full fixed top-0 left-0 bg-white border-b border-gray-100 z-10">
      <Navbar :auth="auth" />
    </header>

      <!-- Contenedor principal con margen superior para evitar que el navbar lo cubra -->
    <div class="flex flex-grow mt-16">
      <!-- Sidebar a la izquierda -->
      <aside :class="sidebar ? 'block' : 'hidden'" class="w-64 bg-white border-r border-gray-100 md:block">
        <Sidebar :username="auth?.user?.name || 'Usuario Alumni'" />
      </aside>

      <!-- Contenido principal -->
      <main class="flex-grow p-4 overflow-auto">
        <slot />
      </main>
    </div>
  </div>
</template>



  
  <script>
  import Navbar from '@/Components/Navbar.vue';
  import Sidebar from '@/Components/Sidebar.vue';
  
  export default {
    name: 'AlumniLayout',
    components: {
      Navbar,
      Sidebar
    },
    props: {
      pageTitle: String,
      header: {
        type: Boolean,
        default: true
      },
      sidebar: {
        type: Boolean,
        default: true
      },
    }
  };
  </script>
  