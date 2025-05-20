<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Layout from '@/Components/Layout.vue';
import CandidateCard from '@/Pages/Company/CandidateManagement/CandidateCard.vue';
import CandidateDetailModal from '@/Pages/Company/CandidateManagement/CandidateDetailModal.vue';
import StatusUpdateModal from '@/Pages/Company/CandidateManagement/StatusUpdateModal.vue';

const page = usePage();

const props = defineProps({
  jobOffers: {
    type: Object,
    required: true
  },
  applications: {
    type: Object,
    default: () => ({
      data: [],
      current_page: 1,
      last_page: 1,
      from: null,
      to: null,
      total: 0
    })
  },
  stats: {
    type: Object,
  },
  selectedStatus: {
    type: String,
    default: 'all'
  },
});

// Estado para la interfaz
const isLoading = ref(false);
const showScrollTopButton = ref(false);
const searchQuery = ref('');
const selectedJobOffer = ref('all');
// Initialize with the prop value
const currentStatus = ref(props.selectedStatus || 'all');

const selectedCandidate = ref(null);
const showCandidateModal = ref(false);
const showStatusModal = ref(false);
const applicationToUpdate = ref(null);

// Watch for changes in the prop
watch(() => props.selectedStatus, (newValue) => {
  if (newValue) {
    currentStatus.value = newValue;
  }
});

// Extraer datos de las aplicaciones
const applicationsList = ref(props.applications || []);

const jobOffersList = ref(props.jobOffers || []);

// Paginación
const pagination = computed(() => ({
  currentPage: props.applications.current_page,
  lastPage: props.applications.last_page,
  from: props.applications.from,
  to: props.applications.to,
  total: props.applications.total || 0
}));

// Filtros de estado
const statusFilters = [
  { id: 'all', name: 'Todos los estados', icon: 'bx-list-ul', color: 'text-gray-700', bgColor: 'bg-gray-200' },
  { id: 'pending', name: 'Pendientes', icon: 'bx-time', color: 'text-yellow-600', bgColor: 'bg-yellow-100' },
  { id: 'in_process', name: 'En proceso', icon: 'bx-loader', color: 'text-blue-600', bgColor: 'bg-blue-100' },
  { id: 'interview', name: 'Entrevista', icon: 'bx-calendar-check', color: 'text-purple-600', bgColor: 'bg-purple-100' },
  { id: 'accepted', name: 'Aceptados', icon: 'bx-check-circle', color: 'text-green-600', bgColor: 'bg-green-100' },
  { id: 'rejected', name: 'Rechazados', icon: 'bx-x-circle', color: 'text-red-600', bgColor: 'bg-red-100' }
];

// Estadísticas
const stats = computed(() => {
  const total =
    props.stats.pending +
    props.stats.in_process +
    props.stats.interview +
    props.stats.accepted +
    props.stats.rejected;
  const pending = props.stats.pending || 0;
  const in_process = props.stats.in_process || 0;
  const interview = props.stats.interview || 0;
  const accepted = props.stats.accepted || 0;
  const rejected = props.stats.rejected || 0;
  
  return {
    total,
    pending,
    in_process,
    interview,
    accepted,
    rejected,
    conversionRate: total > 0 ? Math.round((accepted / total) * 100) : 0
  };
});

// Cargar más aplicaciones (paginación)
const loadMoreApplications = async () => {
  if (isLoading.value || pagination.value.currentPage >= pagination.value.lastPage) return;

  isLoading.value = true;

  try {
    const nextPage = pagination.value.currentPage + 1;
    await router.get(
      '/gestion-candidatos',
      {
        page: nextPage,
        job_offer: selectedJobOffer.value !== 'all' ? selectedJobOffer.value : undefined,
        status: currentStatus.value !== 'all' ? currentStatus.value : undefined,
        search: searchQuery.value || undefined
      },
      {
        preserveState: true,
        preserveScroll: true,
        only: ['applications'],
        onSuccess: (page) => {
          if (page.props.applications && page.props.applications.data) {
            applicationsList.value = [...applicationsList.value, ...page.props.applications.data];
          }
        }
      }
    );
  } catch (error) {
    console.error('Error al cargar más aplicaciones:', error);
  } finally {
    isLoading.value = false;
  }
};

