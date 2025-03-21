<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  application: {
    type: Object,
    required: true
  }
});

const emit = defineEmits([
  'view',
  'update-status',
  'message'
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
  emit('message', props.application.user_id);
};
</script>

<template>
  <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200 transition-all duration-300 hover:shadow-xl">
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
      
      <div class="text-sm">
        <i class='bx bx-calendar'></i>
        <span>Aplicó el {{ formatDate(application.created_at) }}</span>
      </div>
    </div>

    <div class="p-5">
      <!-- Header -->
      <div class="flex items-start gap-4">
        <!-- Candidate Avatar -->
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center shrink-0 border border-gray-200 overflow-hidden">
          <img 
            v-if="application.user && application.user.profile && application.user.profile.profile_picture" 
            :src="application.user.profile.profile_picture" 
            :alt="application.user.name" 
            class="w-full h-full object-cover"
          />
          <i v-else class='bx bx-user text-3xl text-gray-400'></i>
        </div>

        <!-- Candidate Info -->
        <div class="flex-1">
          <div class="flex items-start justify-between">
            <div>
              <h2 class="font-bold text-lg text-[#193CB8] hover:underline cursor-pointer" @click="viewCandidate">
                {{ application.user ? `${application.user.name} ${application.user.last_name_1 || ''}` : 'Candidato' }}
              </h2>
              <div class="text-gray-700">
                {{ application.user && application.user.profile ? application.user.profile.title || 'Sin título profesional' : 'Sin información' }}
              </div>
            </div>

            <!-- Match Score -->
            <div class="bg-[#193CB8]/10 text-[#193CB8] text-sm font-bold px-3 py-1 rounded-full flex items-center">
              <i class='bx bx-badge  text-sm font-bold px-3 py-1 rounded-full flex items-center'></i>
              <i class='bx bx-badge-check mr-1'></i>
              Match 85%
            </div>
          </div>

          <!-- Job Details -->
          <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm mt-3">
            <div class="flex items-center text-gray-600">
              <i class='bx bx-briefcase mr-1 text-[#193CB8]'></i>
              {{ application.job_offer ? application.job_offer.title : 'Oferta no disponible' }}
            </div>
            <div class="flex items-center text-gray-600">
              <i class='bx bx-map-pin mr-1 text-[#193CB8]'></i>
              {{ application.user && application.user.profile ? application.user.profile.location || 'Sin ubicación' : 'Sin ubicación' }}
            </div>
            <div class="flex items-center text-gray-600">
              <i class='bx bx-time-five mr-1 text-[#193CB8]'></i>
              {{ getDaysSinceApplication(application.created_at) }} días desde la aplicación
            </div>
          </div>
        </div>
      </div>

      <!-- Skills & Experience -->
      <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="flex flex-wrap gap-2 mb-3">
          <span 
            v-for="(skill, index) in application.user && application.user.profile ? application.user.profile.skills || [] : []" 
            :key="index"
            class="px-2 py-1 bg-[#193CB8]/10 text-[#193CB8] rounded-md text-xs font-medium"
          >
            {{ skill }}
          </span>
          <span v-if="!application.user || !application.user.profile || !application.user.profile.skills || application.user.profile.skills.length === 0" class="text-gray-500 text-sm">
            No hay habilidades registradas
          </span>
        </div>
        
        <p class="text-gray-700 text-sm line-clamp-2">
          {{ application.cover_letter || 'No hay carta de presentación' }}
        </p>
      </div>

      <!-- Actions -->
      <div class="mt-4 flex flex-wrap gap-2">
        <button 
          @click="viewCandidate"
          class="flex-1 bg-white border border-[#193CB8] text-[#193CB8] hover:bg-blue-50 font-medium py-2 rounded-lg transition-colors flex items-center justify-center"
        >
          <i class='bx bx-user-detail mr-1'></i>
          Ver perfil
        </button>
        
        <button 
          @click="updateStatus"
          class="flex-1 bg-[#193CB8] text-white font-medium py-2 rounded-lg hover:bg-[#142d8c] transition-colors flex items-center justify-center"
        >
          <i class='bx bx-edit mr-1'></i>
          Cambiar estado
        </button>
        
        <button 
          @click="messageCandidate"
          class="flex-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 rounded-lg transition-colors flex items-center justify-center"
        >
          <i class='bx bx-envelope mr-1'></i>
          Contactar
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