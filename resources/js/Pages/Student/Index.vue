<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import Layout from '../../Components/Layout.vue';
import Card from '@/Components/Student/Card.vue';
import Pagination from '@/Components/Student/Pagination.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  students: Array,
  filters: Object,
  page: {
    type: Number,
    default: 1
  },
  perPage: {
    type: Number,
    default: 9
  }
});

const alumni = ref(props.students);
const loading = ref(false);

// Filtros mejorados basados en los campos de perfil
const filters = ref({
  search: '',
  degree: 'all',
  job_title: '',
  graduation_year: 'all',
  location: '',
  availability: 'all',
  experience_level: 'all'
});

const currentPage = ref(props.page);
const itemsPerPage = ref(props.perPage);

// Opciones para filtros
const degreeOptions = ref([
  'Marketing',
  'Informática',
  'Automoción',
]);

function normalizeText(text) {
  return text
    ?.toLowerCase()
    .normalize("NFD") // Descompone letras con acento en base + tilde
    .replace(/[\u0300-\u036f]/g, ""); // Elimina las tildes
}

const graduationYears = computed(() => {
  const currentYear = new Date().getFullYear();
  const years = ['all'];
  for (let i = currentYear; i >= currentYear - 10; i--) {
    years.push(i.toString());
  }
  return years;
});

const experienceLevels = ref([
  { value: 'all', label: 'Todos los niveles' },
  { value: 'junior', label: 'Junior (0-2 años)' },
  { value: 'mid', label: 'Mid-level (2-5 años)' },
  { value: 'senior', label: 'Senior (5+ años)' }
]);

// Filtrado mejorado
const filteredAlumni = computed(() => {
  return alumni.value.filter(person => {
    // Búsqueda por nombre
    if (filters.value.search && !person.name.toLowerCase().includes(filters.value.search.toLowerCase())) {
      return false;
    }
    
    // Filtro por titulación
    if (
      filters.value.degree !== 'all' &&
      normalizeText(person.training_area) !== normalizeText(filters.value.degree)
    ) {
      return false;
    }
    
    // Filtro por puesto de trabajo
    if (filters.value.job_title && 
        (!person.profile?.job_title || 
         !person.profile.job_title.toLowerCase().includes(filters.value.job_title.toLowerCase()))) {
      return false;
    }
    
    // Filtro por año de graduación
    if (filters.value.graduation_year !== 'all' && 
        person.profile?.graduation_year !== filters.value.graduation_year) {
      return false;
    }
    
    // Filtro por ubicación
    if (filters.value.location && 
        (!person.profile?.location || 
         !person.profile.location.toLowerCase().includes(filters.value.location.toLowerCase()))) {
      return false;
    }
    
    // Filtro por disponibilidad
    if (filters.value.availability !== 'all') {
      const availabilityMap = {
        available: ['Disponible para ofertas', 'Abierto a oportunidades'],
        remote: ['Solo ofertas remotas'],
        partial: ['Solo ofertas a tiempo parcial'],
        'not-available': ['No disponible actualmente']
      };

      const selectedOptions = availabilityMap[filters.value.availability];
      if (!selectedOptions.includes(person.profile?.availability)) {
        return false;
      }
    }
    
    // Filtro por nivel de experiencia (basado en graduation_year)
    if (filters.value.experience_level !== 'all' && person.profile?.graduation_year) {
      const currentYear = new Date().getFullYear();
      const yearsOfExperience = currentYear - parseInt(person.profile.graduation_year);
      
      if (filters.value.experience_level === 'junior' && yearsOfExperience > 2) {
        return false;
      } else if (filters.value.experience_level === 'mid' && (yearsOfExperience < 2 || yearsOfExperience > 5)) {
        return false;
      } else if (filters.value.experience_level === 'senior' && yearsOfExperience < 5) {
        return false;
      }
    }
    
    return true;
  });
});

// Paginación
const totalPages = computed(() => Math.ceil(filteredAlumni.value.length / itemsPerPage.value));
const totalItems = computed(() => filteredAlumni.value.length);

const paginatedAlumni = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredAlumni.value.slice(start, end);
});

// Observar cambios en los filtros para resetear la paginación
watch(filters, () => {
  currentPage.value = 1;
}, { deep: true });

// Métodos
function applyFilters() {
  currentPage.value = 1; // Resetear a la primera página al filtrar
  loading.value = true;
  
  // Simular carga (opcional)
  setTimeout(() => {
    loading.value = false;
  }, 300);
}

function resetFilters() {
  filters.value = {
    search: '',
    degree: 'all',
    job_title: '',
    graduation_year: 'all',
    location: '',
    availability: 'all',
    experience_level: 'all'
  };
  currentPage.value = 1;
}

function changePage(page) {
  currentPage.value = page;
  window.scrollTo({ top: 0, behavior: 'smooth' });
  
  // Opcional: actualizar la URL con el parámetro de página
  // router.get(window.location.pathname, { page }, { preserveState: true, replace: true });
}

// Ya no necesitamos esta función ya que el contacto se maneja directamente en el componente Card
// function contactAlumni(alumni) {
//   console.log('Contactando con alumno:', alumni.name);
//   // Aquí iría la lógica para contactar con el alumno
// }

function viewProfile(alumni) {
  router.get(`/perfil/${alumni.profile.slang}`);
}

// Inicializar con valores de URL si existen
onMounted(() => {
  // Aquí podrías inicializar los filtros desde la URL si es necesario
  loading.value = false;
});
</script>