// Manejar scroll infinito
const handleScroll = () => {
  const scrollPosition = window.scrollY + window.innerHeight;
  const pageHeight = document.documentElement.scrollHeight;

  // Mostrar/ocultar botón para volver arriba
  showScrollTopButton.value = window.scrollY > 500;

  // Cargar más aplicaciones cuando estamos cerca del final
  if (scrollPosition > pageHeight - 500 && !isLoading.value && pagination.value.currentPage < pagination.value.lastPage) {
    loadMoreApplications();
  }
};

// Volver al inicio de la página
const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
};

// Aplicar filtros
const applyFilters = () => {
  router.get('/gestion-candidatos', {
    job_offer: selectedJobOffer.value !== 'all' ? selectedJobOffer.value : undefined,
    status: currentStatus.value !== 'all' ? currentStatus.value : undefined,
    search: searchQuery.value || undefined
  }, {
    preserveScroll: true,
    only: ['applications']
  });
};

// Buscar candidatos
const searchCandidates = () => {
  applyFilters();
};

const downloadCV = (candidate) => {
  window.location.href = `/perfil/${candidate}/download-cv/`;
};

// Ver detalle del candidato
const viewCandidate = (candidate) => {
  selectedCandidate.value = candidate;
  showCandidateModal.value = true;


  
  
};

const deleteCandidate = (candidate) => {
  router.delete(`/gestion-candidatos/${candidate.id}`, {
    onSuccess: () => {
      applicationsList.value = applicationsList.value.filter(app => app.id !== candidate.id);
    }
  });
};

const viewPersonalProfile = (candidate) => {
  router.get(`/perfil/${candidate.student.profile.slang}`);
};

// Cerrar modal de candidato
const closeCandidateModal = () => {
  showCandidateModal.value = false;
  selectedCandidate.value = null;
};

// Abrir modal para actualizar estado
const openStatusModal = (application) => {
  applicationToUpdate.value = application;
  showStatusModal.value = true;
};

// Cerrar modal de actualización de estado
const closeStatusModal = () => {
  showStatusModal.value = false;
  applicationToUpdate.value = null;
};

// Actualizar estado de la aplicación
const updateApplicationStatus = (applicationId, newStatus) => {
  router.post(`/gestion-candidatos/${applicationId}/change-status`, {
    status: newStatus
  }, {
    preserveScroll: true,
    only: ['applications'],
    onSuccess: () => {
      showStatusModal.value = false;
      applicationToUpdate.value = null;
    }
  });
};  

const updateFilter = (filterId) => {
  currentStatus.value = filterId;
};

