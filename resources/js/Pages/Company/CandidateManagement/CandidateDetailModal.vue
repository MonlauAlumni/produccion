<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

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

const page = usePage();
const userRole = computed(() => page.props.auth.user?.roles?.[0]?.name ?? null);

const emit = defineEmits(['close', 'update-status', 'message', 'view-personal-profile', 'download-cv']);

const activeTab = ref('profile');

const closeModal = () => {
  emit('close');
};

const updateStatus = () => {
  emit('update-status', props.candidate);
};

const messageCandidate = () => {
  emit('message', props.candidate);
};

const viewFullProfile = () => {
  emit('view-personal-profile', props.candidate);
};

const downloadCV = () => {
  emit('download-cv', props.candidate);
};

const formatDate = (date) => {
  if (!date) return 'Fecha desconocida';
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const getStatusInfo = (status) => {
  const statusMap = {
    pending: { text: 'Pendiente', icon: 'bx-time', color: 'bg-yellow-100 text-yellow-800' },
    in_process: { text: 'En proceso', icon: 'bx-loader', color: 'bg-blue-100 text-blue-800' },
    interview: { text: 'Entrevista', icon: 'bx-calendar-check', color: 'bg-purple-100 text-purple-800' },
    accepted: { text: 'Aceptado', icon: 'bx-check', color: 'bg-green-100 text-green-800' },
    rejected: { text: 'Rechazado', icon: 'bx-x', color: 'bg-red-100 text-red-800' }
  };

  return statusMap[status] || { text: 'Desconocido', icon: 'bx-question-mark', color: 'bg-gray-100 text-gray-800' };
};

const getStatusClass = (status) => {
  const statusClasses = {
    pending: 'bg-yellow-500 text-white',
    in_process: 'bg-blue-500 text-white',
    interview: 'bg-purple-500 text-white',
    accepted: 'bg-green-500 text-white',
    rejected: 'bg-red-500 text-white'
  };

  return statusClasses[status] || 'bg-gray-500 text-white';
};
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <!-- Backdrop -->
      <div class="fixed inset-0 transition-opacity bg-black/70 dark:bg-black/80 backdrop-blur-sm" @click="closeModal"></div>

      <!-- Modal Panel -->
      <div
        class="inline-block w-full max-w-4xl overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-xl shadow-2xl sm:my-8 sm:align-middle border border-gray-200 dark:border-gray-700">
        <!-- Header -->
        <div class="relative bg-[#193CB8] dark:bg-blue-700 text-white p-6">
          <button @click="closeModal"
            class="absolute top-4 right-4 text-white/80 hover:text-white focus:outline-none transition-colors">
            <i class='bx bx-x text-2xl'></i>
          </button>

          <div v-if="candidate" class="flex items-start gap-5">
            <!-- Candidate Avatar -->
            <div
              class="w-24 h-24 rounded-full flex items-center justify-center shrink-0 border-4 border-white/30 overflow-hidden bg-white/10">
              <img v-if="candidate.student && candidate.student.profile && candidate.student.profile.profile_picture"
                :src="candidate.student.profile.profile_picture" :alt="candidate.student.name"
                class="w-full h-full object-cover" />
              <i v-else class='bx bx-user text-4xl'></i>
            </div>

            <!-- Candidate Info -->
            <div class="flex-1">
              <h2 class="text-2xl font-bold">
                {{ candidate.student ? `${candidate.student.name} ${candidate.student.last_name_1 || ''}` : 'Candidato' }}
              </h2>
              <p class="text-white/80 text-lg">
                {{ candidate.student && candidate.student.profile ? candidate.student.profile.job_title || 'Sin título profesional' : 'Sin información' }}
              </p>
              <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm text-white/90">
                <div class="flex items-center">
                  <i class='bx bx-map-pin mr-1'></i>
                  {{ candidate.student && candidate.student.profile ? candidate.student.profile.location || 'Sin ubicación' : 'Sin ubicación' }}
                </div>
                <div class="flex items-center">
                  <i class='bx bx-envelope mr-1'></i>
                  {{ candidate.student ? candidate.student.email : 'Sin email' }}
                </div>
                <div class="flex items-center">
                  <i class='bx bx-phone mr-1'></i>
                  {{ candidate.student && candidate.student.profile ? candidate.student.profile.phone || 'Sin teléfono' : 'Sin teléfono' }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-3 border-b border-gray-200 dark:border-gray-700">
          <div class="flex gap-2">
            <button @click="activeTab = 'profile'" :class="[
              'py-2 px-4 text-sm font-medium cursor-pointer rounded-full transition-colors',
              activeTab === 'profile'
                ? 'bg-[#193CB8] dark:bg-blue-700 text-white shadow-md'
                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700'
            ]">
              <i class='bx bx-user mr-1'></i>
              Perfil
            </button>
            <button @click="activeTab = 'application'" :class="[
              'py-2 px-4 text-sm font-medium cursor-pointer rounded-full transition-colors',
              activeTab === 'application'
                ? 'bg-[#193CB8] dark:bg-blue-700 text-white shadow-md'
                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700'
            ]">
              <i class='bx bx-file mr-1'></i>
              Aplicación
            </button>
            <button @click="activeTab = 'timeline'" :class="[
              'py-2 px-4 text-sm font-medium cursor-pointer rounded-full transition-colors',
              activeTab === 'timeline'
                ? 'bg-[#193CB8] dark:bg-blue-700 text-white shadow-md'
                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700'
            ]">
              <i class='bx bx-history mr-1'></i>
              Historial
            </button>
          </div>
        </div>

        <!-- Tab Content -->
        <div class="max-h-[60vh] overflow-y-auto p-6 custom-scrollbar bg-white dark:bg-gray-800">
          <!-- Profile Tab -->
          <div v-if="activeTab === 'profile'" class="space-y-8">
            <!-- Skills -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 mb-4 flex items-center">
                <i class='bx bx-bulb text-[#193CB8] dark:text-blue-400 mr-2 text-xl'></i>
                Habilidades
              </h3>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="(skill, index) in candidate.student && candidate.student.profile ? candidate.student.profile.skills || [] : []"
                  :key="index"
                  class="px-3 py-1.5 bg-[#193CB8]/10 dark:bg-blue-900/20 text-[#193CB8] dark:text-blue-400 rounded-full text-sm font-medium">
                  {{ skill }}
                </span>
                <span
                  v-if="!candidate.student || !candidate.student.profile || !candidate.student.profile.skills || candidate.student.profile.skills.length === 0"
                  class="text-gray-500 dark:text-gray-400">
                  No hay habilidades registradas
                </span>
              </div>
            </div>

            <!-- Experience with card design -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class='bx bx-briefcase text-[#193CB8] mr-2 text-xl'></i>
                Experiencia
              </h3>
              <div
                v-if="candidate.student && candidate.student.profile && candidate.student.profile.experience && candidate.student.profile.experience.length > 0"
                class="space-y-4">
                <div v-for="(experience, index) in candidate.student.profile.experience" :key="index"
                  class="p-5 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:shadow-md transition-all duration-300">
                  <div class="flex justify-between items-start">
                    <h4 class="font-semibold text-gray-900 text-lg">{{ experience.position }}</h4>
                    <span class="text-sm bg-[#193CB8]/10 text-[#193CB8] px-3 py-1 rounded-full">
                      {{ experience.start_date }} - {{ experience.end_date || 'Actual' }}
                    </span>
                  </div>
                  <p class="text-gray-700 font-medium mt-1">{{ experience.company }}</p>
                  <p class="text-sm text-gray-600 mt-3 leading-relaxed">{{ experience.description }}</p>
                </div>
              </div>
              <p v-else class="text-gray-500 bg-gray-50 p-4 rounded-lg border border-gray-200">No hay experiencia
                registrada</p>
            </div>

            <!-- Education with card design -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class='bx bx-book-open text-[#193CB8] mr-2 text-xl'></i>
                Educación
              </h3>
              <div
                v-if="candidate.student && candidate.student.profile && candidate.student.profile.education && candidate.student.profile.education.length > 0"
                class="space-y-4">
                <div v-for="(edu, index) in candidate.student.profile.education" :key="index"
                  class="p-5 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:shadow-md transition-all duration-300">
                  <div class="flex justify-between items-start">
                    <h4 class="font-semibold text-gray-900 text-lg">{{ edu.degree }}</h4>
                    <span class="text-sm bg-[#193CB8]/10 text-[#193CB8] px-3 py-1 rounded-full">
                      {{ edu.start_date }} - {{ edu.end_date || 'Actual' }}
                    </span>
                  </div>
                  <p class="text-gray-700 font-medium mt-1">{{ edu.institution }}</p>
                  <p class="text-sm text-gray-600 mt-3 leading-relaxed">{{ edu.description }}</p>
                </div>
              </div>
              <p v-else class="text-gray-500 bg-gray-50 p-4 rounded-lg border border-gray-200">No hay educación
                registrada</p>
            </div>
          </div>

          <!-- Application Tab -->
          <div v-if="activeTab === 'application'" class="space-y-8">
            <!-- Job Details with gradient accent -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
              <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-[#193CB8] to-[#4361ee]"></div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class='bx bx-briefcase text-[#193CB8] mr-2 text-xl'></i>
                Oferta aplicada
              </h3>
              <div class="pl-4">
                <h4 class="font-semibold text-[#193CB8] text-xl">{{ candidate.job_offer ? candidate.job_offer.title :
                  'Oferta no disponible' }}</h4>
                <p class="text-gray-700 mt-1 font-medium">{{ candidate.job_offer ?
                  candidate.job_offer.company.company_name : '' }}</p>
                <div class="flex flex-wrap gap-x-6 gap-y-3 text-sm mt-4">
                  <div class="flex items-center text-gray-600">
                    <i class='bx bx-map-pin mr-1 text-[#193CB8] text-lg'></i>
                    {{ candidate.job_offer ? candidate.job_offer.location : '' }}
                  </div>
                  <div class="flex items-center text-gray-600">
                    <i class='bx bx-calendar mr-1 text-[#193CB8] text-lg'></i>
                    Aplicado el {{ formatDate(candidate.created_at) }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Cover Letter with paper effect -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class='bx bx-envelope-open text-[#193CB8] mr-2 text-xl'></i>
                Carta de presentación
              </h3>
              <div class="p-6 border border-gray-200 rounded-xl bg-[#fcfcfc] shadow-inner relative">
                <div class="absolute top-0 right-0 w-12 h-12 bg-gray-100 rounded-bl-xl"></div>
                <div
                  class="absolute top-0 right-0 w-0 h-0 border-t-12 border-r-12 border-t-[#193CB8]/10 border-r-transparent">
                </div>
                <p class="text-gray-700 whitespace-pre-line leading-relaxed relative z-10">
                  {{ candidate.cover_letter || 'No hay carta de presentación' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Timeline Tab with animated timeline -->
          <div v-if="activeTab === 'timeline'" class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
              <i class='bx bx-history text-[#193CB8] mr-2 text-xl'></i>
              Historial de la aplicación
            </h3>

            <div class="relative pl-10 border-l-2 border-gray-200 space-y-8">
              <!-- Aplicación enviada -->
              <div class="relative">
                <div
                  class="absolute -left-[21px] w-10 h-10 rounded-full bg-[#193CB8] flex items-center justify-center text-white  animate-pulse-slow">
                  <i class='bx bx-send'></i>
                </div>
                <div class="ml-4 bg-white px-4 rounded-xl border border-gray-100">
                  <p class="font-medium text-gray-900">Aplicación enviada</p>
                  <p class="text-sm text-gray-500">{{ formatDate(candidate.created_at) }}</p>
                </div>
              </div>

              <!-- Cambios de estado (ejemplo) -->
              <div v-if="candidate.status_changes && candidate.status_changes.length > 0">
                <div v-for="(change, index) in candidate.status_changes" :key="index" class="relative mb-8">
                  <div
                    class="absolute -left-[21px] w-10 h-10 rounded-full flex items-center justify-center text-white shadow-md"
                    :class="{
                      'bg-yellow-500': change.status === 'pending',
                      'bg-blue-500': change.status === 'in_process',
                      'bg-purple-500': change.status === 'interview',
                      'bg-green-500': change.status === 'accepted',
                      'bg-red-500': change.status === 'rejected',
                    }">
                    <i :class="{
                      'bx bx-time': change.status === 'pending',
                      'bx bx-loader': change.status === 'in_process',
                      'bx bx-calendar-check': change.status === 'interview',
                      'bx bx-check': change.status === 'accepted',
                      'bx bx-x': change.status === 'rejected',
                    }"></i>
                  </div>
                  <div class="ml-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="font-medium text-gray-900">{{ getStatusInfo(change.status).text }}</p>
                    <p class="text-sm text-gray-500">{{ formatDate(change.date) }}</p>
                    <p v-if="change.note" class="mt-3 p-4 bg-gray-50 rounded-lg text-sm border border-gray-200">
                      {{ change.note }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Estado actual si no hay historial -->
              <div v-else class="relative">
                <div
                  class="absolute -left-[21px] w-10 h-10 rounded-full flex items-center justify-center text-white shadow-md"
                  :class="{
                    'bg-yellow-500': candidate.status === 'pending',
                    'bg-blue-500': candidate.status === 'in_process',
                    'bg-purple-500': candidate.status === 'interview',
                    'bg-green-500': candidate.status === 'accepted',
                    'bg-red-500': candidate.status === 'rejected',
                  }">
                  <i :class="{
                    'bx bx-time': candidate.status === 'pending',
                    'bx bx-loader': candidate.status === 'in_process',
                    'bx bx-calendar-check': candidate.status === 'interview',
                    'bx bx-check': candidate.status === 'accepted',
                    'bx bx-x': candidate.status === 'rejected',
                  }"></i>
                </div>
                <div class="ml-4 bg-white px-4 rounded-xl  border border-gray-100">
                  <p class="font-medium text-gray-900">{{ getStatusInfo(candidate.status).text }}</p>
                  <p class="text-sm text-gray-500">Estado actual</p>
                  <p v-if="candidate.feedback" class="mt-3 px-4 bg-gray-50 rounded-lg text-sm border border-gray-200">
                    {{ candidate.feedback }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex flex-wrap gap-3">
          <button @click="updateStatus"
            class="flex-1 bg-gradient-to-r from-[#193CB8] to-[#4361ee] text-white font-medium py-3 rounded-lg hover:shadow-lg transition-all flex items-center justify-center">
            <i class='bx bx-edit mr-2'></i>
            Cambiar estado
          </button>
          <button 
            v-if="userRole === 'company'" 
            @click="messageCandidate"
            class="flex-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium py-3 rounded-lg transition-all flex items-center justify-center"
          >
            <i class='bx bx-envelope mr-2'></i>
            Contactar
          </button>
          <button @click="downloadCV"
            class="flex-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white hover:shadow-lg font-medium py-3 rounded-lg transition-all flex items-center justify-center">
            <i class='bx bx-download mr-2'></i>
            Descargar CV
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: rgba(25, 60, 184, 0.3) rgba(229, 231, 235, 0.5);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(229, 231, 235, 0.5);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(25, 60, 184, 0.3);
  border-radius: 10px;
}

.animate-pulse-slow {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {

  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.7;
  }
}
</style>
