<template>
  <div class="min-h-screen absolute bg-gray-50 w-full flex flex-col">
    <!-- Navbar (Header siempre arriba, fijo en la parte superior) -->
    <Navbar  :auth="auth" class="w-full z-50 fixed" />

    <div class="flex flex-1"> 

      <Sidebar 
        :username="auth?.user?.name || 'Usuario Alumni'" 
        class="fixed md:w-64 z-40 mt-14 md:h-[calc(100vh-64px)]" 
      />

      <!-- Main Content -->
      <main class="flex-1 md:ml-64  mt-14 bg-gray-50">
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