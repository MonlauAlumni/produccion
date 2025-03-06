<template>
  <nav class="bg-gradient-to-r from-[#193CB8] to-[#2748c6] w-full py-3 px-4">
    <div class=" mx-10 flex justify-between items-center">
      <!-- Logo y nombre -->
      <div class="flex items-center">
        <a href="/" class="text-white font-bold flex items-center gap-3"> 
          <img src="../../../public/images/monlau-logo.png" alt="Alumni" class="w-8 h-8">
          <span class="text-xl">Alumni</span>
        </a>
      </div>
      
     
      
      <!-- Botones de autenticación -->
      <ul class="flex items-center justify-end gap-4">
        <li v-if="!auth?.user">
          <a href="/login" class="text-white hover:text-white/90 transition-colors font-medium flex items-center">
            <i class='bx bx-log-in mr-1'></i> Iniciar Sesión
          </a>
        </li>
        <li v-if="!auth?.user">
          <a href="/register" class="bg-white text-[#193CB8] px-4 py-2 rounded-md shadow-sm hover:bg-blue-50 transition-colors font-medium flex items-center">
            <i class='bx bx-user-plus mr-1'></i> Registrarse
          </a>
        </li>
        
        <!-- Si el usuario está autenticado, mostrar su avatar o un botón de perfil -->
        <li v-if="auth?.user" class="relative">
          <button 
            @click="toggleUserMenu" 
            class="flex items-center gap-2 text-white hover:text-white/90 transition-colors"
          >
            <div class="w-8 h-8 rounded-full bg-white/20 overflow-hidden flex items-center justify-center">
              <img 
                v-if="auth.user.profile_picture" 
                :src="auth.user.profile_picture" 
                alt="Avatar" 
                class="w-full h-full object-cover"
              >
              <i v-else class='bx bx-user text-white text-xl'></i>
            </div>
            <span class="hidden sm:inline">{{ auth.user.name }}</span>
            <i class='bx bx-chevron-down'></i>
          </button>
          
          <!-- Menú desplegable del usuario -->
          <div 
            v-if="userMenuOpen" 
            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
          >
            <a 
              :href="`/perfil/${auth.user.slang || ''}`" 
              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            >
              <i class='bx bx-user mr-2'></i> Mi Perfil
            </a>
            <a 
              href="/configuracion" 
              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            >
              <i class='bx bx-cog mr-2'></i> Configuración
            </a>
            <hr class="my-1 border-gray-200">
            <a 
              href="/logout" 
              class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
            >
              <i class='bx bx-log-out mr-2'></i> Cerrar Sesión
            </a>
          </div>
        </li>
      </ul>
    </div>    
  </nav>
</template>

<script setup>
import { ref, defineProps, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  auth: {
    type: Object,
    default: () => ({ user: null })
  }
});

const userMenuOpen = ref(false);

const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value;
};

// Función para cerrar el menú al hacer clic fuera
const handleClickOutside = (e) => {
  if (userMenuOpen.value && !e.target.closest('li')) {
    userMenuOpen.value = false;
  }
};

// Agregar y eliminar event listeners
onMounted(() => {
  window.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  window.removeEventListener('click', handleClickOutside);
});
</script>