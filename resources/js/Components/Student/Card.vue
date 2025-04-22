<template>
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
      <!-- Foto de perfil -->
      <div class="h-40 bg-gray-200 flex items-center justify-center overflow-hidden relative">
        <img 
          :src="alumni.profile?.profile_picture" 
          :alt="alumni.name"
          class="w-full h-full object-cover"
        />
       
        
        <!-- Indicador de disponibilidad -->
        <div 
          v-if="alumni.available" 
          class="absolute top-3 right-3 bg-green-500 text-white text-xs px-2 py-1 rounded-full flex items-center"
        >
          <i class='bx bx-check mr-1'></i>
          Disponible
        </div>
      </div>
      
      <!-- Información del alumno -->
      <div class="p-4">
        <div class="flex items-center justify-between mb-2">
          <h3 class="text-lg font-semibold text-gray-800">{{ alumni.name }}</h3>
          <span 
            :class="[
              'text-xs px-2 py-1 rounded-full',
              alumni.status === 'alumni' 
                ? 'bg-purple-100 text-purple-800' 
                : 'bg-blue-100 text-blue-800'
            ]"
          >
            {{ alumni.status === 'alumni' ? 'Exalumno' : 'Alumno' }}
          </span>
        </div>
        
        <div class="flex items-center text-gray-600 mb-3">
          <i class='bx bx-map text-lg'></i>
          <span class="ml-1 text-sm">{{ alumni.profile?.location }}</span>
        </div>
        
        <!-- Habilidades -->
        <!-- <div class="mb-4">
          <div class="flex flex-wrap gap-1">
            <span 
              v-for="skill in displayedSkills" 
              :key="skill"
              class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
            >
              {{ skill }}
            </span>
            <span 
              v-if="alumni.skills.length > maxSkillsToShow"
              class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
            >
              +{{ alumni.skills.length - maxSkillsToShow }}
            </span>
          </div>
        </div> -->
        
        <!-- Enlaces sociales -->
        <div class="flex items-center space-x-3 mb-4">
          <a 
            v-if="alumni.linkedin" 
            :href="alumni.linkedin" 
            target="_blank" 
            class="text-gray-500 hover:text-[#193CB8] transition"
          >
            <i class='bx bxl-linkedin text-xl'></i>
          </a>
          <a 
            v-if="alumni.github" 
            :href="alumni.github" 
            target="_blank" 
            class="text-gray-500 hover:text-gray-800 transition"
          >
            <i class='bx bxl-github text-xl'></i>
          </a>
          <a 
            v-if="alumni.portfolio" 
            :href="alumni.portfolio" 
            target="_blank" 
            class="text-gray-500 hover:text-[#193CB8] transition"
          >
            <i class='bx bx-globe text-xl'></i>
          </a>
        </div>
        
        <!-- Botones de acción -->
        <div class="flex space-x-2">
          <button 
            @click="viewProfile"
            class="flex-1 py-2 border border-[#193CB8] text-[#193CB8] rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center"
          >
            <i class='bx bx-user mr-1'></i>
            Ver perfil
          </button>
          <button 
            @click="contact"
            class="flex-1 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors flex items-center justify-center"
          >
            <i class='bx bx-envelope mr-1'></i>
            Contactar
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits, computed } from 'vue';
  
  const props = defineProps({
    alumni: {
      type: Object,
      required: true
    },
    // maxSkillsToShow: {
    //   type: Number,
    //   default: 3
    // }
  });

  const emit = defineEmits(['contact', 'view-profile']);
  
  // const displayedSkills = computed(() => {
  //   return props.alumni.skills.slice(0, props.maxSkillsToShow);
  // });
  
  function contact() {
    emit('contact', props.alumni);
  }
  
  function viewProfile() {
    emit('view-profile', props.alumni);
  }
  </script>