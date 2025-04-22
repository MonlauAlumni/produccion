<script setup>
import { ref, computed, onMounted } from 'vue';
import Layout from '../../Components/Layout.vue';
import Card from '@/Components/Student/Card.vue';
import Pagination from '@/Components/Student/Pagination.vue';
import { router } from '@inertiajs/vue3';
const props = defineProps({
  students: Array,
  filters: Object,
});

const alumni = ref(props.students);

const loading = ref(true);

const filters = ref({
  search: '',
  status: 'all', // 'current', 'alumni', 'all'
  skills: [],
  availability: 'all' // 'available', 'not-available', 'all'
});
const currentPage = ref(1);
const itemsPerPage = 9;

const availableSkills = ref([
  'JavaScript', 'Vue.js', 'React', 'Node.js', 'Python', 'Java', 
  'PHP', 'Ruby', 'C#', '.NET', 'SQL', 'MongoDB', 'AWS', 'Azure',
  'DevOps', 'UI/UX', 'Mobile', 'Flutter', 'React Native'
]);



// function sampleSkills(index) {
//   const allSkills = availableSkills.value;
//   const numSkills = (index % 5) + 3; 
//   const shuffled = [...allSkills].sort(() => 0.5 - Math.random());
//   return shuffled.slice(0, numSkills);
// }


const filteredAlumni = computed(() => {
  return alumni.value.filter(person => {
    
    if (filters.value.search && !person.name.toLowerCase().includes(filters.value.search.toLowerCase())) {
      return false;
    }
    
    // Filtro por estado (alumno/exalumno)
    if (filters.value.status !== 'all' && person.status !== filters.value.status) {
      return false;
    }
    
    // Filtro por habilidades
    // if (filters.value.skills.length > 0 && !filters.value.skills.some(skill => person.skills.includes(skill))) {
    //   return false;
    // }
    
    // Filtro por disponibilidad
    if (filters.value.availability !== 'all') {
      const isAvailable = filters.value.availability === 'available';
      if (person.available !== isAvailable) {
        return false;
      }
    }
    
    return true;
  });
});


// Paginación
const totalPages = computed(() => Math.ceil(filteredAlumni.value.length / itemsPerPage));

const paginatedAlumni = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredAlumni.value.slice(start, end);
});

// Métodos
// function toggleSkill(skill) {
//   const index = filters.value.skills.indexOf(skill);
//   if (index === -1) {
//     filters.value.skills.push(skill);
//   } else {
//     filters.value.skills.splice(index, 1);
//   }
// }

function applyFilters() {
  currentPage.value = 1; // Resetear a la primera página al filtrar
}

function resetFilters() {
  filters.value = {
    search: '',
    status: 'all',
    skills: [],
    availability: 'all'
  };
  currentPage.value = 1;
}

function changePage(page) {
  currentPage.value = page;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function contactAlumni(alumni) {
  console.log('Contactando con alumno:', alumni.name);
  // Aquí iría la lógica para contactar con el alumno
}

function viewProfile(alumni) {
  router.get(`/perfil/${alumni.profile.slang}`);
}
</script>

<template>
  <Layout>
    <div class="bg-gray-50 min-h-screen py-8">
      <div class="container mx-auto px-4">
        <!-- Cabecera -->
        <div class="mb-8 text-center">
          <h1 class="text-3xl font-bold text-gray-800 mb-2">Directorio de Alumnos</h1>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Explora los perfiles de alumnos y exalumnos disponibles para incorporar talento a tu empresa
          </p>
        </div>
        
        <!-- Filtros y búsqueda -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
          <form @submit.prevent="applyFilters">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <!-- Búsqueda por nombre -->
              <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar por nombre</label>
                <div class="relative">
                  <input
                    type="text"
                    id="search"
                    v-model="filters.search"
                    placeholder="Nombre del alumno..."
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-[#193CB8] focus:border-[#193CB8] focus:outline-none"
                  />
                  <i class='bx bx-search absolute right-3 top-2.5 text-gray-400'></i>
                </div>
              </div>
              
              <!-- Filtro por estado -->
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select
                  id="status"
                  v-model="filters.status"
                  class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-[#193CB8] focus:border-[#193CB8] focus:outline-none"
                >
                  <option value="all">Todos</option>
                  <option value="current">Alumnos actuales</option>
                  <option value="alumni">Exalumnos</option>
                </select>
              </div>
              
              <!-- Filtro por disponibilidad -->
              <div>
                <label for="availability" class="block text-sm font-medium text-gray-700 mb-1">Disponibilidad</label>
                <select
                  id="availability"
                  v-model="filters.availability"
                  class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-[#193CB8] focus:border-[#193CB8] focus:outline-none"
                >
                  <option value="all">Cualquier disponibilidad</option>
                  <option value="available">Disponibles</option>
                  <option value="not-available">No disponibles</option>
                </select>
              </div>
            </div>
            
            <!-- Filtro por habilidades -->
            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Habilidades</label>
              <div class="flex flex-wrap gap-2">
                <div 
                  v-for="skill in availableSkills" 
                  :key="skill"
                  @click="toggleSkill(skill)"
                  :class="[
                    'px-3 py-1 rounded-full text-sm cursor-pointer transition',
                    filters.skills.includes(skill) 
                      ? 'bg-[#193CB8] text-white' 
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  {{ skill }}
                </div>
              </div>
            </div>
            
            <div class="flex justify-end mt-4">
              <button
                type="button"
                @click="resetFilters"
                class="px-4 py-2 mr-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
              >
                Limpiar filtros
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors"
              >
                <i class='bx bx-search mr-1'></i>
                Buscar
              </button>
            </div>
          </form>
        </div>
        
       
        
        <div >
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <Card
              v-for="alumni in paginatedAlumni"
              :key="alumni.id"
              :alumni="alumni"
              @contact="contactAlumni"
              @view-profile="viewProfile"
            />
          </div>
          
          <!-- Paginación -->
          <Pagination 
            v-if="filteredAlumni.length > itemsPerPage"
            :total-pages="totalPages" 
            :current-page="currentPage" 
            @page-changed="changePage"
          />
        </div>
        
        <!-- Estado vacío -->
        <div  class="bg-white rounded-lg shadow-md p-8 text-center">
          <div class="flex flex-col items-center">
            <i class='bx bx-user-x text-6xl text-gray-300 mb-4'></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No se encontraron alumnos</h3>
            <p class="text-gray-500 mb-4">Prueba con otros filtros o términos de búsqueda</p>
            <button
              @click="resetFilters"
              class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors"
            >
              Ver todos los alumnos
            </button>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

