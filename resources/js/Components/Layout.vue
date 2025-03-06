<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Navbar (Header siempre arriba, fijo en la parte superior) -->
    <Navbar :auth="auth" class="w-full" />

    <!-- Main container for Sidebar and Content -->
    <div class="flex flex-1"> 
      <!-- Sidebar (Izquierda, debajo del Navbar) -->
      <Sidebar 
        :username="auth?.user?.name || 'Usuario Alumni'" 
        class="hidden md:block md:w-64 md:h-[calc(100vh-64px)]" 
      />

      <!-- Main Content -->
      <main class="flex-1  bg-gray-50">
        <slot></slot>
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
    auth: {
      type: Object,
      default: () => ({ user: null })
    }
  }
};
</script>