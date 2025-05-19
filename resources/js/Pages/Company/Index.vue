<script setup>
  import { ref, watch, onMounted, computed } from 'vue';
  import { usePage, router } from '@inertiajs/vue3';
  import Layout from '../../Components/Layout.vue';
  import CompanyCard from '../../Components/Company/CompanyCard.vue';
  
  const props = defineProps({
    companies: Object,
    locations: Array,
    filters: Object,
  });
  
  const page = usePage();
  const companies = computed(() => props.companies);
  const locations = computed(() => props.locations);
  
  
  const initialFilters = {
    search: props.filters?.search || '',
    sector: props.filters?.sector || '',
    location: props.filters?.location || '',
  };
  
  const filters = ref({ ...initialFilters });
  
  // Lista de sectores
  const sectors = [
    { id: 1, name: 'Tecnología' },
    { id: 2, name: 'Salud' },
    { id: 3, name: 'Finanzas' },
    { id: 4, name: 'Educación' },
    { id: 5, name: 'Retail' },
    { id: 6, name: 'Telecomunicaciones' },
    { id: 7, name: 'Energía' },
    { id: 8, name: 'Automoción' },
    { id: 9, name: 'Consultoría' },
    { id: 10, name: 'Alimentación' }
  ];
  
  // Estado de carga
  const isLoading = ref(false);
  
  // Función para aplicar los filtros y navegar a la página correspondiente
  const applyFilters = () => {
    isLoading.value = true;
    
    router.get('/empresas', filters.value, {
      preserveState: true,
      only: ['companies'],
      onSuccess: () => {
        isLoading.value = false;
      },
      onError: () => {
        isLoading.value = false;
      }
    });
  };
  
  // Resetear filtros
  const resetFilters = () => {
    filters.value = { ...initialFilters };
  };
  
  // Ir a una página específica
  const goToPage = (page) => {
    isLoading.value = true;
    
    const params = { ...filters.value, page };
    
    router.get('/empresas', params, {
      preserveState: true,
      only: ['companies'],
      onSuccess: () => {
        isLoading.value = false;
      },
      onError: () => {
        isLoading.value = false;
      }
    });
  };
  
  // Observar cambios en los filtros y aplicarlos automáticamente
  const debouncedApplyFilters = () => {
    isLoading.value = true;
    setTimeout(() => {
      applyFilters();
    }, 300);
  };
  
  watch(
    () => [filters.value.search, filters.value.sector, filters.value.location],
    () => {
      if (filters.value.search) {
        debouncedApplyFilters();
      } else {
        applyFilters();
      }
    }
  );
</script>

