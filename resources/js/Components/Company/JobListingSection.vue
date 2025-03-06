<script setup>
  import { ref } from 'vue';
  
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
  
  const isExpanded = ref(true); // Cambiado a true por defecto para mostrar las ofertas
  
  const toggleExpand = () => {
    isExpanded.value = !isExpanded.value;
  };
  </script>
  
  <template>
    <div class="mb-6">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <button @click="toggleExpand"
          class="w-full flex justify-between items-center py-4 px-6 bg-white text-gray-800 transition-all duration-300 ease-in-out hover:bg-gray-50 cursor-pointer">
          <div class="flex items-center gap-3">
            <i class='bx bx-briefcase text-xl text-[#193CB8]'></i>
            <h2 class="text-xl font-semibold">{{ title }}</h2>
            <span v-if="items && items.length" class="bg-[#193CB8] text-white text-xs rounded-full px-2 py-1">
              {{ items.length }}
            </span>
          </div>
          <div class="space-x-3 flex items-center">
            <button v-if="isAdmin" @click.stop="isJobListingModalOpen = true" 
              class="flex items-center gap-1 text-[#193CB8] hover:text-[#2748c6] bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-md transition-colors">
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
            <div v-if="items && items.length" class="divide-y divide-gray-100">
              <div v-for="(item, index) in items" :key="index"
                class="p-6 hover:bg-gray-50 transition-colors duration-200">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                  <div class="flex-1">
                    <h3 class="font-semibold text-lg text-[#193CB8]">
                      {{ item.title }}
                    </h3>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-2 text-sm text-gray-500">
                      <div class="flex items-center gap-1">
                        <i class='bx bx-map-pin'></i>
                        <span>{{ item.location }}</span>
                      </div>
                      <div class="flex items-center gap-1">
                        <i class='bx bx-time-five'></i>
                        <span>{{ item.type }}</span>
                      </div>
                      <div class="flex items-center gap-1">
                        <i class='bx bx-calendar'></i>
                        <span>Publicado: {{ item.published_date || 'Recientemente' }}</span>
                      </div>
                    </div>
                    <p class="mt-3 text-gray-600">{{ item.description }}</p>
                    
                    <!-- Tags/Skills -->
                    <div class="mt-4 flex flex-wrap gap-2">
                      <span v-for="(skill, skillIndex) in item.skills || []" :key="skillIndex"
                        class="bg-blue-50 text-[#193CB8] px-2 py-1 rounded-md text-xs">
                        {{ skill }}
                      </span>
                    </div>
                  </div>
                  
                  <div class="flex flex-row md:flex-col items-center md:items-end gap-3">
                    <span v-if="item.salary" class="bg-green-50 text-green-700 px-3 py-1 rounded-md text-sm font-medium">
                      {{ item.salary }}
                    </span>
                    <button class="bg-[#193CB8] hover:bg-[#2748c6] text-white px-4 py-2 rounded-md transition-colors text-sm font-medium">
                      Aplicar ahora
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Empty state -->
            <div v-else class="p-8 text-center">
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 mb-4">
                <i class='bx bx-briefcase text-3xl text-[#193CB8]'></i>
              </div>
              <h3 class="text-lg font-medium text-gray-800 mb-2">No hay ofertas disponibles</h3>
              <p class="text-gray-500 max-w-md mx-auto">
                {{ isAdmin ? 'Añade tu primera oferta de empleo para que los alumnos puedan aplicar.' : 'Esta empresa aún no ha publicado ofertas de empleo.' }}
              </p>
              <button v-if="isAdmin" @click="isJobListingModalOpen = true"
                class="mt-4 inline-flex items-center gap-2 bg-[#193CB8] hover:bg-[#2748c6] text-white px-4 py-2 rounded-md transition-colors">
                <i class='bx bx-plus'></i>
                <span>Añadir oferta</span>
              </button>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </template>