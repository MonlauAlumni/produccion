<script setup>
import { defineProps, defineEmits, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  application: {
    type: Object,
    required: true
  }
});

const isExpanded = ref(false);

const emit = defineEmits([
  'view',
  'update-status',
  'message',
  'download-cv',
  'delete'
]);

// Obtener información de estado de la aplicación
const getStatusInfo = (status) => {
  const statusMap = {
    'pending': { 
      text: 'Pendiente de revisión', 
      color: 'bg-yellow-100 text-yellow-800 border-yellow-200',
      icon: 'bx-time'
    },
    'in_process': { 
      text: 'En proceso de selección', 
      color: 'bg-blue-100 text-blue-800 border-blue-200',
      icon: 'bx-loader'
    },
    'interview': { 
      text: 'Entrevista programada', 
      color: 'bg-purple-100 text-purple-800 border-purple-200',
      icon: 'bx-calendar-check'
    },
    'accepted': { 
      text: 'Candidato aceptado', 
      color: 'bg-green-100 text-green-800 border-green-200',
      icon: 'bx-check-circle'
    },
    'rejected': { 
      text: 'Candidato rechazado', 
      color: 'bg-red-100 text-red-800 border-red-200',
      icon: 'bx-x-circle'
    }
  };
  
  return statusMap[status] || { 
    text: 'Estado desconocido', 
    color: 'bg-gray-100 text-gray-800 border-gray-200',
    icon: 'bx-question-mark'
  };
};

// Formatear fecha
const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

// Calcular días desde la aplicación
const getDaysSinceApplication = (dateString) => {
  if (!dateString) return 0;
  
  const applicationDate = new Date(dateString);
  const today = new Date();
  const diffTime = Math.abs(today - applicationDate);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  return diffDays;
};

// Ver detalle del candidato
const viewCandidate = () => {
  emit('view', props.application);
};

// Actualizar estado
const updateStatus = () => {
  emit('update-status', props.application);
};

// Enviar mensaje
const messageCandidate = () => {
  // Navegar a la página de mensajes con parámetros para abrir el modal
  // y preseleccionar el usuario y la oferta
  router.get('/mensajes', {
    openModal: true,
    userId: props.application.user_id,
    jobId: props.application.job_offer ? props.application.job_offer.id : null,
    userName: props.application.student ? `${props.application.student.name} ${props.application.student.last_name_1 || ''}` : 'Candidato'
  });
};

const deleteCandidate = () => {
  emit('delete', props.application);
};

const downloadCV = () => {
  emit('download-cv', props.application.student.profile.slang);
};
</script>