// Enviar mensaje al candidato
const messageCandidate = (candidateId) => {
  router.get(`/mensajes/nuevo/${candidateId}`);
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

// Inicializar
onMounted(() => {
  window.addEventListener('scroll', handleScroll);
  
  // Initialize selectedJobOffer from URL parameter if present
  if (page.props.ziggy?.query?.job_offer) {
    selectedJobOffer.value = page.props.ziggy.query.job_offer;
  }

  // Limpiar al desmontar
  return () => {
    window.removeEventListener('scroll', handleScroll);
  };
});
</script>

<template>
  <Layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col">
      <!-- Header Section -->
      <div class="bg-[#193CB8] dark:bg-[#142d8c] text-white py-8">
        <div class="max-w-6xl mx-auto px-4">
          <div class="flex flex-col md:flex-row items-start justify-between">
            <div class="mb-6 md:mb-0">
              <h1 class="text-3xl font-bold mb-2">Gestión de Candidatos</h1>
              <p class="text-blue-100 dark:text-blue-300 mb-4">Administra las aplicaciones a tus ofertas de trabajo</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <div class="bg-white/10 dark:bg-gray-800/50 backdrop-blur-sm p-3 rounded-lg border border-white/20 dark:border-gray-700 text-center">
                <div class="text-2xl font-bold">{{ stats.total }}</div>
                <div class="text-blue-100 dark:text-blue-300 text-sm">Total candidatos</div>
              </div>
              <div class="bg-white/10 dark:bg-gray-800/50 backdrop-blur-sm p-3 rounded-lg border border-white/20 dark:border-gray-700 text-center">
                <div class="text-2xl font-bold">{{ stats.pending }}</div>
                <div class="text-blue-100 dark:text-blue-300 text-sm">Pendientes</div>
              </div>
              <div class="bg-white/10 dark:bg-gray-800/50 backdrop-blur-sm p-3 rounded-lg border border-white/20 dark:border-gray-700 text-center">
                <div class="text-2xl font-bold">{{ stats.conversionRate }}%</div>
                <div class="text-blue-100 dark:text-blue-300 text-sm">Tasa de conversión</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex-1 py-6">
        <div class="max-w-6xl mx-auto px-4">
          <div class="flex flex-col md:flex-row gap-6">
            <!-- Sidebar (Filters) -->
            <div class="md:w-64 shrink-0">
              <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 sticky top-20">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                  <i class='bx bx-filter mr-2 text-[#193CB8] dark:text-blue-400'></i>
                  Filtros
                </h2>
                
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Oferta de trabajo</label>
                  <select 
                    v-model="selectedJobOffer"
                    @change="applyFilters"
                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8] dark:focus:ring-blue-400 focus:border-transparent text-gray-800 dark:text-gray-100"
                  >
                    <option value="all">Todas las ofertas</option>
                    <option v-for="offer in jobOffersList" :key="offer.id" :value="offer.id">
                      {{ offer.title }}
                    </option>
                  </select>
                </div>
                
                <!-- Filtro por estado -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado</label>
                  <div class="space-y-2">
                    <button 
                      v-for="filter in statusFilters" 
                      :key="filter.id"
                      @click="updateFilter(filter.id); applyFilters()"
                      :class="[
                        'w-full flex cursor-pointer items-center px-3 py-2 rounded-lg text-left transition-colors',
                        currentStatus === filter.id
                          ? `${filter.bgColor} ${filter.color}`
                          : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300'
                      ]"
                    >
                      <i :class="['bx mr-2', filter.icon]"></i>
                      {{ filter.name }}
                      <span v-if="filter.id === 'all'" class="ml-auto bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-300 text-xs rounded-full px-2 py-0.5">
                        {{ stats.total }}
                      </span>
                      <span v-else-if="filter.id === 'pending'"  class="ml-auto bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300 text-xs rounded-full px-2 py-0.5">
                        {{ stats.pending }}
                      </span>
                      <span v-else-if="filter.id === 'in_process'" class="ml-auto bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 text-xs rounded-full px-2 py-0.5">
                        {{ stats.in_process }}
                      </span>
                      <span v-else-if="filter.id === 'interview'" class="ml-auto bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300 text-xs rounded-full px-2 py-0.5">
                        {{ stats.interview }}
                      </span>
                      <span v-else-if="filter.id === 'accepted'" class="ml-auto bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 text-xs rounded-full px-2 py-0.5">
                        {{ stats.accepted }}
                      </span>
                      <span v-else-if="filter.id === 'rejected'" class="ml-auto bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 text-xs rounded-full px-2 py-0.5">
                        {{ stats.rejected }}
                      </span>
                    </button>
                  </div>
                </div>
                
                <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>
                
                <!-- Consejos -->
                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg border-l-4 border-[#193CB8] dark:border-blue-400">
                  <h3 class="font-semibold text-[#193CB8] dark:text-blue-200 mb-2">Consejo</h3>
                  <p class="text-sm text-gray-700 dark:text-gray-300">Responde rápidamente a los candidatos para mejorar la experiencia de reclutamiento.</p>
                </div>
              </div>
            </div>
            
            <!-- Main Feed -->
            <div class="flex-1">
              <!-- Candidates List -->
              <div class="space-y-6">
                <CandidateCard 
                  v-for="application in applicationsList" 
                  :key="application.id"
                  :application="application"
                  class="candidate-card"
                  @view="viewCandidate"
                  @delete="deleteCandidate"
                  @update-status="openStatusModal"
                  @message="messageCandidate"
                  @download-cv="downloadCV"
                />
                
                <!-- Loading Indicator -->
                <div v-if="isLoading" class="flex justify-center py-4">
                  <div class="animate-pulse flex space-x-2">
                    <div class="w-2 h-2 bg-[#193CB8] dark:bg-blue-400 rounded-full"></div>
                    <div class="w-2 h-2 bg-[#193CB8] dark:bg-blue-400 rounded-full animation-delay-200"></div>
                    <div class="w-2 h-2 bg-[#193CB8] dark:bg-blue-400 rounded-full animation-delay-400"></div>
                  </div>
                </div>
                
                <!-- End of Results -->
                <div v-if="pagination.currentPage >= pagination.lastPage && !isLoading && applicationsList.length > 0" class="text-center py-8">
                  <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-check-circle text-3xl text-[#193CB8] dark:text-blue-400'></i>
                  </div>
                  <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">¡Has visto todos los candidatos!</h3>
                  <p class="text-gray-500 dark:text-gray-400 mb-4">Vuelve más tarde para ver nuevas aplicaciones</p>
                  <button 
                    @click="scrollToTop"
                    class="text-[#193CB8] dark:text-blue-400 font-medium hover:underline flex items-center justify-center mx-auto"
                  >
                    <i class='bx bx-chevron-up mr-1'></i>
                    Volver arriba
                  </button>
                </div>
                
                <!-- No Results -->
                <div v-if="applicationsList.length === 0 && !isLoading" class="text-center py-8">
                  <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-user-x text-3xl text-[#193CB8] dark:text-blue-400'></i>
                  </div>
                  <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">No hay candidatos</h3>
                  <p class="text-gray-500 dark:text-gray-400 mb-4">Aún no has recibido aplicaciones o no hay resultados para los filtros seleccionados</p>
                  <button 
                    @click="router.get('/ofertas/crear')"
                    class="bg-[#193CB8] dark:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium hover:bg-[#142d8c] dark:hover:bg-blue-800 transition-colors"
                  >
                    Crear nueva oferta
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Scroll to Top Button -->
      <button 
        v-show="showScrollTopButton" 
        @click="scrollToTop"
        class="fixed bottom-6 right-6 w-12 h-12 rounded-full bg-[#193CB8] dark:bg-blue-700 text-white shadow-lg flex items-center justify-center hover:bg-[#142d8c] dark:hover:bg-blue-800 transition-all duration-300 z-50 animate-fade-in"
      >
        <i class='bx bx-chevron-up text-xl'></i>
      </button>
      
      <!-- Candidate Detail Modal -->
      <CandidateDetailModal 
        :is-open="showCandidateModal"
        :candidate="selectedCandidate"
        @close="closeCandidateModal"
        @update-status="openStatusModal"
        @message="messageCandidate"
        @view-profile="viewCandidate"
        @view-personal-profile="viewPersonalProfile"
      />
      
      <!-- Status Update Modal -->
      <StatusUpdateModal 
        :is-open="showStatusModal"
        :application="applicationToUpdate"
        @close="closeStatusModal"
        @update="updateApplicationStatus"
      />
    </div>
  </Layout>
</template>