<template>
  <Layout>
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-8">
      <div class="container mx-auto px-4">
        <!-- Cabecera -->
        <div class="mb-8 text-center">
          <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-2">Directorio de Talento</h1>
          <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
            Encuentra el talento ideal para tu empresa entre nuestros alumnos y exalumnos cualificados
          </p>
        </div>
        
        <!-- Filtros y búsqueda mejorados -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-8 border border-gray-200 dark:border-gray-700">
          <form @submit.prevent="applyFilters" class="space-y-6">
            <!-- Fila 1: Búsqueda rápida -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar por nombre</label>
                <div class="relative">
                  <input
                    type="text"
                    id="search"
                    v-model="filters.search"
                    placeholder="Nombre del alumno..."
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-[#193CB8] dark:focus:ring-blue-400 focus:border-[#193CB8] dark:focus:border-blue-400 focus:outline-none"
                  />
                  <i class='bx bx-search absolute right-3 top-2.5 text-gray-400 dark:text-gray-500'></i>
                </div>
              </div>
              <div>
                <label for="job_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Puesto de trabajo</label>
                <input
                  type="text"
                  id="job_title"
                  v-model="filters.job_title"
                  placeholder="Desarrollador, Diseñador..."
                  class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-[#193CB8] dark:focus:ring-blue-400 focus:border-[#193CB8] dark:focus:border-blue-400 focus:outline-none"
                />
              </div>
              <div>
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ubicación</label>
                <input
                  type="text"
                  id="location"
                  v-model="filters.location"
                  placeholder="Ciudad, provincia o país..."
                  class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-[#193CB8] dark:focus:ring-blue-400 focus:border-[#193CB8] dark:focus:border-blue-400 focus:outline-none"
                />
              </div>
            </div>

            <!-- Fila 2: Filtros avanzados -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div>
                <label for="degree" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titulación</label>
                <select
                  id="degree"
                  v-model="filters.degree"
                  class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-[#193CB8] dark:focus:ring-blue-400 focus:border-[#193CB8] dark:focus:border-blue-400 focus:outline-none"
                >
                  <option value="all">Todas las titulaciones</option>
                  <option v-for="degree in degreeOptions" :key="degree" :value="degree">{{ degree }}</option>
                </select>
              </div>
              <div>
                <label for="availability" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Disponibilidad</label>
                <select
                  id="availability"
                  v-model="filters.availability"
                  class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-[#193CB8] dark:focus:ring-blue-400 focus:border-[#193CB8] dark:focus:border-blue-400 focus:outline-none"
                >
                  <option value="all">Cualquier disponibilidad</option>
                  <option value="available">Disponible</option>
                  <option value="remote">Solo ofertas remotas</option>
                  <option value="partial">Solo ofertas a tiempo parcial</option>
                  <option value="not-available">No disponibles</option>
                </select>
              </div>
              <div>
                <label for="graduation_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Año de graduación</label>
                <select
                  id="graduation_year"
                  v-model="filters.graduation_year"
                  class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-[#193CB8] dark:focus:ring-blue-400 focus:border-[#193CB8] dark:focus:border-blue-400 focus:outline-none"
                >
                  <option value="all">Todos los años</option>
                  <option v-for="year in graduationYears" :key="year" :value="year">
                    {{ year !== 'all' ? year : 'Todos los años' }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-2">
              <button
                type="button"
                @click="resetFilters"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
              >
                Limpiar filtros
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-[#193CB8] dark:bg-blue-700 text-white rounded-lg hover:bg-[#142d8c] dark:hover:bg-blue-800 transition"
                :disabled="loading"
              >
                <span v-if="loading" class="flex items-center">
                  <i class='bx bx-loader-alt animate-spin mr-1'></i>
                  Cargando...
                </span>
                <span v-else class="flex items-center">
                  <i class='bx bx-search mr-1'></i>
                  Aplicar filtros
                </span>
              </button>
            </div>
          </form>
        </div>
        
        <!-- Estado de carga -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#193CB8] dark:border-blue-400"></div>
        </div>
        
        <!-- Lista de alumnos -->
        <div v-else-if="filteredAlumni.length > 0">
          <!-- Contador de resultados -->
          <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Se encontraron <span class="font-semibold">{{ filteredAlumni.length }}</span> resultados
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <Card
              v-for="alumni in paginatedAlumni"
              :key="alumni.id"
              :alumni="alumni"
              @view-profile="viewProfile"
            />
          </div>
          
          <!-- Paginación mejorada -->
          <Pagination 
            v-if="filteredAlumni.length > itemsPerPage"
            :total-pages="totalPages" 
            :current-page="currentPage"
            :total-items="totalItems"
            :items-per-page="itemsPerPage"
            @page-changed="changePage"
            class="mt-8"
          />
        </div>
        
        <!-- Estado vacío -->
        <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center border border-gray-200 dark:border-gray-700">
          <div class="flex flex-col items-center">
            <i class='bx bx-user-x text-6xl text-gray-300 dark:text-gray-600 mb-4'></i>
            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">No se encontraron alumnos</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Prueba con otros filtros o términos de búsqueda</p>
            <button
              @click="resetFilters"
              class="px-4 py-2 bg-[#193CB8] dark:bg-blue-700 text-white rounded-lg hover:bg-[#142d8c] dark:hover:bg-blue-800 transition-colors"
            >
              Ver todos los alumnos
            </button>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>