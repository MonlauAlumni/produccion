<template>
    <div class="flex justify-center mt-8">
      <nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        <!-- Botón anterior -->
        <button 
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage === 1"
          :class="[
            'relative inline-flex items-center px-3 py-2 rounded-l-md border',
            currentPage === 1 
              ? 'border-gray-300 bg-white text-gray-300 cursor-not-allowed' 
              : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50'
          ]"
        >
          <i class='bx bx-chevron-left'></i>
          <span class="sr-only">Anterior</span>
        </button>
        
        <!-- Páginas -->
        <button 
          v-for="page in visiblePages" 
          :key="page"
          @click="goToPage(page)"
          :class="[
            'relative inline-flex items-center px-4 py-2 border',
            currentPage === page 
              ? 'z-10 border-[#193CB8] bg-[#193CB8] text-white' 
              : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50'
          ]"
        >
          {{ page }}
        </button>
        
        <!-- Botón siguiente -->
        <button 
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          :class="[
            'relative inline-flex items-center px-3 py-2 rounded-r-md border',
            currentPage === totalPages 
              ? 'border-gray-300 bg-white text-gray-300 cursor-not-allowed' 
              : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50'
          ]"
        >
          <i class='bx bx-chevron-right'></i>
          <span class="sr-only">Siguiente</span>
        </button>
      </nav>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits, computed } from 'vue';
  
  const props = defineProps({
    totalPages: {
      type: Number,
      required: true
    },
    currentPage: {
      type: Number,
      required: true
    },
    maxVisiblePages: {
      type: Number,
      default: 5
    }
  });
  
  const emit = defineEmits(['page-changed']);
  
  // Calcular páginas visibles
  const visiblePages = computed(() => {
    if (props.totalPages <= props.maxVisiblePages) {
      return Array.from({ length: props.totalPages }, (_, i) => i + 1);
    }
    
    const halfVisible = Math.floor(props.maxVisiblePages / 2);
    let start = props.currentPage - halfVisible;
    let end = props.currentPage + halfVisible;
    
    if (start < 1) {
      start = 1;
      end = Math.min(props.maxVisiblePages, props.totalPages);
    } else if (end > props.totalPages) {
      end = props.totalPages;
      start = Math.max(1, props.totalPages - props.maxVisiblePages + 1);
    }
    
    return Array.from({ length: end - start + 1 }, (_, i) => start + i);
  });
  
  function goToPage(page) {
    if (page < 1 || page > props.totalPages || page === props.currentPage) {
      return;
    }
    emit('page-changed', page);
  }
  </script>