<template>
  <div class="min-h-screen">
    <!-- Navbar fijo arriba -->
    <header class="h-16 w-full fixed top-0 left-0 bg-gradient-to-r from-[#193CB8] to-[#2748c6] z-50">
      <Navbar :auth="auth" />
    </header>

    <!-- Contenedor con Sidebar y Main -->
    <div class="flex">
      <!-- Sidebar, empieza justo después del navbar -->
      <aside 
        :class="[
          'w-64 fixed left-0 top-16 h-[calc(100vh-4rem)] bg-white border-r border-gray-100 z-40 transition-transform',
          sidebar ? 'translate-x-0' : '-translate-x-full',
          'md:translate-x-0' 
        ]">
        <Sidebar :username="auth?.user?.name || 'Usuario Alumni'" />
      </aside>

      <!-- Contenido principal -->
      <main class="flex-grow p-4 ml-0 md:ml-64 mt-16">
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
  