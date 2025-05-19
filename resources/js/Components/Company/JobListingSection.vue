<script setup>
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3';
    
  const props = defineProps({
    items: Array,  
    title: String,
    slang: String,
    isAdmin: Boolean
  });
    
  const isJobListingModalOpen = ref(false);
    
  const handleSaveJobListing = (jobListingData) => {
    console.log("Nueva oferta de empleo:", jobListingData);
  };
  
  const isExpanded = ref(true);
    
  const toggleExpand = () => {
    isExpanded.value = !isExpanded.value;
  };
  
  const createJobOffer = () => {
    router.get('/ofertas/crear');
  }
  
  // Calcular tiempo desde publicación
  const getTimeAgo = (dateString) => {
    if (!dateString) return 'Reciente';
    
    const posted = new Date(dateString);
    const today = new Date();
    const diffTime = Math.abs(today - posted);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) return 'Hoy';
    if (diffDays === 1) return 'Ayer';
    if (diffDays < 7) return `Hace ${diffDays} días`;
    if (diffDays < 30) return `Hace ${Math.floor(diffDays / 7)} semanas`;
    return `Hace ${Math.floor(diffDays / 30)} meses`;
  };
  
  // Obtener etiqueta para tipo de trabajo
  const getJobTypeLabel = (jobType) => {
    const types = {
      'remote': 'Remoto',
      'onsite': 'Presencial',
      'hybrid': 'Híbrido'
    };
    return types[jobType] || jobType;
  };
  
  const viewJobOffer = (id) => {
    router.get(`/ofertas/${id}`);
  };
  
  // Obtener color para el tipo de trabajo
  const getJobTypeColor = (jobType) => {
    const colors = {
      'remote': 'bg-purple-100 text-purple-700',
      'onsite': 'bg-blue-100 text-blue-700',
      'hybrid': 'bg-indigo-100 text-indigo-700'
    };
    return colors[jobType] || 'bg-gray-100 text-gray-700';
  };
  
  // Obtener icono para el tipo de trabajo
  const getJobTypeIcon = (jobType) => {
    const icons = {
      'remote': 'bx-laptop',
      'onsite': 'bx-building',
      'hybrid': 'bx-building-house'
    };
    return icons[jobType] || 'bx-briefcase';
  };
  </script>
    
  <template>
    <div class="mb-6">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <button @click="toggleExpand"
          class="w-full flex justify-between items-center py-4 px-6 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 transition-all duration-300 ease-in-out hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
          <div class="flex items-center gap-3">
            <i class='bx bx-briefcase text-xl text-[#193CB8] dark:text-blue-200'></i>
            <h2 class="text-xl font-semibold">{{ title }}</h2>
            <span v-if="items && items.length" class="bg-[#193CB8] dark:bg-blue-900 text-white text-xs rounded-full px-2 py-1">
              {{ items.length }}
            </span>
          </div>
          <div class="space-x-3 flex items-center">
            <button v-if="isAdmin" @click.stop="createJobOffer" 
              class="flex items-center gap-1 text-[#193CB8] dark:text-blue-200 hover:text-[#2748c6] dark:hover:text-blue-300 cursor-pointer bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 px-3 py-1.5 rounded-md transition-colors">
              <i class="bx bx-plus"></i>
              <span class="text-sm font-medium">Nueva Oferta</span>
            </button>
            <i class="bx text-xl" :class="isExpanded ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
          </div>
        </button>
        
        <transition 
          enter-active-class="transition-all duration-300 ease-out"
          leave-active-class="transition-all duration-300 ease-in"
          enter-from-class="max-h-0 opacity-0" 
          enter-to-class="max-h-[1000px] opacity-100"
          leave-from-class="max-h-[1000px] opacity-100" 
          leave-to-class="max-h-0 opacity-0"
        >
          <div v-if="isExpanded" class="overflow-hidden">
            <div v-if="items && items.length" class="grid gap-3 p-4">
              <!-- Nuevo diseño de mini cards -->
              <div v-for="(item, index) in items" :key="index"
                class="group relative bg-white dark:bg-gray-800 border-l-4 border-l-[#193CB8] dark:border-l-blue-900 border-t border-r border-b border-gray-200 dark:border-gray-700 rounded-r-lg hover:shadow-md transition-all duration-200 overflow-hidden">
                
                <!-- Indicador de estado (si existe) -->
                <div v-if="item.status === 'featured'" 
                  class="absolute top-0 right-0 w-0 h-0 border-t-[40px] border-t-[#193CB8] dark:border-t-blue-900 border-l-[40px] border-l-transparent">
                  <i class='bx bx-star text-white absolute -right-1 top-[-35px]'></i>
                </div>
                
                <div v-if="item.status === 'urgent'" 
                  class="absolute top-0 right-0 w-0 h-0 border-t-[40px] border-t-red-500 border-l-[40px] border-l-transparent">
                  <i class='bx bx-time text-white absolute -right-1 top-[-35px]'></i>
                </div>
                
                <div class="p-4">
                  <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <!-- Información principal -->
                    <div class="flex-1">
                      <div class="flex items-center gap-2">
                        <h3 @click="viewJobOffer(item.id)" 
                          class="font-semibold text-[#193CB8] dark:text-blue-200 hover:underline cursor-pointer group-hover:text-[#2748c6] dark:group-hover:text-blue-300 transition-colors">
                          {{ item.title }}
                        </h3>
                        
                        <!-- Tiempo desde publicación -->
                        <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">
                          {{ getTimeAgo(item.created_at || item.published_date) }}
                        </span>
                      </div>
                      
                      <!-- Empresa -->
                      <div class="text-sm text-gray-700 dark:text-gray-300 mt-1 font-medium">
                        {{ item.company ? item.company.name : '' }}
                      </div>
                    </div>
                    
                    <!-- Salario (si existe) -->
                    <div v-if="item.minimum_salary && item.maximum_salary" 
                      class="bg-green-50 dark:bg-green-900 text-green-700 dark:text-green-300 px-3 py-1.5 rounded-md text-sm font-medium flex items-center gap-1 self-start sm:self-center">
                      <i class='bx bx-euro'></i>
                      <span>{{ (item.minimum_salary && item.maximum_salary ? 
                        `${item.minimum_salary.toLocaleString()}€ - ${item.maximum_salary.toLocaleString()}€` : 
                        item.minimum_salary ? `Desde ${item.minimum_salary.toLocaleString()}€` : 
                        item.maximum_salary ? `Hasta ${item.maximum_salary.toLocaleString()}€` : 'No especificado') }}</span>
                    </div>
                  </div>
                  
                  <!-- Detalles del trabajo -->
                  <div class="flex flex-wrap gap-2 mt-3">
                    <div :class="[
                      'flex items-center text-xs px-2 py-1 rounded-md', 
                      getJobTypeColor(item.work_mode || item.type)
                    ]">
                      <i :class="['bx mr-1', getJobTypeIcon(item.work_mode)]"></i>
                      {{ getJobTypeLabel(item.work_mode) }}
                    </div>
                    
                    <div class="flex items-center text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded-md">
                      <i class='bx bx-map-pin mr-1'></i>
                      {{ item.location }}
                    </div>
                  </div>

                  <!-- Descripción -->
                  <div class="mt-3">
                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3">
                      {{ item.description }}
                    </p>
                  </div>
                  
                  <!-- Skills/Tags -->
                  <div v-if="item.skills && item.skills.length" class="mt-3 flex flex-wrap gap-1.5">
                    <span v-for="(skill, skillIndex) in item.skills.slice(0, 3)" :key="skillIndex"
                      class="bg-blue-50 dark:bg-blue-900 text-[#193CB8] dark:text-blue-200 px-2 py-0.5 rounded text-xs">
                      {{ skill }}
                    </span>
                    <span v-if="item.skills.length > 3" class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                      +{{ item.skills.length - 3 }} más
                    </span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Empty state -->
            <div v-else class="p-8 text-center">
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 dark:bg-blue-900 mb-4">
                <i class='bx bx-briefcase text-3xl text-[#193CB8] dark:text-blue-200'></i>
              </div>
              <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">No hay ofertas disponibles</h3>
              <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                {{ isAdmin ? 'Añade tu primera oferta de empleo para que los alumnos puedan aplicar.' : 'Esta empresa aún no ha publicado ofertas de empleo.' }}
              </p>
              <button v-if="isAdmin" @click="createJobOffer"
                class="mt-4 inline-flex cursor-pointer items-center gap-2 bg-[#193CB8] dark:bg-blue-900 hover:bg-[#2748c6] dark:hover:bg-blue-800 text-white px-4 py-2 rounded-md transition-colors">
                <i class='bx bx-plus'></i>
                <span>Añadir oferta</span>
              </button>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </template>