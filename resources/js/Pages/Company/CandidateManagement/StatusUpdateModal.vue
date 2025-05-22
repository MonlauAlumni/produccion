<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  application: {
    type: Object,
    default: null
  }
});

const emit = defineEmits([
  'close',
  'update'
]);

const selectedStatus = ref('');
const feedbackText = ref('');

// Status options
const statusOptions = [
  { id: 'pending', name: 'Pendiente de revisión', icon: 'bx-time', color: 'text-yellow-600' },
  { id: 'in_process', name: 'En proceso de selección', icon: 'bx-loader', color: 'text-blue-600' },
  { id: 'interview', name: 'Programar entrevista', icon: 'bx-calendar-check', color: 'text-purple-600' },
  { id: 'accepted', name: 'Aceptar candidato', icon: 'bx-check-circle', color: 'text-green-600' },
  { id: 'rejected', name: 'Rechazar candidato', icon: 'bx-x-circle', color: 'text-red-600' }
];

// Cerrar modal
const closeModal = () => {
  emit('close');
};

// Actualizar estado
const updateStatus = () => {
  if (!selectedStatus.value) return;
  
  emit('update', props.application.id, selectedStatus.value, feedbackText.value);
};

// Resetear formulario cuando cambia la aplicación
watch(() => props.application, () => {
  if (props.application) {
    selectedStatus.value = props.application.status || '';
    feedbackText.value = props.application.feedback || '';
  } else {
    selectedStatus.value = '';
    feedbackText.value = '';
  }
});

// Formatear nombre del candidato
const getCandidateName = () => {
  if (!props.application || !props.application.user) return 'Candidato';
  return `${props.application.user.name} ${props.application.user.last_name_1 || ''}`;
};
</script>

<template>
 <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
  <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
    <!-- Overlay -->
    <div class="fixed inset-0 z-[-10] transition-opacity bg-black/70 dark:bg-black/80 backdrop-blur-sm" @click="closeModal"></div>

    <!-- Modal Panel -->
    <div class="inline-block w-full max-w-md px-4 pt-5 pb-4 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle sm:p-6 border border-gray-200 dark:border-gray-700">
      <!-- Close Button -->
      <div class="absolute top-0 right-0 pt-4 pr-4">
        <button 
          @click="closeModal"
          class="text-gray-400 dark:text-gray-300 bg-white dark:bg-gray-800 cursor-pointer my-2 rounded-md hover:text-gray-500 dark:hover:text-gray-200 focus:outline-none"
        >
          <i class='bx bx-x text-2xl'></i>
        </button>
      </div>

      <!-- Modal Content -->
      <div v-if="application">
        <h3 class="text-lg font-medium text-blue-900 dark:text-blue-300 mb-1">
          Actualizar estado de {{ getCandidateName() }}
        </h3>

        <hr class="border-t border-gray-200 dark:border-gray-700 mb-5">
        
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
          Selecciona el nuevo estado para esta aplicación.
        </p>
        
        <!-- Status Selection -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado</label>
          <div class="space-y-2">
            <button 
              v-for="option in statusOptions" 
              :key="option.id"
              @click="selectedStatus = option.id"
              :class="[ 
                'w-full flex cursor-pointer items-center px-3 py-3 rounded-lg text-left transition-colors border', 
                selectedStatus === option.id 
                  ? 'bg-[#193CB8]/10 dark:bg-blue-900/20 text-[#193CB8] dark:text-blue-400 border-[#193CB8] dark:border-blue-400' 
                  : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600' 
              ]"
            >
              <i :class="['bx mr-2 text-lg', option.icon, option.color]"></i>
              {{ option.name }}
            </button>
          </div>
        </div>
        
       
        
        <!-- Actions -->
        <div class="flex justify-end gap-3">
          <button 
            @click="closeModal"
            class="px-4 py-2 border cursor-pointer border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Cancelar
          </button>
          
          <button 
            @click="updateStatus"
            :disabled="!selectedStatus"
            :class="[ 
              'px-4 py-2 cursor-pointer rounded-md text-white transition-colors', 
              selectedStatus 
                ? 'bg-[#193CB8] dark:bg-blue-700 hover:bg-[#142d8c] dark:hover:bg-blue-800' 
                : 'bg-gray-400 dark:bg-gray-600 cursor-not-allowed' 
            ]"
          >
            Actualizar estado
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
</template>