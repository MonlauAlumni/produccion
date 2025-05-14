<script setup>
  import { ref, onMounted, computed, watch } from 'vue'
  import { router, usePage } from '@inertiajs/vue3'
  import Layout from '@/Components/Layout.vue'
  import JobCard from '@/Pages/JobOffers/JobCard.vue'
  import JobConfirmationModal from '@/Components/JobOffers/JobConfirmationModal.vue'

  const props = defineProps({
    jobOffers: {
      type: Object,
      required: true
    }
  });

  const page = usePage();

  // Default filtering by user's training_area
  const userTrainingArea = computed(() => page.props.auth.user?.training_area ?? null);
  const normalizeText = (text) => {
    if (!text) return '';
    return text.toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "");
  };
  const areaToCategoryMap = {
    informatica: 'it',
    marketing: 'marketing',
    automocion: 'automotive'
  };

  // Recuperar los filtros y página de la URL al cargar la página
  const urlParams = new URLSearchParams(window.location.search);

  // Initialize with default values to ensure hooks are always called
  const initialActiveCategoryValue = urlParams.has('categoria')
    ? Array.isArray(urlParams.getAll('categoria'))
      ? urlParams.getAll('categoria')
      : [urlParams.get('categoria')]
    : [];

  const initialActiveJobTypeValue = urlParams.has('trabajo')
    ? Array.isArray(urlParams.getAll('trabajo'))
      ? urlParams.getAll('trabajo')
      : [urlParams.get('trabajo')]
    : [];

  const initialSearchQueryValue = urlParams.get('search') || '';

  // Apply default filter based on training_area if no category in URL
  if (initialActiveCategoryValue.length === 0 && userTrainingArea.value) {
    const normArea = normalizeText(userTrainingArea.value);
    const mappedCategory = areaToCategoryMap[normArea];
    if (mappedCategory) {
      initialActiveCategoryValue.push(mappedCategory);
    }
  }

  const activeFilter = ref('all');
  const activeCategory = ref(initialActiveCategoryValue);
  const activeJobType = ref(initialActiveJobTypeValue);
  const searchQuery = ref(initialSearchQueryValue);

  const isJobConfirmationModalOpen = ref(false);
  const selectedJobId = ref(null);

  // Nueva variable para controlar la visibilidad del drawer de filtros en móvil
  const isFilterDrawerOpen = ref(false);

  const getJobOffer = (id) => {
    return jobOffersList.value.find(job => job.id === id);
  };
  
  const openJobConfirmationModal = (jobId) => {
    selectedJobId.value = jobId;
    isJobConfirmationModalOpen.value = true;
  };

  const closeJobConfirmationModal = () => {
    isJobConfirmationModalOpen.value = false;
    selectedJobId.value = null;
  };

  // Extraer los datos de las ofertas de trabajo
  const jobOffersList = ref(props.jobOffers.data || []);
  const pagination = computed(() => ({
    currentPage: props.jobOffers.current_page,
    lastPage: props.jobOffers.last_page,
    from: props.jobOffers.from,
    to: props.jobOffers.to,
    total: props.jobOffers.total,
    links: props.jobOffers.links || []
  }));

  const isLoading = ref(false);
  const showScrollTopButton = ref(false);
  const showFilters = ref(false);
  
  // Inicializar searchQuery desde la URL si existe
  //const searchQuery = ref(urlParams.get('search') || '');

  // Estado para interacciones
  const savedJobs = ref(new Set());
  const likedJobs = ref(new Set());
  const viewedJobs = ref(new Set());

  // Categorías destacadas
  const featuredCategories = [
    { id: 'all', name: 'Todas', icon: 'bx-globe', color: '#193CB8' },
    { id: 'it', name: 'Tecnología', icon: 'bx-code-alt', color: '#4285F4' },
    { id: 'marketing', name: 'Marketing', icon: 'bx-line-chart', color: '#EA4335' },
    { id: 'automotive', name: 'Automoción', icon: 'bx-car', color: '#FF7C43' },
  ];

  // Tipos de trabajo
  const jobTypes = [
    { id: 'all', name: 'Todos' },
    { id: 'remote', name: 'Remoto' },
    { id: 'onsite', name: 'Presencial' },
    { id: 'hybrid', name: 'Híbrido' }
  ];

  // Cambiar de página
  const goToPage = (pageNumber) => {
    if (pageNumber < 1 || pageNumber > pagination.value.lastPage || pageNumber === pagination.value.currentPage) {
      return;
    }
    
    isLoading.value = true;
    
    // Construir los parámetros de filtro
    const params = {
      page: pageNumber,
      categoria: activeCategory.value.length ? activeCategory.value : undefined,
      trabajo: activeJobType.value.length ? activeJobType.value : undefined,
      search: searchQuery.value || undefined
    };
    
    // Navegar a la página solicitada
    router.get('/home', params, {
      preserveScroll: true,
      preserveState: true,
      only: ['jobOffers'],
      onSuccess: (page) => {
        // Actualizar manualmente los datos de jobOffers
        if (page.props.jobOffers) {
          jobOffersList.value = page.props.jobOffers.data || [];
          scrollToTop();
        }
        isLoading.value = false;
      },
      onError: () => {
        isLoading.value = false;
      }
    });
  };

  const toggleSaveJob = (jobId) => {
    router.post(`ofertas/${jobId}/guardar`), {
      onSuccess: () => {
        if (savedJobs.value.has(jobId)) {
          savedJobs.value.delete(jobId);
        } else {
          savedJobs.value.add(jobId);
        }
        console.log(savedJobs.value);
      },
    };
  };

  // Ver detalle de oferta
  const viewJobOffer = (jobId) => {
    viewedJobs.value.add(jobId);
    router.get(`/ofertas/${jobId}`);
  };

  // Aplicar a oferta
  const applyToJob = (jobId) => {
    viewedJobs.value.add(jobId);
    openJobConfirmationModal(jobId);
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
  const toggleCategory = (category) => {
    if (category === 'all') {
      activeCategory.value = [];
    } else if (activeCategory.value.includes(category)) {
      activeCategory.value = activeCategory.value.filter((c) => c !== category);
    } else {
      activeCategory.value.push(category);
    }
    applyFilters();
    // Cerrar el drawer en móvil después de aplicar un filtro
    if (window.innerWidth < 768) {
      isFilterDrawerOpen.value = false;
    }
  };

  // Cambiar tipo de trabajo activo
  const toggleJobType = (jobType) => {
    if (jobType === 'all') {
      activeJobType.value = [];
    } else if (activeJobType.value.includes(jobType)) {
      activeJobType.value = activeJobType.value.filter((t) => t !== jobType);
    } else {
      activeJobType.value.push(jobType);
    }
    applyFilters();
    // Cerrar el drawer en móvil después de aplicar un filtro
    if (window.innerWidth < 768) {
      isFilterDrawerOpen.value = false;
    }
  };

  // Limpiar todos los filtros
  const clearAllFilters = () => {
    activeCategory.value = [];
    activeJobType.value = [];
    searchQuery.value = '';
    applyFilters();
  };

  // Aplicar filtros - SOLUCIÓN MEJORADA CON BÚSQUEDA
  const applyFilters = () => {
    isLoading.value = true;
    
    const params = {
      page: 1 // Siempre volver a la primera página al aplicar filtros
    };

    // Agregar siempre los filtros actuales, aunque ya estuvieran
    if (activeCategory.value.length > 0) {
      params.categoria = activeCategory.value;
    }

    if (activeJobType.value.length > 0) {
      params.trabajo = activeJobType.value;
    }

    if (searchQuery.value) {
      params.search = searchQuery.value;
    }

    // Usar only para actualizar solo los datos de jobOffers
    router.get('/home', params, {
      preserveScroll: true,
      preserveState: true,
      only: ['jobOffers'],
      onSuccess: (page) => {
        // Actualizar manualmente los datos de jobOffers
        if (page.props.jobOffers) {
          jobOffersList.value = page.props.jobOffers.data || [];
        }
        isLoading.value = false;
      },
      onError: () => {
        isLoading.value = false;
      }
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

  // Generar array de páginas para la paginación
  const paginationPages = computed(() => {
    const currentPage = pagination.value.currentPage;
    const lastPage = pagination.value.lastPage;
    
    if (lastPage <= 7) {
      // Si hay 7 o menos páginas, mostrar todas
      return Array.from({ length: lastPage }, (_, i) => i + 1);
    }
    
    // Siempre mostrar la primera y última página
    const pages = [1];
    
    // Determinar el rango de páginas a mostrar alrededor de la página actual
    let startPage = Math.max(2, currentPage - 2);
    let endPage = Math.min(lastPage - 1, currentPage + 2);
    
    // Ajustar para mostrar siempre 5 páginas en el medio si es posible
    if (endPage - startPage < 4) {
      if (startPage === 2) {
        endPage = Math.min(lastPage - 1, startPage + 4);
      } else if (endPage === lastPage - 1) {
        startPage = Math.max(2, endPage - 4);
      }
    }
    
    // Añadir elipsis si es necesario
    if (startPage > 2) {
      pages.push('...');
    }
    
    // Añadir páginas del rango
    for (let i = startPage; i <= endPage; i++) {
      pages.push(i);
    }
    
    // Añadir elipsis si es necesario
    if (endPage < lastPage - 1) {
      pages.push('...');
    }
    
    // Añadir última página
    if (lastPage > 1) {
      pages.push(lastPage);
    }
    
    return pages;
  });

  // Función para abrir/cerrar el drawer de filtros
  const toggleFilterDrawer = () => {
    isFilterDrawerOpen.value = !isFilterDrawerOpen.value;
    
    // Si se abre el drawer, prevenir el scroll del body
    if (isFilterDrawerOpen.value) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
  };

  // Inicializar
  onMounted(() => {
    // Mostrar/ocultar botón para volver arriba al hacer scroll
    window.addEventListener('scroll', () => {
      showScrollTopButton.value = window.scrollY > 500;
    });

    // Si hay filtros en la URL, aplicarlos
    if (activeCategory.value.length > 0 || activeJobType.value.length > 0 || searchQuery.value) {
      applyFilters();
    }

    // Cerrar el drawer si se redimensiona la ventana a desktop
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 768 && isFilterDrawerOpen.value) {
        isFilterDrawerOpen.value = false;
        document.body.style.overflow = '';
      }
    });

    // Limpiar al desmontar
    return () => {
      window.removeEventListener('scroll', () => {});
      window.removeEventListener('resize', () => {});
      document.body.style.overflow = '';
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
                  <input v-model="searchQuery" type="text" placeholder="Buscar por título, empresa o ubicación..."
                    class="w-full px-4 py-3 rounded-l-lg text-gray-700 bg-white focus:outline-none focus:ring-2 placeholder-gray-500 focus:ring-blue-300 border-1 border-white"
                    @keyup.enter="searchJobs" />
                  <button @click="searchJobs"
                    class="bg-white hover:bg-gray-100 px-4 py-3 rounded-r-lg border-1 border-white flex items-center justify-center transition-colors text-[#193CB8]">
                    <i class='bx bx-search text-xl'></i>
                  </button>
                </div>

                <!-- Botón para mostrar filtros en móvil -->
                <button 
                  @click="toggleFilterDrawer"
                  class="md:hidden absolute right-16 top-1/2 transform -translate-y-1/2 text-gray-700 bg-white hover:bg-gray-100 p-2 rounded-full"
                  aria-label="Mostrar filtros"
                >
                  <i class='bx bx-filter text-xl'></i>
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
              <!-- Filtros activos -->
              <div v-if="activeCategory.length > 0 || activeJobType.length > 0 || searchQuery" class="mb-4 flex flex-wrap gap-2">
                <div class="text-sm font-medium text-gray-700 mr-2 flex items-center">Filtros activos:</div>
                
                <div v-for="cat in activeCategory" :key="`cat-${cat}`" 
                     class="bg-[#193CB8]/10 text-[#193CB8] px-3 py-1 rounded-full text-sm flex items-center">
                  {{ featuredCategories.find(c => c.id === cat)?.name || cat }}
                  <button @click="toggleCategory(cat)" class="ml-2 text-[#193CB8] hover:text-[#142d8c]">
                    <i class='bx bx-x'></i>
                  </button>
                </div>
                
                <div v-for="type in activeJobType" :key="`type-${type}`" 
                     class="bg-[#193CB8]/10 text-[#193CB8] px-3 py-1 rounded-full text-sm flex items-center">
                  {{ jobTypes.find(t => t.id === type)?.name || type }}
                  <button @click="toggleJobType(type)" class="ml-2 text-[#193CB8] hover:text-[#142d8c]">
                    <i class='bx bx-x'></i>
                  </button>
                </div>
                
                <div v-if="searchQuery" 
                     class="bg-[#193CB8]/10 text-[#193CB8] px-3 py-1 rounded-full text-sm flex items-center">
                  Búsqueda: "{{ searchQuery }}"
                  <button @click="() => { searchQuery = ''; applyFilters(); }" class="ml-2 text-[#193CB8] hover:text-[#142d8c]">
                    <i class='bx bx-x'></i>
                  </button>
                </div>
                
                <button @click="clearAllFilters" 
                        class="text-gray-500 hover:text-gray-700 text-sm underline">
                  Limpiar todos
                </button>
              </div>
              
              <!-- Botón para mostrar filtros en móvil (fuera de la barra de búsqueda) -->
              <div class="md:hidden mb-4">
                <button 
                  @click="toggleFilterDrawer"
                  class="w-full bg-white border border-gray-200 rounded-lg py-3 px-4 flex items-center justify-center text-gray-700 hover:bg-gray-50 transition-colors shadow-sm"
                >
                  <i class='bx bx-filter-alt mr-2'></i>
                  Filtrar resultados
                </button>
              </div>
              
              <!-- Loading Indicator para filtros -->
              <div v-if="isLoading" class="mb-4 flex justify-center">
                <div class="animate-pulse flex space-x-2">
                  <div class="w-2 h-2 bg-[#193CB8] rounded-full"></div>
                  <div class="w-2 h-2 bg-[#193CB8] rounded-full animation-delay-200"></div>
                  <div class="w-2 h-2 bg-[#193CB8] rounded-full animation-delay-400"></div>
                </div>
              </div>
              
              <!-- Feed Items -->
              <div class="space-y-6">
                <JobCard v-for="job in jobOffersList" :key="job.id" :jobOffer="job"
                  :isSaved="savedJobs.has(job.id)" class="job-card"
                  @view="viewJobOffer" @apply="applyToJob" @save="toggleSaveJob" @like="toggleLikeJob"
                  @share="shareJob" />

                <!-- No Results -->
                <div v-if="jobOffersList.length === 0 && !isLoading" class="text-center py-8">
                  <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-search-alt text-3xl text-[#193CB8]'></i>
                  </div>
                  <h3 class="text-lg font-medium text-gray-800 mb-2">No se encontraron ofertas</h3>
                  <p class="text-gray-500 mb-4">Prueba con otros filtros o vuelve más tarde</p>
                </div>
              </div>
              
              <!-- Paginación -->
              <div v-if="pagination.lastPage > 1 && !isLoading" class="flex justify-center mt-8">
                <nav class="flex items-center space-x-1">
                  <!-- Botón Anterior -->
                  <button 
                    @click="goToPage(pagination.currentPage - 1)" 
                    :disabled="pagination.currentPage === 1"
                    :class="[
                      'px-3 py-2 rounded-md text-sm font-medium',
                      pagination.currentPage === 1 
                        ? 'text-gray-400 cursor-not-allowed' 
                        : 'text-gray-700 hover:bg-gray-100'
                    ]"
                  >
                    <i class='bx bx-chevron-left'></i>
                    <span class="sr-only">Anterior</span>
                  </button>
                  
                  <!-- Números de página -->
                  <template v-for="(page, index) in paginationPages" :key="index">
                    <span v-if="page === '...'" class="px-3 py-2 text-gray-500">...</span>
                    <button 
                      v-else
                      @click="goToPage(page)"
                      :class="[
                        'px-3 py-2 rounded-md text-sm font-medium',
                        page === pagination.currentPage
                          ? 'bg-[#193CB8] text-white'
                          : 'text-gray-700 hover:bg-gray-100'
                      ]"
                    >
                      {{ page }}
                    </button>
                  </template>
                  
                  <!-- Botón Siguiente -->
                  <button 
                    @click="goToPage(pagination.currentPage + 1)" 
                    :disabled="pagination.currentPage === pagination.lastPage"
                    :class="[
                      'px-3 py-2 rounded-md text-sm font-medium',
                      pagination.currentPage === pagination.lastPage 
                        ? 'text-gray-400 cursor-not-allowed' 
                        : 'text-gray-700 hover:bg-gray-100'
                    ]"
                  >
                    <i class='bx bx-chevron-right'></i>
                    <span class="sr-only">Siguiente</span>
                  </button>
                </nav>
              </div>
              
              <!-- Loading Indicator para paginación -->
              <div v-if="isLoading" class="flex justify-center py-4 mt-4">
                <div class="animate-pulse flex space-x-2">
                  <div class="w-2 h-2 bg-[#193CB8] rounded-full"></div>
                  <div class="w-2 h-2 bg-[#193CB8] rounded-full animation-delay-200"></div>
                  <div class="w-2 h-2 bg-[#193CB8] rounded-full animation-delay-400"></div>
                </div>
              </div>
            </div>

            <!-- Sidebar (Desktop) -->
            <div class="hidden md:block w-80 shrink-0">
              <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200 sticky top-20">
                <h2 class="text-lg font-bold text-gray-800 mb-3">Categorías</h2>

                <div class="space-y-2">
                  <button v-for="category in featuredCategories" :key="category.id" @click="toggleCategory(category.id)"
                    :class="[
                        'w-full flex cursor-pointer items-center px-3 py-2 rounded-lg text-left transition-colors',
                        activeCategory.includes(category.id)
                          ? 'bg-[#193CB8]/10 text-[#193CB8]'
                          : 'hover:bg-gray-100 text-gray-700'
                      ]">
                    <i :class="['bx mr-2', category.icon]" :style="{ color: category.color }"></i>
                    {{ category.name }}
                  </button>
                </div>

                <div class="border-t border-gray-200 my-4"></div>

                <h2 class="text-lg font-bold text-gray-800 mb-3">Modalidad</h2>

                <div class="space-y-2">
                  <button v-for="type in jobTypes" :key="type.id" @click="toggleJobType(type.id)" 
                    :class="[
                      'w-full flex cursor-pointer items-center px-3 py-2 rounded-lg text-left transition-colors',
                      activeJobType.includes(type.id)
                        ? 'bg-[#193CB8]/10 text-[#193CB8]'
                        : 'hover:bg-gray-100 text-gray-700'
                    ]">
                    {{ type.name }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Drawer de filtros para móvil -->
      <div v-if="isFilterDrawerOpen" 
           class="fixed inset-0 bg-black/50 z-50 md:hidden transition-opacity duration-300"
           @click="toggleFilterDrawer">
      </div>
      
      <div :class="[
        'fixed inset-y-0 right-0 w-4/5 max-w-xs bg-white z-50 transform transition-transform duration-300 ease-in-out shadow-xl md:hidden',
        isFilterDrawerOpen ? 'translate-x-0' : 'translate-x-full'
      ]">
        <div class="flex flex-col h-full">
          <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">Filtros</h2>
            <button @click="toggleFilterDrawer" class="text-gray-500 hover:text-gray-700">
              <i class='bx bx-x text-2xl'></i>
            </button>
          </div>
          
          <div class="flex-1 overflow-y-auto p-4">
            <div class="mb-6">
              <h3 class="text-md font-bold text-gray-800 mb-3">Categorías</h3>
              <div class="space-y-2">
                <button v-for="category in featuredCategories" :key="category.id" @click="toggleCategory(category.id)"
                  :class="[
                    'w-full flex cursor-pointer items-center px-3 py-2 rounded-lg text-left transition-colors',
                    activeCategory.includes(category.id)
                      ? 'bg-[#193CB8]/10 text-[#193CB8]'
                      : 'hover:bg-gray-100 text-gray-700'
                  ]">
                  <i :class="['bx mr-2', category.icon]" :style="{ color: category.color }"></i>
                  {{ category.name }}
                </button>
              </div>
            </div>
            
            <div class="border-t border-gray-200 my-4"></div>
            
            <div>
              <h3 class="text-md font-bold text-gray-800 mb-3">Modalidad</h3>
              <div class="space-y-2">
                <button v-for="type in jobTypes" :key="type.id" @click="toggleJobType(type.id)" 
                  :class="[
                    'w-full flex cursor-pointer items-center px-3 py-2 rounded-lg text-left transition-colors',
                    activeJobType.includes(type.id)
                      ? 'bg-[#193CB8]/10 text-[#193CB8]'
                      : 'hover:bg-gray-100 text-gray-700'
                  ]">
                  {{ type.name }}
                </button>
              </div>
            </div>
          </div>
          
          <div class="p-4 border-t border-gray-200">
            <button @click="clearAllFilters" 
                    class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors">
              Limpiar filtros
            </button>
            <button @click="toggleFilterDrawer" 
                    class="w-full bg-[#193CB8] text-white py-2 px-4 rounded-lg mt-2 hover:bg-[#142d8c] transition-colors">
              Aplicar filtros
            </button>
          </div>
        </div>
      </div>

      <!-- Scroll to Top Button -->
      <button v-show="showScrollTopButton" @click="scrollToTop"
        class="fixed bottom-6 right-6 w-12 h-12 rounded-full bg-[#193CB8] text-white shadow-lg flex items-center justify-center hover:bg-[#142d8c] transition-all duration-300 z-40 animate-fade-in">
        <i class='bx bx-chevron-up text-xl'></i>
      </button>

      <!-- Floating Action Button (Mobile) -->
      <div class="md:hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 z-40">
        <button @click="router.get('/ofertas/crear')"
          class="bg-[#193CB8] text-white px-6 py-3 rounded-full shadow-lg flex items-center justify-center hover:bg-[#142d8c] transition-all duration-300">
          <i class='bx bx-plus-circle mr-2'></i>
          Publicar oferta
        </button>
      </div>
    </div>
    <JobConfirmationModal v-model="isJobConfirmationModalOpen" @close="closeJobConfirmationModal"
      :is-open="isJobConfirmationModalOpen"
      :job-offer="getJobOffer(selectedJobId)" />
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
  
  /* Prevenir scroll cuando el drawer está abierto */
  body.no-scroll {
    overflow: hidden;
  }
</style>