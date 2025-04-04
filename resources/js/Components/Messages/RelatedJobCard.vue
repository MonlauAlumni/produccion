<template>
    <div class="flex justify-center">
      <div class="bg-[#193CB8]/5 rounded-lg p-3 max-w-md">
        <div class="flex items-center mb-2">
          <i class='bx bx-briefcase text-[#193CB8] mr-2'></i>
          <span class="font-medium text-[#193CB8]">Oferta relacionada</span>
        </div>
        <h4 class="font-medium mb-1">{{ job.title }}</h4>
        <p class="text-sm text-gray-600 mb-2">{{ job.company.name }}</p>
        <button 
          @click="$emit('view')"
          class="text-sm text-[#193CB8] hover:underline"
        >
          Ver detalles
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    job: {
      type: Object,
      required: true
    }
  })
  
  defineEmits(['view'])
  </script>