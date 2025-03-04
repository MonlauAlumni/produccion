<template>
    <nav class="bg-gradient-to-r from-[#193CB8] to-[#2748c6] py-3 px-4 ">
      <div class="container mx-auto flex justify-between items-center">
        <!-- Logo y nombre -->
        <div class="flex items-center">
          <a href="/" class="text-white font-bold flex items-center gap-3"> 
            
              <img src="../../../public/images/monlau-logo.png" alt="Alumni" class="w-8 h-8">
          
            <span class="text-xl">Alumni </span>
          </a>
        </div>
        
        <!-- Menú central - visible solo en pantallas medianas y grandes -->
        <div class="hidden md:flex items-center space-x-6">
          <a href="#" class="text-white/90 hover:text-white transition-colors flex items-center">
            <i class='bx bx-home-alt mr-1'></i> Inicio
          </a>
          <a href="#" class="text-white/90 hover:text-white transition-colors flex items-center">
            <i class='bx bx-book-alt mr-1'></i> Recursos
          </a>
          <a href="#" class="text-white/90 hover:text-white transition-colors flex items-center">
            <i class='bx bx-briefcase mr-1'></i> Empleos
          </a>
          <a href="#" class="text-white/90 hover:text-white transition-colors flex items-center">
            <i class='bx bx-calendar-event mr-1'></i> Eventos
          </a>
        </div>
        
        <!-- Botones de autenticación -->
        <ul class="flex items-center gap-4">
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
          
          <!-- Usuario autenticado -->
          <li v-if="auth?.user" class="relative">
            <button @click="toggleUserMenu" class="flex items-center gap-2 text-white">
              <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                <i class='bx bxs-user text-white'></i>
              </div>
              <span class="hidden sm:inline">{{ auth.user.name }}</span>
              <i class='bx bx-chevron-down'></i>
            </button>
            
            <!-- Menú desplegable -->
            <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
              <a href="/profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                <i class='bx bx-user mr-2'></i> Mi Perfil
              </a>
              <a href="/dashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                <i class='bx bx-grid-alt mr-2'></i> Dashboard
              </a>
              <a href="/settings" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                <i class='bx bx-cog mr-2'></i> Configuración
              </a>
              <div class="border-t border-gray-100 my-1"></div>
              <a href="/logout" class="block px-4 py-2 text-red-600 hover:bg-gray-100">
                <i class='bx bx-log-out mr-2'></i> Cerrar Sesión
              </a>
            </div>
          </li>
        </ul>
      </div>    
    </nav>
  </template>
  
  <script setup>
  import { ref, defineProps } from 'vue';
  
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
  
  // Cerrar el menú al hacer clic fuera
  if (typeof window !== 'undefined') {
    window.addEventListener('click', (e) => {
      if (userMenuOpen.value && !e.target.closest('li')) {
        userMenuOpen.value = false;
      }
    });
  }
  </script>
  