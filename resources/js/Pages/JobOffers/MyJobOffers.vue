<script setup>
  import { ref, onMounted, computed } from 'vue'
  import { router } from '@inertiajs/vue3'
  import Layout from '@/Components/Layout.vue'
  import JobCard from '@/Pages/JobOffers/JobCard.vue'
  import MyJobCard from '@/Components/JobOffers/MyJobCard.vue'
  
  const props = defineProps({
    appliedOffers: {
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
    savedOffers: {
      type: Object,
      default: () => ({
        data: [],
        current_page: 1,
        last_page: 1,
        from: null,
        to: null,
        total: 0
      })
    }
  });
  
  // Tab state
  const activeTab = ref('applied');
  
  // Extract data from props
  const appliedJobsList = ref(props.appliedOffers.data || []);
  const savedJobsList = ref(props.savedOffers.data || []);
  
  const urlParams = new URLSearchParams(window.location.search);



  // Pagination for applied offers
  const appliedPagination = computed(() => ({
    currentPage: props.appliedOffers.current_page,
    lastPage: props.appliedOffers.last_page,
    from: props.appliedOffers.from,
    to: props.appliedOffers.to,
    total: props.appliedOffers.total || 0
  }));
  
  // Pagination for saved offers
  const savedPagination = computed(() => ({
    currentPage: props.savedOffers.current_page,
    lastPage: props.savedOffers.last_page,
    from: props.savedOffers.from,
    to: props.savedOffers.to,
    total: props.savedOffers.total || 0
  }));
  
  // Loading states
  const isLoadingApplied = ref(false);
  const isLoadingSaved = ref(false);
  
  // Show scroll top button
  const showScrollTopButton = ref(false);
  
  // Status filters
  const statusFilters = [
    { id: 'all', name: 'Todas', icon: 'bx-list-ul' },
    { id: 'pending', name: 'Pendientes', icon: 'bx-time' },
    { id: 'in_process', name: 'En proceso', icon: 'bx-loader' },
    { id: 'accepted', name: 'Aceptadas', icon: 'bx-check-circle' },
    { id: 'rejected', name: 'Rechazadas', icon: 'bx-x-circle' }
  ];
  
  // Active status filter
  const activeStatusFilter = ref('all');
  const initialTab = urlParams.get('tab') || 'applied';
  const initialStatus = urlParams.get('status') || 'all';
  activeTab.value = initialTab;
  activeStatusFilter.value = initialStatus;
  // Set active status filter
  const setStatusFilter = (status) => {
    activeStatusFilter.value = status;
    loadFilteredApplications();
  };
  
  // Load filtered applications
  const loadFilteredApplications = () => {
    router.get('/mis-ofertas', { 
      tab: activeTab.value,
      status: activeStatusFilter.value !== 'all' ? activeStatusFilter.value : undefined
    }, { 
      preserveScroll: true,
      preserveState: true,
      only: ['appliedOffers', 'savedOffers'],
      onSuccess: (page) => {
        if (activeTab.value === 'applied') {
          appliedJobsList.value = page.props.appliedOffers.data;
        } else {
          savedJobsList.value = page.props.savedOffers.data;
        }
      }
    });
  };
  
  // Load more applied offers
  const loadMoreApplied = async () => {
    if (isLoadingApplied.value || appliedPagination.value.currentPage >= appliedPagination.value.lastPage) return;
  
    isLoadingApplied.value = true;
  
    try {
      const nextPage = appliedPagination.value.currentPage + 1;
      await router.get(
        '/mis-ofertas', 
        { 
          tab: 'applied',
          page: nextPage,
          status: activeStatusFilter.value !== 'all' ? activeStatusFilter.value : undefined
        },
        { 
          preserveState: true,
          preserveScroll: true,
          only: ['appliedOffers'],
          onSuccess: (page) => {
            if (page.props.appliedOffers && page.props.appliedOffers.data) {
              appliedJobsList.value = [...appliedJobsList.value, ...page.props.appliedOffers.data];
            }
          }
        }
      );
    } catch (error) {
      console.error('Error al cargar más ofertas aplicadas:', error);
    } finally {
      isLoadingApplied.value = false;
    }
  };
  
  // Load more saved offers
  const loadMoreSaved = async () => {
    if (isLoadingSaved.value || savedPagination.value.currentPage >= savedPagination.value.lastPage) return;
  
    isLoadingSaved.value = true;
  
    try {
      const nextPage = savedPagination.value.currentPage + 1;
      await router.get(
        '/mis-ofertas', 
        { 
          tab: 'saved',
          page: nextPage
        },
        { 
          preserveState: true,
          preserveScroll: true,
          only: ['savedOffers'],
          onSuccess: (page) => {
            if (page.props.savedOffers && page.props.savedOffers.data) {
              savedJobsList.value = [...savedJobsList.value, ...page.props.savedOffers.data];
            }
          }
        }
      );
    } catch (error) {
      console.error('Error al cargar más ofertas guardadas:', error);
    } finally {
      isLoadingSaved.value = false;
    }
  };
  
  // Handle scroll
  const handleScroll = () => {
    const scrollPosition = window.scrollY + window.innerHeight;
    const pageHeight = document.documentElement.scrollHeight;
  
    // Show/hide scroll to top button
    showScrollTopButton.value = window.scrollY > 500;
  
    // Load more offers when near the bottom
    if (scrollPosition > pageHeight - 500) {
      if (activeTab.value === 'applied' && !isLoadingApplied.value && appliedPagination.value.currentPage < appliedPagination.value.lastPage) {
        loadMoreApplied();
      } else if (activeTab.value === 'saved' && !isLoadingSaved.value && savedPagination.value.currentPage < savedPagination.value.lastPage) {
        loadMoreSaved();
      }
    }
  };
  
  // Scroll to top
  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  };
  
  // View job offer
  const viewJobOffer = (jobId) => {
    router.get(`/ofertas/${jobId}`);
  };
  
  // Apply to job
  const applyToJob = (jobId) => {
    router.get(`/ofertas/${jobId}/aplicar`);
  };
  
  // Toggle save job
  const toggleSaveJob = (jobId) => {
    router.post(route('job-offers.toggle-save'), {
      job_offer_id: jobId
    }, {
      preserveScroll: true,
      only: ['savedOffers']
    });
  };
  
  // Share job
  const shareJob = (jobId) => {
    if (navigator.share) {
      navigator.share({
        title: 'Oferta de trabajo',
        text: 'Mira esta oferta de trabajo',
        url: `${window.location.origin}/ofertas/${jobId}`,
      });
    } else {
      // Fallback for browsers that don't support Web Share API
      alert(`Compartiendo oferta ${jobId}`);
    }
  };
  
  // Cancel job application
  const cancelApplication = (applicationId) => {
    if (confirm('¿Estás seguro de que deseas cancelar esta solicitud?')) {
      router.patch(route('job-applications.cancel', applicationId), {}, {
        preserveScroll: true,
        only: ['appliedOffers']
      });
    }
  };
  
  // Initialize
  onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    
    // Clean up on unmount
    return () => {
      window.removeEventListener('scroll', handleScroll);
    };
  });
  </script>
  
  <template>
    <Layout>
      <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col">
        <!-- Header Section -->
        <div class="bg-gradient-to-r bg-[#193CB8] dark:bg-[#142d8c] text-white py-8">
          <div class="max-w-6xl mx-auto px-4">
            <div class="flex flex-col items-start">
              <h1 class="text-3xl font-bold mb-2">Mis Ofertas</h1>
              <p class="text-blue-100 mb-6">Gestiona tus solicitudes y ofertas guardadas</p>

              <!-- Tabs -->
              <div class="flex space-x-2 bg-white/10 backdrop-blur-sm p-1 rounded-lg border border-white/20">
                <button 
                  @click="activeTab = 'applied'; activeStatusFilter = 'all'"
                  :class="[
                    'px-4 py-2 rounded-md cursor-pointer font-medium transition-colors flex items-center',
                    activeTab === 'applied' 
                      ? 'bg-white text-[#193CB8]' 
                      : 'text-white hover:bg-white/20'
                  ]"
                >
                  <i class='bx bx-briefcase mr-2'></i>
                  Aplicadas <span class="ml-2 bg-[#193CB8]/20 px-2 py-0.5 rounded-full text-sm">{{ appliedPagination.total }}</span>
                </button>
                <button 
                  @click="activeTab = 'saved'; activeStatusFilter = 'all'"
                  :class="[
                    'px-4 py-2 rounded-md cursor-pointer font-medium transition-colors flex items-center',
                    activeTab === 'saved' 
                      ? 'bg-white text-[#193CB8]' 
                      : 'text-white hover:bg-white/20'
                  ]"
                >
                  <i class='bx bx-bookmark mr-2'></i>
                  Guardadas <span class="ml-2 bg-[#193CB8]/20 px-2 py-0.5 rounded-full text-sm">{{ savedPagination.total }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 py-6">
          <div class="max-w-6xl mx-auto px-4">
            <div class="flex flex-col md:flex-row gap-6">
              <!-- Sidebar (Status Filters) -->
              <div v-if="activeTab === 'applied'" class="md:w-64 shrink-0">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 sticky top-20">
                  <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                    <i class='bx bx-filter mr-2 text-[#193CB8] dark:text-blue-200'></i>
                    Estado
                  </h2>

                  <div class="space-y-2">
                    <button 
                      v-for="filter in statusFilters" 
                      :key="filter.id"
                      @click="setStatusFilter(filter.id)"
                      :class="[
                        'w-full flex cursor-pointer items-center px-3 py-2 rounded-lg text-left transition-colors',
                        activeStatusFilter === filter.id
                          ? 'bg-[#193CB8]/10 dark:bg-blue-900 text-[#193CB8] dark:text-blue-200'
                          : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300'
                      ]"
                    >
                      <i :class="['bx mr-2', filter.icon]"></i>
                      {{ filter.name }}
                    </button>
                  </div>

                  <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>

                  <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg border-l-4 border-[#193CB8] dark:border-blue-200">
                    <h3 class="font-semibold text-[#193CB8] dark:text-blue-200 mb-2">Consejo</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">Mantén actualizado tu perfil para aumentar tus posibilidades de ser seleccionado.</p>
                  </div>
                </div>
              </div>

              <!-- Main Feed -->
              <div class="flex-1">
                <!-- Applied Jobs Tab -->
                <div v-if="activeTab === 'applied'" class="space-y-6">
                  <div v-if="appliedJobsList.length === 0 && !isLoadingApplied" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                      <i class='bx bx-briefcase text-3xl text-[#193CB8] dark:text-blue-200'></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">No has aplicado a ninguna oferta</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Explora las ofertas disponibles y aplica a las que más te interesen</p>
                    <button 
                      @click="router.get('/ofertas')"
                      class="bg-[#193CB8] dark:bg-blue-200 text-white dark:text-gray-900 px-4 py-2 rounded-lg cursor-pointer font-medium hover:bg-[#142d8c] dark:hover:bg-blue-300 transition-colors"
                    >
                      Ver ofertas disponibles
                    </button>
                  </div>

                  <!-- Usando el nuevo componente MyJobCard para aplicaciones -->
                  <div v-for="application in appliedJobsList" :key="application.id" class="job-card">
                    <MyJobCard 
                      :jobOffer="application.job_offer"
                      :application="application"
                      :isSaved="false"
                      @view="viewJobOffer"
                      @apply="applyToJob"
                      @save="toggleSaveJob"
                      @share="shareJob"
                      @cancel="cancelApplication"
                    />
                  </div>

                  <!-- Loading Indicator -->
                  <div v-if="isLoadingApplied" class="flex justify-center py-4">
                    <div class="animate-pulse flex space-x-2">
                      <div class="w-2 h-2 bg-[#193CB8] dark:bg-blue-200 rounded-full"></div>
                      <div class="w-2 h-2 bg-[#193CB8] dark:bg-blue-200 rounded-full animation-delay-200"></div>
                      <div class="w-2 h-2 bg-[#193CB8] dark:bg-blue-200 rounded-full animation-delay-400"></div>
                    </div>
                  </div>

                  <!-- End of Results -->
                  <div v-if="appliedPagination.currentPage >= appliedPagination.lastPage && !isLoadingApplied && appliedJobsList.length > 0" class="text-center py-6">
                    <p class="text-gray-500 dark:text-gray-400">No hay más solicitudes para mostrar</p>
                  </div>
                </div>

                <!-- Saved Jobs Tab -->
                <div v-if="activeTab === 'saved'" class="space-y-6">
                  <div v-if="savedJobsList.length === 0 && !isLoadingSaved" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                      <i class='bx bx-bookmark text-3xl text-[#193CB8] dark:text-blue-200'></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">No tienes ofertas guardadas</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Guarda las ofertas que te interesen para revisarlas más tarde</p>
                    <button 
                      @click="router.get('/ofertas')"
                      class="bg-[#193CB8] dark:bg-blue-200 text-white dark:text-gray-900 px-4 py-2 rounded-lg font-medium hover:bg-[#142d8c] dark:hover:bg-blue-300 transition-colors"
                    >
                      Ver ofertas disponibles
                    </button>
                  </div>

                  <div v-for="job in savedJobsList" :key="job.id" class="job-card">
                    <JobCard 
                      :jobOffer="job"
                      :isSaved="true"
                      @view="viewJobOffer"
                      @apply="applyToJob"
                      @save="toggleSaveJob"
                      @share="shareJob"
                    />
                  </div>

                  <!-- Loading Indicator -->
                  <div v-if="isLoadingSaved" class="flex justify-center py-4">
                    <div class="animate-pulse flex space-x-2">
                      <div class="w-2 h-2 bg-[#193CB8] dark:bg-blue-200 rounded-full"></div>
                      <div class="w-2 h-2 bg-[#193CB8] dark:bg-blue-200 rounded-full animation-delay-200"></div>
                      <div class="w-2 h-2 bg-[#193CB8] dark:bg-blue-200 rounded-full animation-delay-400"></div>
                    </div>
                  </div>

                  <!-- End of Results -->
                  <div v-if="savedPagination.currentPage >= savedPagination.lastPage && !isLoadingSaved && savedJobsList.length > 0" class="text-center py-6">
                    <p class="text-gray-500 dark:text-gray-400">No hay más ofertas guardadas para mostrar</p>
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
          class="fixed bottom-6 right-6 w-12 h-12 rounded-full bg-[#193CB8] dark:bg-blue-200 text-white dark:text-gray-900 shadow-lg flex items-center justify-center hover:bg-[#142d8c] dark:hover:bg-blue-300 transition-all duration-300 z-50 animate-fade-in"
        >
          <i class='bx bx-chevron-up text-xl'></i>
        </button>
      </div>
    </Layout>
  </template>
  
  <style scoped>
  /* Animaciones para hacer la experiencia más adictiva */
  .job-card {
    transform: translateY(0);
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
  }
  
  .job-card:hover {
    transform: translateY(-3px);
  }
  
  /* Animación para el botón de scroll to top */
  @keyframes fade-in {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .animate-fade-in {
    animation: fade-in 0.3s ease-out;
  }
  
  /* Animación para los elementos que aparecen al hacer scroll */
  @keyframes slide-up {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .job-card {
    animation: slide-up 0.5s ease-out;
  }
  
  /* Delays para animaciones */
  .animation-delay-200 {
    animation-delay: 0.2s;
  }
  
  .animation-delay-400 {
    animation-delay: 0.4s;
  }
  </style>