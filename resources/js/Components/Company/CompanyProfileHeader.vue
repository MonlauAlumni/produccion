<script setup>
  import SocialButton from '../SocialButton.vue'
  import { computed, ref } from 'vue'
  import { router } from '@inertiajs/vue3';
  
  const props = defineProps({
    company: Object,
    profile: Object,
    isAdmin: Boolean,
    openEditModal: Function,
  });
  
  const uploadProfileImage = (e) => {
    const file = e.target.files[0];
    const formData = new FormData();
    formData.append('logo', file);
    router.post(`/empresa/${props.company.slang}/update-logo`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      }, onSuccess: () => {
   
      props.company.logo_url = response.data.logo_url;
        },  }
  );
  };
  
  // Computed property for company industry
  const industry = computed(() => {
    return props.company.sector || 'Sector no especificado';
  });
  
  // Computed property for company size
  const companySize = computed(() => {
    return props.company.size || 'Tamaño no especificado';
  });
  </script>
  
  <template>
    <div class="bg-white rounded-lg shadow-md border border-gray-200 w-full">
      <div class="p-6">
        <div class="flex flex-col md:flex-row md:items-center gap-6">
          <!-- Logo de la empresa -->
          <div class="relative h-26 w-28 rounded-lg bg-white shadow-md overflow-hidden group border-4 border-white">
            <img 
              :src="company.profile_picture" 
              :alt="company.name + ' logo'" 
              class="h-26 w-28 rounded-lg" 
            />
            
            <!-- Hover para subir nueva imagen (solo para administradores) -->
            <div v-if="isAdmin" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-60 opacity-0 group-hover:opacity-100 transition-opacity">
              <label class="cursor-pointer text-white bg-[#193CB8] hover:bg-[#2748c6] px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                <i class="bx bx-upload mr-1"></i> Cambiar
                <input type="file" class="hidden" @change="uploadProfileImage" accept="image/*">
              </label>
            </div>
          </div>
          
          <!-- Información de la empresa -->
          <div class="flex-1">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
              <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">{{ company.company_name }}</h1>
                <div class="flex flex-wrap items-center gap-3 mt-2 text-gray-600">
                  <div class="flex items-center gap-1">
                    <i class='bx bx-buildings text-[#193CB8]'></i>
                    <span>{{ industry }}</span>
                  </div>
                  <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                  <div class="flex items-center gap-1">
                    <i class='bx bx-group text-[#193CB8]'></i>
                    <span>{{ companySize }}</span>
                  </div>
                  <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                  <div class="flex items-center gap-1">
                    <i class='bx bx-map-pin text-[#193CB8]'></i>
                    <span>{{ company.location || 'Ubicación no especificada' }}</span>
                  </div>
                </div>
              </div>
              
              <!-- Botones de acción -->
              <div class="flex items-center gap-3 mt-4 md:mt-0">
                <button v-if="isAdmin" @click="props.openEditModal" 
                  class="flex items-center cursor-pointer gap-1.5 bg-[#193CB8] hover:bg-[#2748c6] text-white px-3 py-2 rounded-md transition-colors">
                  <div class="flex justify-center items-center w-30 gap-3"><i class='bx bx-pencil'></i>
                  <span>Editar Perfil</span>
                </div>
                </button>
                
                <button v-else
                  class="flex items-center gap-1.5 bg-[#193CB8] hover:bg-[#2748c6] text-white px-4 py-2 rounded-md transition-colors">
                  <i class='bx bx-envelope'></i>
                  <span>Contactar</span>
                </button>
                
                <button class="flex items-center gap-1.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 cursor-pointer px-4 py-2 rounded-md transition-colors">
                  <i class='bx bx-share-alt'></i>
                  <span class="hidden sm:inline">Compartir</span>
                </button>
              </div>
            </div>
            
            <!-- Tags/Keywords de la empresa -->
            <div class="mt-4 flex flex-wrap gap-2">
              <span v-for="(tag, index) in company.tags || ['Tecnología', 'Innovación', 'Desarrollo']" :key="index"
                class="bg-blue-50 text-[#193CB8] px-3 py-1 rounded-full text-sm">
                {{ tag }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>