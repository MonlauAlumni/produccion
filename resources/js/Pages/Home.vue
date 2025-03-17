<script setup>
  import { ref, onMounted, computed, watch } from 'vue'
  import { router } from '@inertiajs/vue3'
  import Layout from '@/Components/Layout.vue'
  import JobCard from '@/Pages/JobOffers/JobCard.vue'
  import JobConfirmationModal from '@/Components/JobOffers/JobConfirmationModal.vue'

  const props = defineProps({
    jobOffers: {
      type: Object,
      required: true
    }
  });

  const isJobConfirmationModalOpen = ref(false);  

  const openJobConfirmationModal = (jobId) => {
    isJobConfirmationModalOpen.value = true;
  };

  const closeJobConfirmationModal = () => {
    isJobConfirmationModalOpen.value = false;
  };

  // Extraer los datos de las ofertas de trabajo
  const jobOffersList = ref(props.jobOffers.data || []);
  const pagination = computed(() => ({
    currentPage: props.jobOffers.current_page,
    lastPage: props.jobOffers.last_page,
    from: props.jobOffers.from,
    to: props.jobOffers.to,
    total: props.jobOffers.total
  }));

  const isLoading = ref(false);
  const showScrollTopButton = ref(false);
  const showFilters = ref(false);
  const searchQuery = ref('');
  
  // Estado para interacciones
  const savedJobs = ref(new Set());
  const likedJobs = ref(new Set());
  const viewedJobs = ref(new Set());
  
  // Estado para filtros
  const activeFilter = ref('all');
  const activeCategory = ref('all');
  const activeJobType = ref('all');
  const activeSalaryRange = ref('all');
  
  // Categorías destacadas
  const featuredCategories = [
    { id: 'all', name: 'Todas', icon: 'bx-globe', color: '#193CB8' },
    { id: 'tech', name: 'Tecnología', icon: 'bx-code-alt', color: '#4285F4' },
    { id: 'marketing', name: 'Marketing', icon: 'bx-line-chart', color: '#EA4335' },
    { id: 'automotion', name: 'Automoción', icon: 'bx-car', color: '#FF7C43' },

  ];
  
  // Tipos de trabajo
  const jobTypes = [
    { id: 'all', name: 'Todos' },
    { id: 'remote', name: 'Remoto' },
    { id: 'onsite', name: 'Presencial' },
    { id: 'hybrid', name: 'Híbrido' }
  ];
  
  // Rangos salariales
  const salaryRanges = [
    { id: 'all', name: 'Todos' },
    { id: 'under30k', name: 'Menos de 30.000€' },
    { id: '30k-50k', name: '30.000€ - 50.000€' },
    { id: 'over50k', name: 'Más de 50.000€' }
  ];
  
  // Estadísticas del mercado (datos de ejemplo)
  const marketStats = {
    topRoles: [
      { name: 'Desarrollador Full Stack', trend: 24 },
      { name: 'UX/UI Designer', trend: 18 },
      { name: 'Data Scientist', trend: 32 }
    ],
    topSkills: [
      { name: 'React & TypeScript', trend: 32 },
      { name: 'Python', trend: 28 },
      { name: 'AWS', trend: 22 }
    ],
    averageSalary: {
      value: 42500,
      trend: 5
    }
  };
  
  // Cargar más ofertas (paginación)
  const loadMoreJobs = async () => {
    if (isLoading.value || pagination.value.currentPage >= pagination.value.lastPage) return;

    isLoading.value = true;

    try {
      // Usar Inertia para cargar la siguiente página
      const nextPage = pagination.value.currentPage + 1;
      await router.get(
        '/ofertas', 
        { 
          page: nextPage,
          filter: activeFilter.value !== 'all' ? activeFilter.value : undefined,
          category: activeCategory.value !== 'all' ? activeCategory.value : undefined,
          job_type: activeJobType.value !== 'all' ? activeJobType.value : undefined,
          salary_range: activeSalaryRange.value !== 'all' ? activeSalaryRange.value : undefined,
          search: searchQuery.value || undefined
        },
        { 
          preserveState: true,
          preserveScroll: true,
          only: ['jobOffers'],
          onSuccess: (page) => {
            // Añadir las nuevas ofertas a la lista existente
            if (page.props.jobOffers && page.props.jobOffers.data) {
              jobOffersList.value = [...jobOffersList.value, ...page.props.jobOffers.data];
            }
          }
        }
      );
    } catch (error) {
      console.error('Error al cargar más ofertas:', error);
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

    // Cargar más ofertas cuando estamos cerca del final
    if (scrollPosition > pageHeight - 500 && !isLoading.value && pagination.value.currentPage < pagination.value.lastPage) {
      loadMoreJobs();
    }
  };

  // Guardar oferta
  const toggleSaveJob = (jobId) => {
    if (savedJobs.value.has(jobId)) {
      savedJobs.value.delete(jobId);
    } else {
      savedJobs.value.add(jobId);
      // Mostrar notificación o animación
    }
  };

  // Like a una oferta
  const toggleLikeJob = (jobId) => {
    if (likedJobs.value.has(jobId)) {
      likedJobs.value.delete(jobId);
    } else {
      likedJobs.value.add(jobId);
    }
  };

  // Ver detalle de oferta
  const viewJobOffer = (jobId) => {
    viewedJobs.value.add(jobId);
    router.get(`/ofertas/${jobId}`);
  };

  // Aplicar a oferta
  const applyToJob = (jobId) => {
    viewedJobs.value.add(jobId);
    router.get(`/ofertas/${jobId}/aplicar`);
  };

  // Compartir oferta
  const shareJob = (jobId) => {
    // Implementación de compartir (podría usar la API Web Share si está disponible)
    if (navigator.share) {
      navigator.share({
        title: 'Oferta de trabajo',
        text: 'Mira esta oferta de trabajo',
        url: `${window.location.origin}/ofertas/${jobId}`,
      });
    } else {
      // Fallback para navegadores que no soportan Web Share API
      alert(`Compartiendo oferta ${jobId}`);
    }
  };

  // Volver al inicio de la página
  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  };

  // Cambiar filtro activo
  const setFilter = (filter) => {
    activeFilter.value = filter;
    applyFilters();
  };
  
  // Cambiar categoría activa
  const setCategory = (category) => {
    activeCategory.value = category;
    applyFilters();
  };
  
  // Cambiar tipo de trabajo activo
  const setJobType = (jobType) => {
    activeJobType.value = jobType;
    applyFilters();
  };
  
  // Cambiar rango salarial activo
  const setSalaryRange = (range) => {
    activeSalaryRange.value = range;
    applyFilters();
  };
  
  // Aplicar filtros
  const applyFilters = () => {
    // Navegar a la ruta con los filtros aplicados
    router.get('/ofertas', { 
      filter: activeFilter.value !== 'all' ? activeFilter.value : undefined,
      category: activeCategory.value !== 'all' ? activeCategory.value : undefined,
      job_type: activeJobType.value !== 'all' ? activeJobType.value : undefined,
      salary_range: activeSalaryRange.value !== 'all' ? activeSalaryRange.value : undefined,
      search: searchQuery.value || undefined
    }, { 
      preserveScroll: true 
    });
  };
  
  // Buscar ofertas
  const searchJobs = () => {
    applyFilters();
  };
  
  // Formatear número
  const formatNumber = (number) => {
    return number.toLocaleString();
  };

  // Cambiar categoría activa


  // Cambiar tipo de trabajo activo
 
  // Aplicar filtros
  

  // Buscar ofertas
 

  // Inicializar
  onMounted(() => {
    window.addEventListener('scroll', handleScroll);

    // Limpiar al desmontar
    return () => {
      window.removeEventListener('scroll', handleScroll);
    };
  });
  
  // Observar cambios en la búsqueda
  watch(searchQuery, (newValue, oldValue) => {
    if (newValue === '' && oldValue !== '') {
      // Si se borra la búsqueda, aplicar filtros
      applyFilters();
    }
  });
  </script>
  
  <template>
    <Layout>
      <div class="min-h-screen bg-gray-50 flex flex-col">
        <!-- Hero Section -->
        <div class="bg-[#193CB8] text-white py-8">
          <div class="max-w-6xl mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center mx-auto justify-between">
              <div class="mb-6 md:mb-0 md:mr-8">
                <h1 class="text-3xl font-bold mb-2">Encuentra tu próxima oportunidad</h1>
                <p class="text-blue-100 mb-4">Explora las mejores ofertas de trabajo adaptadas a tu perfil</p>
                
                <!-- Search Bar -->
                <div class="relative max-w-xl">
                  <div class="flex">
                    <input 
                      v-model="searchQuery"
                      type="text" 
                      placeholder="Buscar por título, empresa o ubicación..." 
                      class="w-full px-4 py-3 rounded-l-lg text-gray-800 focus:outline-none focus:ring-2 text-white placeholder-gray-200 focus:ring-blue-300 border-1 border-white"
                      @keyup.enter="searchJobs"
                    />
                    <button 
                      @click="searchJobs"
                      class="bg-[#193CB8] hover:bg-[#142d8c] px-4 py-3 rounded-r-lg border-1 border-white flex items-center justify-center transition-colors"
                    >
                      <i class='bx bx-search text-xl'></i>
                    </button>
                  </div>
                  
                  <button 
                    @click="showFilters = !showFilters"
                    class="absolute right-16 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                  >
                  </button>
                </div>
              </div>
              
              <!-- Stats -->
              <div class="bg-white/10 backdrop-blur-sm p-4 rounded-lg border border-white/20 text-center">
                <div class="text-3xl font-bold">{{ formatNumber(pagination.total || 0) }}</div>
                <div class="text-blue-100">Ofertas disponibles</div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 py-6">
          <div class="max-w-6xl mx-auto px-4">
            <div class="flex flex-col md:flex-row gap-6">
              <!-- Main Feed -->
              <div class="flex-1">
                <!-- Feed Items -->
                <div class="space-y-6">
                    <JobCard 
                    v-for="job in jobOffersList.slice().reverse()" 
                    :key="job.id"
                    :jobOffer="job"
                    :isLiked="likedJobs.has(job.id)"
                    :isSaved="savedJobs.has(job.id)"
                    class="job-card"
                    @view="viewJobOffer"
                    @apply="applyToJob"
                    @save="toggleSaveJob"
                    @like="toggleLikeJob"
                    @share="shareJob"
                    />
                  
                  <!-- Loading Indicator -->
                  <div v-if="isLoading" class="flex justify-center py-4">
                    <div class="animate-pulse flex space-x-2">
                      <div class="w-2 h-2 bg-[#193CB8] rounded-full"></div>
                      <div class="w-2 h-2 bg-[#193CB8] rounded-full animation-delay-200"></div>
                      <div class="w-2 h-2 bg-[#193CB8] rounded-full animation-delay-400"></div>
                    </div>
                  </div>
                  
                  <!-- End of Results -->
                  <div v-if="pagination.currentPage >= pagination.lastPage && !isLoading && jobOffersList.length > 0" class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                      <i class='bx bx-check-circle text-3xl text-[#193CB8]'></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800 mb-2">¡Has visto todas las ofertas!</h3>
                    <p class="text-gray-500 mb-4">Vuelve más tarde para descubrir nuevas oportunidades</p>
                    <button 
                      @click="scrollToTop"
                      class="text-[#193CB8] font-medium hover:underline flex items-center justify-center mx-auto"
                    >
                      <i class='bx bx-chevron-up mr-1'></i>
                      Volver arriba
                    </button>
                  </div>
                  
                  <!-- No Results -->
                  <div v-if="jobOffersList.length === 0 && !isLoading" class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                      <i class='bx bx-search-alt text-3xl text-[#193CB8]'></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800 mb-2">No se encontraron ofertas</h3>
                    <p class="text-gray-500 mb-4">Prueba con otros filtros o vuelve más tarde</p>
                  </div>
                </div>
              </div>
              
              <!-- Sidebar (Desktop) -->
              <div class="hidden md:block w-80 shrink-0">
                <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200 sticky top-20">
                  <h2 class="text-lg font-bold text-gray-800 mb-3">Categorías</h2>
                  
                  <div class="space-y-2">
                    <button 
                      v-for="category in featuredCategories" 
                      :key="category.id"
                      @click="setCategory(category.id)"
                      :class="[
                        'w-full flex cursor-pointer items-center px-3 py-2 rounded-lg text-left transition-colors',
                        activeCategory === category.id
                          ? 'bg-[#193CB8]/10 text-[#193CB8]'
                          : 'hover:bg-gray-100 text-gray-700'
                      ]"
                    >
                      <i :class="['bx mr-2', category.icon]" :style="{ color: category.color }"></i>
                      {{ category.name }}
                    </button>
                  </div>
                  
                  <div class="border-t border-gray-200 my-4"></div>
                  
                  <h2 class="text-lg font-bold text-gray-800 mb-3">Tipo de trabajo</h2>
                  
                  <div class="space-y-2">
                    <button 
                      v-for="type in jobTypes" 
                      :key="type.id"
                      @click="setJobType(type.id)"
                      :class="[
                        'w-full flex cursor-pointer items-center px-3 py-2 rounded-lg text-left transition-colors',
                        activeJobType === type.id
                          ? 'bg-[#193CB8]/10 text-[#193CB8]'
                          : 'hover:bg-gray-100 text-gray-700'
                      ]"
                    >
                      {{ type.name }}
                    </button>
                  </div>
                  
                  <div class="border-t border-gray-200 my-4"></div>
                  
                  <h2 class="text-lg font-bold text-gray-800 mb-3">Rango salarial</h2>
                  
                  <div class="space-y-2">
                    <button 
                      v-for="range in salaryRanges" 
                      :key="range.id"
                      @click="setSalaryRange(range.id)"
                      :class="[
                        'w-full flex cursor-pointer items-center px-3 py-2 rounded-lg text-left transition-colors',
                        activeSalaryRange === range.id
                          ? 'bg-[#193CB8]/10 text-[#193CB8]'
                          : 'hover:bg-gray-100 text-gray-700'
                      ]"
                    >
                      {{ range.name }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Scroll to Top Button -->
        <button v-show="showScrollTopButton" @click="scrollToTop"
          class="fixed bottom-6 right-6 w-12 h-12 rounded-full bg-[#193CB8] text-white shadow-lg flex items-center justify-center hover:bg-[#142d8c] transition-all duration-300 z-50 animate-fade-in">
          <i class='bx bx-chevron-up text-xl'></i>
        </button>
  
        <!-- Floating Action Button (Mobile) -->
        <div class="md:hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50">
          <button @click="router.get('/ofertas/crear')"
            class="bg-[#193CB8] text-white px-6 py-3 rounded-full shadow-lg flex items-center justify-center hover:bg-[#142d8c] transition-all duration-300">
            <i class='bx bx-plus-circle mr-2'></i>
            Publicar oferta
          </button>
        </div>
      </div>
    </Layout>
  </template>

<style>
  /* Estilos básicos */
  .prose p {
    margin-bottom: 1rem;
    line-height: 1.7;
  }

  /* Ocultar scrollbar pero mantener funcionalidad */
  .hide-scrollbar {
    -ms-overflow-style: none;
    /* IE and Edge */
    scrollbar-width: none;
    /* Firefox */
  }

  .hide-scrollbar::-webkit-scrollbar {
    display: none;
    /* Chrome, Safari and Opera */
  }

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