<template>
  <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-xl">
    <!-- Status Banner -->
    <div 
      :class="[
        'px-4 py-2 flex items-center justify-between',
        getStatusInfo(application.status).color
      ]"
    >
      <div class="flex items-center gap-2">
        <i :class="['bx text-lg', getStatusInfo(application.status).icon]"></i>
        <span class="font-medium">{{ getStatusInfo(application.status).text }}</span>
      </div>
      
      <div class="text-sm text-gray-700">
        <i class='bx bx-calendar'></i>
        <span class="ml-2 text-gray-700">Aplicó el {{ formatDate(application.created_at) }}</span>
      </div>
    </div>

    <div class="p-5">
      <!-- Header with Download CV Button -->
      <div class="flex items-start gap-4">
        <!-- Candidate Avatar -->
        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center shrink-0 border border-gray-200 dark:border-gray-600 overflow-hidden">
          <img 
            v-if="application.student && application.student.profile && application.student.profile.profile_picture" 
            :src="application.student.profile.profile_picture" 
            :alt="application.student.name" 
            class="w-full h-full object-cover"
          />
          <i v-else class='bx bx-user text-3xl text-gray-400 dark:text-gray-500'></i>
        </div>

        <!-- Candidate Info -->
        <div class="flex-1">
          <div class="flex items-start justify-between">
            <div>
              <h2 class="font-bold text-lg text-[#193CB8] dark:text-blue-400 hover:underline cursor-pointer" @click="viewCandidate">
                {{ application.student ? `${application.student.name} ${application.student.last_name_1 || ''}` : 'Candidato' }}
              </h2>
              <div class="text-gray-700 dark:text-gray-300">
                {{ application.student && application.student.profile ? application.student.profile.job_title || 'Sin título profesional' : 'Sin información' }}
              </div>
            </div>

            <!-- Download CV Button -->
            <button 
              @click="downloadCV"
              class="bg-gradient-to-r from-green-500 to-emerald-500 text-white hover:shadow-lg font-medium py-2 px-5 gap-3 rounded-lg transition-all flex items-center justify-center cursor-pointer"
            >
              <i class='bx bx-download text-lg'></i>
              <span>Descargar CV</span>
            </button>
          </div>

          <!-- Match Score and Job Details -->
          <div class="mt-3 flex flex-wrap items-center gap-2">
            <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm">
              <div class="flex items-center text-gray-600 dark:text-gray-400">
                <i class='bx bx-briefcase mr-1 text-[#193CB8] dark:text-blue-400'></i>
                {{ application.job_offer ? application.job_offer.title : 'Oferta no disponible' }}
              </div>
              <div class="flex items-center text-gray-600 dark:text-gray-400">
                <i class='bx bx-map-pin mr-1 text-[#193CB8] dark:text-blue-400'></i>
                {{ application.student && application.student.profile ? application.student.profile.location || 'Sin ubicación' : 'Sin ubicación' }}
              </div>
              <div class="flex items-center text-gray-600 dark:text-gray-400">
                <i class='bx bx-time-five mr-1 text-[#193CB8] dark:text-blue-400'></i>
                {{ getDaysSinceApplication(application.created_at) }} días desde la aplicación
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Skills & Experience -->
      <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
        
        
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Carta de presentación</h3>
        <p class="text-gray-700 dark:text-gray-300 text-sm bg-gray-50 dark:bg-gray-700 p-2 whitespace-pre-line rounded-md">
          {{ isExpanded ? (application.cover_letter || 'No hay carta de presentación') : (application.cover_letter ? application.cover_letter.slice(0, 150) + '...' : 'No hay carta de presentación') }}
          <button 
            v-if="application.cover_letter && application.cover_letter.length > 150"
            @click="isExpanded = !isExpanded"
            class="text-[#193CB8] dark:text-blue-400 cursor-pointer font-medium ml-1"
          >
            {{ isExpanded ? 'Ver menos' : 'Ver más' }}
          </button>
        </p>
      </div>

      <!-- Actions -->
      <div class="mt-4 grid grid-cols-3 gap-2">
        <button 
          @click="viewCandidate"
          class="bg-white dark:bg-gray-800 border border-[#193CB8] dark:border-blue-400 text-[#193CB8] dark:text-blue-400 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900 font-medium py-2 rounded-lg transition-colors flex items-center justify-center"
        >
          <i class='bx bx-user-detail mr-1'></i>
          Ver perfil
        </button>
        
        <button 
          @click="updateStatus"
          class="bg-[#193CB8] dark:bg-blue-700 text-white font-medium py-2 rounded-lg cursor-pointer hover:bg-[#142d8c] dark:hover:bg-blue-800 transition-colors flex items-center justify-center"
        >
          <i class='bx bx-edit mr-1'></i>
          Cambiar estado 
        </button>
        
        <button 
          v-if="application.status !== 'rejected'"
          @click="messageCandidate"
          class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium py-2 rounded-lg transition-colors flex items-center cursor-pointer justify-center"
        >
          <i class='bx bx-envelope mr-1'></i>
          Contactar
        </button>
        <button 
          v-else
          @click="deleteCandidate"
          class="border border-gray-300 dark:border-gray-600 text-white bg-red-600 dark:bg-red-700 hover:bg-red-500 dark:hover:bg-red-600 font-medium py-2 rounded-lg transition-colors flex items-center cursor-pointer justify-center"
        >
          <i class='bx bx-trash mr-1'></i>
          Eliminar
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>