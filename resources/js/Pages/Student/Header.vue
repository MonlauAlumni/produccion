<template>
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
      <div class="container mx-auto px-4 py-12">
        <h1 class="text-3xl md:text-4xl font-bold">{{ title }}</h1>
        <p class="mt-2 text-lg text-blue-100">{{ description }}</p>
        
        <div class="mt-6 flex items-center">
          <div class="flex items-center bg-white bg-opacity-20 rounded-full px-4 py-2">
            <i class='bx bx-info-circle text-xl'></i>
            <span class="ml-2 text-sm">{{ alumniCount }} perfiles disponibles</span>
          </div>
          
          <div class="ml-4 flex items-center bg-white bg-opacity-20 rounded-full px-4 py-2">
            <i class='bx bx-briefcase text-xl'></i>
            <span class="ml-2 text-sm">{{ availableCount }} disponibles para contratar</span>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, computed } from 'vue'
  
  const props = defineProps({
    title: {
      type: String,
      default: 'Directorio de Alumnos'
    },
    description: {
      type: String,
      default: 'Encuentra talento para tu empresa'
    },
    alumniCount: {
      type: Number,
      default: 250
    },
    availableCount: {
      type: Number,
      default: 120
    }
  })
  </script>