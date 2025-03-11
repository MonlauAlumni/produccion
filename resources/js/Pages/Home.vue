<script setup>
  import { ref, onMounted, computed } from 'vue'
  import { router } from '@inertiajs/vue3'
  import Layout from '@/Components/Layout.vue'
  import JobCard from '@/Pages/JobOffers/JobCard.vue'
  
  const props = defineProps({
    jobOffers: {
      type: Object,
      required: true
    }
  });
  
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
  
  // Estado para interacciones
  const savedJobs = ref(new Set());
  const likedJobs = ref(new Set());
  const viewedJobs = ref(new Set());
  
  // Estado para filtros rápidos
  const activeFilter = ref('all');
  
  // Categorías destacadas
  const featuredCategories = [
    { id: 'tech', name: 'Tecnología', icon: 'bx-code-alt', color: '#193CB8' },
    { id: 'marketing', name: 'Marketing', icon: 'bx-line-chart', color: '#E44D26' },
    { id: 'design', name: 'Diseño', icon: 'bx-palette', color: '#FF7C43' },
    { id: 'finance', name: 'Finanzas', icon: 'bx-money', color: '#28A745' },
    { id: 'health', name: 'Salud', icon: 'bx-plus-medical', color: '#17A2B8' },
    { id: 'education', name: 'Educación', icon: 'bx-book-open', color: '#6F42C1' }
  ];
  
  // Cargar más ofertas (paginación)
  const loadMoreJobs = async () => {
    if (isLoading.value || pagination.value.currentPage >= pagination.value.lastPage) return;
    
    isLoading.value = true;
    
    try {
      // Usar Inertia para cargar la siguiente página
      const nextPage = pagination.value.currentPage + 1;
      await router.get(
        '/ofertas', 
        { page: nextPage },
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
    console.log("hola" . $jobId);
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
    
    // Navegar a la ruta con el filtro aplicado
    router.get('/ofertas', { filter }, { preserveScroll: true });
  };
  
  // Inicializar
  onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    
    // Limpiar al desmontar
    return () => {
      window.removeEventListener('scroll', handleScroll);
    };
  });
  </script>
  
  <template>
    <Layout>
      <div class="min-h-screen bg-gray-50 flex flex-col">
        <!-- Categories Section -->
        <div class="bg-white border-b shadow-sm sticky top-0 z-10">
          <div class="max-w-6xl mx-auto px-4">
            <div class="flex items-center justify-between py-3">
              <!-- Quick Filters -->
              <div class="flex items-center space-x-1 overflow-x-auto hide-scrollbar">
                <button 
                  @click="setFilter('all')"
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors',
                    activeFilter === 'all' 
                      ? 'bg-[#193CB8] text-white' 
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  <i class='bx bx-globe mr-1'></i>
                  Todas
                </button>
                <button 
                  @click="setFilter('recent')"
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors',
                    activeFilter === 'recent' 
                      ? 'bg-[#193CB8] text-white' 
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  <i class='bx bx-time mr-1'></i>
                  Recientes
                </button>
                <button 
                  @click="setFilter('saved')"
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors',
                    activeFilter === 'saved' 
                      ? 'bg-[#193CB8] text-white' 
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  <i class='bx bx-bookmark mr-1'></i>
                  Guardadas
                </button>
                <button 
                  @click="setFilter('remote')"
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors',
                    activeFilter === 'remote' 
                      ? 'bg-[#193CB8] text-white' 
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  <i class='bx bx-laptop mr-1'></i>
                  Remoto
                </button>
              </div>
              
              <!-- View Toggle (podría implementarse para cambiar la vista) -->
              <div class="hidden md:flex items-center space-x-2">
                <button class="p-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200">
                  <i class='bx bx-list-ul'></i>
                </button>
                <button class="p-2 rounded-full bg-[#193CB8] text-white">
                  <i class='bx bx-grid-alt'></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Main Feed -->
        <div class="flex-1 py-6">
          <div class="max-w-3xl mx-auto px-4">
            <!-- Feed Items -->
            <div class="space-y-6">
              <JobCard 
                v-for="job in jobOffersList" 
                :key="job.id"
                :jobOffer="job"
                :isLiked="likedJobs.has(job.id)"
                :isSaved="savedJobs.has(job.id)"
                class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden job-card"
                :class="{ 'border-[#193CB8] border-2': job.status === 'featured' }"
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
                  <div class="w-2 h-2 bg-[#193CB8] rounded-full"></div>
                  <div class="w-2 h-2 bg-[#193CB8] rounded-full"></div>
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
        </div>
        
        <!-- Scroll to Top Button -->
        <button 
          v-show="showScrollTopButton"
          @click="scrollToTop"
          class="fixed bottom-6 right-6 w-12 h-12 rounded-full bg-[#193CB8] text-white shadow-lg flex items-center justify-center hover:bg-[#142d8c] transition-all duration-300 z-50 animate-fade-in"
        >
          <i class='bx bx-chevron-up text-xl'></i>
        </button>
        
        <!-- Floating Action Button (Mobile) -->
        <div class="md:hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50">
          <button 
            @click="router.get('/ofertas/crear')"
            class="bg-[#193CB8] text-white px-6 py-3 rounded-full shadow-lg flex items-center justify-center hover:bg-[#142d8c] transition-all duration-300"
          >
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
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
  }
  
  .hide-scrollbar::-webkit-scrollbar {
    display: none;  /* Chrome, Safari and Opera */
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
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
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
  </style>