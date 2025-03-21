<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  candidate: {
    type: Object,
    default: null
  }
});

const emit = defineEmits([
  'close',
  'update-status',
  'message'
]);

const activeTab = ref('profile');

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

// Cerrar modal
const closeModal = () => {
  emit('close');
};

// Actualizar estado
const updateStatus = () => {
  emit('update-status', props.candidate);
};

// Enviar mensaje
const messageCandidate = () => {
  emit('message', props.candidate.user_id);
};

// Ver perfil completo
const viewFullProfile = () => {
  if (props.candidate && props.candidate.user && props.candidate.user.profile) {
    router.get(`/perfil/${props.candidate.user.profile.slang || props.candidate.user_id}`);
  }
};

// Resetear tab activo cuando cambia el candidato
watch(() => props.candidate, () => {
  activeTab.value = 'profile';
});
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <!-- Overlay -->
      <div class="fixed inset-0 transition-opacity bg-black/80" @click="closeModal"></div>

      <!-- Modal Panel -->
      <div class="inline-block w-full max-w-4xl px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:p-6">
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button 
            @click="closeModal"
            class="text-gray-400 bg-white rounded-md hover:text-gray-500 focus:outline-none"
          >
            <i class='bx bx-x text-2xl'></i>
          </button>
        </div>

        <div v-if="candidate">
          <!-- Header -->
          <div class="flex items-start gap-4 mb-6">
            <!-- Candidate Avatar -->
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center shrink-0 border border-gray-200 overflow-hidden">
              <img 
                v-if="candidate.user && candidate.user.profile && candidate.user.profile.profile_picture" 
                :src="candidate.user.profile.profile_picture" 
                :alt="candidate.user.name" 
                class="w-full h-full object-cover"
              />
              <i v-else class='bx bx-user text-4xl text-gray-400'></i>
            </div>

            <!-- Candidate Info -->
            <div class="flex-1">
              <h2 class="text-2xl font-bold text-gray-900">
                {{ candidate.user ? `${candidate.user.name} ${candidate.user.last_name_1 || ''}` : 'Candidato' }}
              </h2>
              <p class="text-gray-600">
                {{ candidate.user && candidate.user.profile ? candidate.user.profile.title || 'Sin título profesional' : 'Sin información' }}
              </p>
              
              <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm mt-2">
                <div class="flex items-center text-gray-600">
                  <i class='bx bx-map-pin mr-1 text-[#193CB8]'></i>
                  {{ candidate.user && candidate.user.profile ? candidate.user.profile.location || 'Sin ubicación' : 'Sin ubicación' }}
                </div>
                <div class="flex items-center text-gray-600">
                  <i class='bx bx-envelope mr-1 text-[#193CB8]'></i>
                  {{ candidate.user ? candidate.user.email : 'Sin email' }}
                </div>
                <div class="flex items-center text-gray-600">
                  <i class='bx bx-phone mr-1 text-[#193CB8]'></i>
                  {{ candidate.user && candidate.user.profile ? candidate.user.profile.phone || 'Sin teléfono' : 'Sin teléfono' }}
                </div>
              </div>
            </div>

            <!-- Status Badge -->
            <div 
              :class="[
                'px-3 py-1 rounded-lg text-sm font-medium flex items-center',
                getStatusInfo(candidate.status).color
              ]"
            >
              <i :class="['bx mr-1', getStatusInfo(candidate.status).icon]"></i>
              {{ getStatusInfo(candidate.status).text }}
            </div>
          </div>

          <!-- Tabs -->
          <div class="border-b border-gray-200 mb-6">
            <div class="flex -mb-px">
              <button 
                @click="activeTab = 'profile'"
                :class="[
                  'py-2 px-4 text-sm font-medium border-b-2 focus:outline-none',
                  activeTab === 'profile'
                    ? 'border-[#193CB8] text-[#193CB8]'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                <i class='bx bx-user mr-1'></i>
                Perfil
              </button>
              <button 
                @click="activeTab = 'application'"
                :class="[
                  'py-2 px-4 text-sm font-medium border-b-2 focus:outline-none',
                  activeTab === 'application'
                    ? 'border-[#193CB8] text-[#193CB8]'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                <i class='bx bx-file mr-1'></i>
                Aplicación
              </button>
              <button 
                @click="activeTab = 'timeline'"
                :class="[
                  'py-2 px-4 text-sm font-medium border-b-2 focus:outline-none',
                  activeTab === 'timeline'
                    ? 'border-[#193CB8] text-[#193CB8]'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                <i class='bx bx-history mr-1'></i>
                Historial
              </button>
            </div>
          </div>

          <!-- Tab Content -->
          <div class="max-h-[60vh] overflow-y-auto pr-2">
            <!-- Profile Tab -->
            <div v-if="activeTab === 'profile'" class="space-y-6">
              <!-- Skills -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Habilidades</h3>
                <div class="flex flex-wrap gap-2">
                  <span 
                    v-for="(skill, index) in candidate.user && candidate.user.profile ? candidate.user.profile.skills || [] : []" 
                    :key="index"
                    class="px-3 py-1.5 bg-[#193CB8]/10 text-[#193CB8] rounded-md text-sm font-medium"
                  >
                    {{ skill }}
                  </span>
                  <span v-if="!candidate.user || !candidate.user.profile || !candidate.user.profile.skills || candidate.user.profile.skills.length === 0" class="text-gray-500">
                    No hay habilidades registradas
                  </span>
                </div>
              </div>

              <!-- Experience -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Experiencia</h3>
                <div v-if="candidate.user && candidate.user.profile && candidate.user.profile.experiences && candidate.user.profile.experiences.length > 0" class="space-y-4">
                  <div 
                    v-for="(experience, index) in candidate.user.profile.experiences" 
                    :key="index"
                    class="p-4 border border-gray-200 rounded-lg"
                  >
                    <div class="flex justify-between">
                      <h4 class="font-medium text-gray-900">{{ experience.position }}</h4>
                      <span class="text-sm text-gray-500">{{ experience.start_date }} - {{ experience.end_date || 'Actual' }}</span>
                    </div>
                    <p class="text-gray-700">{{ experience.company }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ experience.description }}</p>
                  </div>
                </div>
                <p v-else class="text-gray-500">No hay experiencia registrada</p>
              </div>

              <!-- Education -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Educación</h3>
                <div v-if="candidate.user && candidate.user.profile && candidate.user.profile.education && candidate.user.profile.education.length > 0" class="space-y-4">
                  <div 
                    v-for="(edu, index) in candidate.user.profile.education" 
                    :key="index"
                    class="p-4 border border-gray-200 rounded-lg"
                  >
                    <div class="flex justify-between">
                      <h4 class="font-medium text-gray-900">{{ edu.degree }}</h4>
                      <span class="text-sm text-gray-500">{{ edu.start_date }} - {{ edu.end_date || 'Actual' }}</span>
                    </div>
                    <p class="text-gray-700">{{ edu.institution }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ edu.description }}</p>
                  </div>
                </div>
                <p v-else class="text-gray-500">No hay educación registrada</p>
              </div>
            </div>

            <!-- Application Tab -->
            <div v-if="activeTab === 'application'" class="space-y-6">
              <!-- Job Details -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Oferta aplicada</h3>
                <div class="p-4 border border-gray-200 rounded-lg">
                  <h4 class="font-medium text-[#193CB8]">{{ candidate.job_offer ? candidate.job_offer.title : 'Oferta no disponible' }}</h4>
                  <p class="text-gray-700 text-sm mt-1">{{ candidate.job_offer ? candidate.job_offer.company.company_name : '' }}</p>
                  <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm mt-3">
                    <div class="flex items-center text-gray-600">
                      <i class='bx bx-map-pin mr-1 text-[#193CB8]'></i>
                      {{ candidate.job_offer ? candidate.job_offer.location : '' }}
                    </div>
                    <div class="flex items-center text-gray-600">
                      <i class='bx bx-calendar mr-1 text-[#193CB8]'></i>
                      Aplicado el {{ formatDate(candidate.created_at) }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cover Letter -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Carta de presentación</h3>
                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                  <p class="text-gray-700 whitespace-pre-line">{{ candidate.cover_letter || 'No hay carta de presentación' }}</p>
                </div>
              </div>

              <!-- Feedback -->
              <div v-if="candidate.feedback">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Feedback</h3>
                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                  <p class="text-gray-700">{{ candidate.feedback }}</p>
                </div>
              </div>
            </div>

            <!-- Timeline Tab -->
            <div v-if="activeTab === 'timeline'" class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 mb-3">Historial de la aplicación</h3>
              
              <div class="relative pl-8 border-l-2 border-gray-200 space-y-8">
                <!-- Aplicación enviada -->
                <div class="relative">
                  <div class="absolute -left-[17px] w-8 h-8 rounded-full bg-[#193CB8] flex items-center justify-center text-white">
                    <i class='bx bx-send'></i>
                  </div>
                  <div class="ml-4">
                    <p class="font-medium">Aplicación enviada</p>
                    <p class="text-sm text-gray-500">{{ formatDate(candidate.created_at) }}</p>
                  </div>
                </div>
                
                <!-- Cambios de estado (ejemplo) -->
                <div v-if="candidate.status_changes && candidate.status_changes.length > 0">
                  <div 
                    v-for="(change, index) in candidate.status_changes" 
                    :key="index"
                    class="relative mb-8"
                  >
                    <div 
                      class="absolute -left-[17px] w-8 h-8 rounded-full flex items-center justify-center text-white"
                      :class="{
                        'bg-yellow-500': change.status === 'pending',
                        'bg-blue-500': change.status === 'in_process',
                        'bg-purple-500': change.status === 'interview',
                        'bg-green-500': change.status === 'accepted',
                        'bg-red-500': change.status === 'rejected',
                      }"
                    >
                      <i :class="{
                        'bx bx-time': change.status === 'pending',
                        'bx bx-loader': change.status === 'in_process',
                        'bx bx-calendar-check': change.status === 'interview',
                        'bx bx-check': change.status === 'accepted',
                        'bx bx-x': change.status === 'rejected',
                      }"></i>
                    </div>
                    <div class="ml-4">
                      <p class="font-medium">{{ getStatusInfo(change.status).text }}</p>
                      <p class="text-sm text-gray-500">{{ formatDate(change.date) }}</p>
                      <p v-if="change.note" class="mt-2 p-3 bg-gray-50 rounded-md text-sm border border-gray-200">
                        {{ change.note }}
                      </p>
                    </div>
                  </div>
                </div>
                
                <!-- Estado actual si no hay historial -->
                <div v-else class="relative">
                  <div 
                    class="absolute -left-[17px] w-8 h-8 rounded-full flex items-center justify-center text-white"
                    :class="{
                      'bg-yellow-500': candidate.status === 'pending',
                      'bg-blue-500': candidate.status === 'in_process',
                      'bg-purple-500': candidate.status === 'interview',
                      'bg-green-500': candidate.status === 'accepted',
                      'bg-red-500': candidate.status === 'rejected',
                    }"
                  >
                    <i :class="{
                      'bx bx-time': candidate.status === 'pending',
                      'bx bx-loader': candidate.status === 'in_process',
                      'bx bx-calendar-check': candidate.status === 'interview',
                      'bx bx-check': candidate.status === 'accepted',
                      'bx bx-x': candidate.status === 'rejected',
                    }"></i>
                  </div>
                  <div class="ml-4">
                    <p class="font-medium">{{ getStatusInfo(candidate.status).text }}</p>
                    <p class="text-sm text-gray-500">Estado actual</p>
                    <p v-if="candidate.feedback" class="mt-2 p-3 bg-gray-50 rounded-md text-sm border border-gray-200">
                      {{ candidate.feedback }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-6 pt-6 border-t border-gray-200 flex flex-wrap gap-3">
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
            
            <button 
              @click="viewFullProfile"
              class="flex-1 bg-white border border-[#193CB8] text-[#193CB8] hover:bg-blue-50 font-medium py-2 rounded-lg transition-colors flex items-center justify-center"
            >
              <i class='bx bx-user-detail mr-1'></i>
              Ver perfil completo
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>