<template>
  <Layout>
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-8">
      <div class="container mx-auto px-4">
        <!-- Cabecera -->
        <div class="mb-8 text-center">
          <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-2">Empresas</h1>
          <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
            Explora las empresas registradas en Alumni y conecta con ellas para descubrir oportunidades profesionales
          </p>
        </div>

        <!-- Filtros y búsqueda -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Búsqueda por nombre -->
            <div>
              <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar por nombre</label>
              <div class="relative">
                <input
                  type="text"
                  id="search"
                  v-model="filters.search"
                  placeholder="Nombre de la empresa..."
                  class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 focus:ring-[#193CB8] focus:border-[#193CB8] focus:outline-none bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                />
                <i class='bx bx-search absolute right-3 top-2.5 mt-1 text-gray-400 dark:text-gray-500'></i>
              </div>
            </div>

            <!-- Filtro por sector -->
            <div>
              <label for="sector" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sector</label>
              <select
                id="sector"
                v-model="filters.sector"
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 focus:ring-[#193CB8] focus:border-[#193CB8] focus:outline-none bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
              >
                <option value="">Todos los sectores</option>
                <option v-for="sector in sectors" :key="sector.id" :value="sector.name">
                  {{ sector.name }}
                </option>
              </select>
            </div>

            <!-- Filtro por ubicación -->
            <div>
              <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ubicación</label>
              <select
                id="location"
                v-model="filters.location"
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 focus:ring-[#193CB8] focus:border-[#193CB8] focus:outline-none bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
              >
                <option value="">Todas las ubicaciones</option>
                <option v-for="location in locations" :key="location" :value="location">
                  {{ location }}
                </option>
              </select>
            </div>
          </div>

          <div class="flex justify-end mt-4">
            <button
              type="button"
              @click="resetFilters"
              class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800"
            >
              Limpiar filtros
            </button>
          </div>
        </div>

        <!-- Indicador de carga -->
        <div v-if="isLoading" class="flex justify-center my-8">
          <div class="animate-pulse flex space-x-2">
            <div class="w-3 h-3 bg-[#193CB8] dark:bg-blue-200 rounded-full"></div>
            <div class="w-3 h-3 bg-[#193CB8] dark:bg-blue-200 rounded-full animation-delay-200"></div>
            <div class="w-3 h-3 bg-[#193CB8] dark:bg-blue-200 rounded-full animation-delay-400"></div>
          </div>
        </div>

        <!-- Resultados -->
        <div v-else-if="companies.data && companies.data.length > 0">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <CompanyCard
              v-for="company in companies.data"
              :key="company.id"
              :company="company"
            />
          </div>

          <!-- Paginación -->
          <div v-if="companies.last_page > 1" class="flex justify-center mt-8">
            <nav class="flex items-center space-x-1">
              <!-- Botón Anterior -->
              <button 
                @click="goToPage(companies.current_page - 1)" 
                :disabled="companies.current_page === 1"
                :class="[
                  'px-3 py-2 rounded-md text-sm font-medium',
                  companies.current_page === 1 
                    ? 'text-gray-400 cursor-not-allowed' 
                    : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800'
                ]"
              >
                <i class='bx bx-chevron-left'></i>
                <span class="sr-only">Anterior</span>
              </button>

              <!-- Números de página -->
              <template v-for="page in companies.links" :key="page.label">
                <span 
                  v-if="page.label === '...'" 
                  class="px-3 py-2 text-gray-500 dark:text-gray-400"
                >
                  ...
                </span>
                <button 
                  v-else-if="!page.url"
                  disabled
                  class="px-3 py-2 text-gray-400 dark:text-gray-500 cursor-not-allowed rounded-md text-sm font-medium"
                >
                  {{ page.label }}
                </button>
                <button 
                  v-else
                  @click="goToPage(page.label)"
                  :class="[
                    'px-3 py-2 rounded-md text-sm font-medium',
                    page.active
                      ? 'bg-[#193CB8] dark:bg-blue-200 text-white dark:text-gray-900'
                      : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800'
                  ]"
                >
                  {{ page.label }}
                </button>
              </template>

              <!-- Botón Siguiente -->
              <button 
                @click="goToPage(companies.current_page + 1)" 
                :disabled="companies.current_page === companies.last_page"
                :class="[
                  'px-3 py-2 rounded-md text-sm font-medium',
                  companies.current_page === companies.last_page 
                    ? 'text-gray-400 cursor-not-allowed' 
                    : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800'
                ]"
              >
                <i class='bx bx-chevron-right'></i>
                <span class="sr-only">Siguiente</span>
              </button>
            </nav>
          </div>
        </div>

        <!-- Estado vacío -->
        <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center">
          <div class="flex flex-col items-center">
            <i class='bx bx-building-house text-6xl text-gray-300 dark:text-gray-600 mb-4'></i>
            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">No se encontraron empresas</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Prueba con otros filtros o términos de búsqueda</p>
            <button
              @click="resetFilters"
              class="px-4 py-2 bg-[#193CB8] dark:bg-blue-200 text-white dark:text-gray-900 rounded-lg hover:bg-[#142d8c] dark:hover:bg-blue-300 transition-colors"
            >
              Ver todas las empresas
            </button>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<style scoped>
  /* Animaciones para los indicadores de carga */
  @keyframes pulse {
    0%, 100% {
      opacity: 1;
    }
    50% {
      opacity: 0.5;
    }
  }
  
  .animation-delay-200 {
    animation-delay: 0.2s;
  }
  
  .animation-delay-400 {
    animation-delay: 0.4s;
  }
  
  /* Transiciones para las tarjetas de empresa */
  .company-card-enter-active,
  .company-card-leave-active {
    transition: all 0.3s ease;
  }
  
  .company-card-enter-from,
  .company-card-leave-to {
    opacity: 0;
    transform: translateY(20px);
  }
</style>