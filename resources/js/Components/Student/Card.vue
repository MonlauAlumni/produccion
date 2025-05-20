<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
    <!-- Foto de perfil y banner -->
    <div class="h-40 bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden relative">
      <img 
        v-if="alumni.profile?.banner_url"
        :src="alumni.profile.banner_url"
        class="w-full h-full object-cover absolute inset-0"
        alt="Banner"
      />
      <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/50"></div>
      
      <img 
        :src="alumni.profile?.profile_picture" 
        :alt="alumni.name"
        class="w-20 h-20 rounded-full border-2 border-white object-cover z-10 shadow-md"
      />
      
      <!-- Indicador de disponibilidad -->
      <div 
        v-if="alumni.available" 
        class="absolute top-3 right-3 bg-green-500 text-white text-xs px-2 py-1 rounded-full flex items-center z-10"
      >
        <i class='bx bx-check mr-1'></i>
        Disponible
      </div>
    </div>
    
    <!-- Información del alumno -->
    <div class="p-4">
      <div class="text-center mb-3">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ alumni.name }}</h3>
        <p v-if="alumni.profile?.job_title" class="text-sm text-gray-600 dark:text-gray-400">{{ alumni.profile.job_title }}</p>
      </div>
      
      <!-- Detalles adicionales -->
      <div class="space-y-2 mb-4">
        <div v-if="alumni.profile?.location" class="flex items-center text-gray-600 dark:text-gray-400">
          <i class='bx bx-map text-lg text-gray-500 dark:text-gray-400'></i>
          <span class="ml-2 text-sm">{{ alumni.profile.location }}</span>
        </div>
        
        <div v-if="alumni.profile?.graduation_year" class="flex items-center text-gray-600 dark:text-gray-400">
          <i class='bx bx-graduation text-lg text-gray-500 dark:text-gray-400'></i>
          <span class="ml-2 text-sm">{{ alumni.profile.degree }} ({{ alumni.profile.graduation_year }})</span>
        </div>
      </div>
      
      <!-- Enlaces sociales -->
      <div class="flex items-center justify-center space-x-4 mb-4">
        <a 
          v-if="alumni.profile?.linkedin" 
          :href="alumni.profile.linkedin" 
          target="_blank" 
          class="text-gray-500 dark:text-gray-400 hover:text-[#0077B5] transition"
          aria-label="LinkedIn"
        >
          <i class='bx bxl-linkedin text-xl'></i>
        </a>
        <a 
          v-if="alumni.profile?.github" 
          :href="alumni.profile.github" 
          target="_blank" 
          class="text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 transition"
          aria-label="GitHub"
        >
          <i class='bx bxl-github text-xl'></i>
        </a>
        <a 
          v-if="alumni.profile?.website" 
          :href="alumni.profile.website" 
          target="_blank" 
          class="text-gray-500 dark:text-gray-400 hover:text-[#193CB8] dark:hover:text-blue-400 transition"
          aria-label="Sitio web"
        >
          <i class='bx bx-globe text-xl'></i>
        </a>
        <a 
          v-if="alumni.profile?.cv_path" 
          :href="alumni.profile.cv_path" 
          target="_blank" 
          class="text-gray-500 dark:text-gray-400 hover:text-[#FF5722] transition"
          aria-label="Currículum"
        >
          <i class='bx bx-file text-xl'></i>
        </a>
      </div>
      
      <!-- Botones de acción -->
      <div class="flex space-x-2">
        <button 
          @click="viewProfile"
          class="flex-1 py-2 border border-[#193CB8] dark:border-blue-400 text-[#193CB8] dark:text-blue-400 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center justify-center"
        >
          <i class='bx bx-user mr-1'></i>
          Ver perfil
        </button>
        <button 
          @click="contact"
          class="flex-1 py-2 bg-[#193CB8] dark:bg-blue-700 text-white rounded-lg hover:bg-[#142d8c] dark:hover:bg-blue-800 transition-colors flex items-center justify-center"
        >
          <i class='bx bx-envelope mr-1'></i>
          Contactar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  alumni: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['contact', 'view-profile']);

function contact() {
  // Navegar directamente a la página de mensajes con parámetros para abrir el modal
  // y preseleccionar el usuario
  router.get('/mensajes', {
    openModal: true,
    userId: props.alumni.id,
    jobId: null, // No hay oferta asociada en este caso
    userName: props.alumni.name // Usar el nombre completo del alumno
  });
}

function viewProfile() {
  emit('view-profile', props.alumni);
}
</script>