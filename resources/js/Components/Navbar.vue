<template>
  <nav class="bg-[#193CB8] w-full h-14 mb-1  ">
    <div class=" flex justify-between items-center">
      <div class="flex items-center ">
        <a href="/" class=" text-white font-bold flex items-center gap-3"> 
          <img src="../../../public/images/logoMonlau.jpg" alt="Alumni" class="h-14 " />
          
          <span class="text-xl">Alumni</span>
        </a>
      </div>      
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