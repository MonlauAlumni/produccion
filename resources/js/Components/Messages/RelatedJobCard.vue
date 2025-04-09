<template>
  <div class="absolute top-16 right-4 z-10 w-80 bg-white rounded-lg shadow-lg border border-gray-200 p-4">
    <div class="flex justify-between items-start mb-3">
      <h4 class="font-semibold text-gray-800">Oferta relacionada</h4>
      <button 
        @click="$emit('close')"
        class="text-gray-400 hover:text-gray-600"
      >
        <i class='bx bx-x text-xl'></i>
      </button>
    </div>
    
    <div class="mb-3">
      <h5 class="font-medium text-gray-800 mb-1">{{ job.title }}</h5>
      <p class="text-sm text-gray-600 mb-2">{{ job.company.name }}</p>
      <div class="flex items-center text-sm text-gray-500 mb-2">
        <i class='bx bx-map-pin mr-1'></i>
        <span>{{ job.location }}</span>
      </div>
      <div class="flex items-center text-sm text-gray-500">
        <i class='bx bx-time mr-1'></i>
        <span>Publicado el {{ formatDate(job.created_at) }}</span>
      </div>
    </div>
    
    <div class="flex flex-wrap gap-1 mb-3">
      <span 
        v-for="tag in job.tags" 
        :key="tag"
        class="px-2 py-0.5 bg-gray-100 rounded-full text-xs text-gray-600"
      >
        {{ tag }}
      </span>
    </div>
    
    <a 
      :href="`/jobs/${job.id}`"
      target="_blank"
      class="block w-full py-2 text-center bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors text-sm font-medium"
    >
      Ver oferta completa
    </a>
  </div>
</template>

<script setup>
defineProps({
  job: {
    type: Object,
    required: true
  }
});

defineEmits(['close']);

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES', { 
    day: 'numeric', 
    month: 'long', 
    year: 'numeric' 
  });
};
</script>