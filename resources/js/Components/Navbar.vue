<template>
    <nav class="bg-gradient-to-r from-[#193CB8] to-[#2748c6] py-3 px-4 ">
      <div class="container mx-2 flex justify-between items-center">
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
  