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
    forceFormData: true, // importante para asegurar que Inertia no serializa como JSON
    onSuccess: (page) => {
      // Puedes acceder a los datos actualizados desde el objeto `page.props`
      const newLogoUrl = page.props.company?.logo_url;

      if (newLogoUrl) {
        props.company.logo_url = newLogoUrl;
      }
    },
  });
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
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 w-full">
      <div class="p-6">
        <div class="flex flex-col md:flex-row md:items-center gap-6">
          <!-- Logo de la empresa -->
          <div class="relative h-26 w-28 rounded-lg bg-white dark:bg-gray-700 shadow-md overflow-hidden group border-4 border-white dark:border-gray-600">
            <img 
              :src="company.profile_picture" 
              :alt="company.name + ' logo'" 
              class="h-26 w-28 rounded-lg" 
            />
            
            <!-- Hover para subir nueva imagen (solo para administradores) -->
            <div v-if="isAdmin" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-60 opacity-0 group-hover:opacity-100 transition-opacity">
              <label class="cursor-pointer text-white bg-[#193CB8] dark:bg-blue-900 hover:bg-[#2748c6] dark:hover:bg-blue-800 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                <i class="bx bx-upload mr-1"></i> Cambiar
                <input type="file" class="hidden" @change="uploadProfileImage" accept="image/*">
              </label>
            </div>
          </div>
          
          <!-- Información de la empresa -->
          <div class="flex-1">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
              <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-200">{{ company.company_name }}</h1>
                <div class="flex flex-wrap items-center gap-3 mt-2 text-gray-600 dark:text-gray-400">
                  <div class="flex items-center gap-1">
                    <i class='bx bx-buildings text-[#193CB8] dark:text-blue-200'></i>
                    <span>{{ industry }}</span>
                  </div>
                  <div class="w-1 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                  <div class="flex items-center gap-1">
                    <i class='bx bx-group text-[#193CB8] dark:text-blue-200'></i>
                    <span>{{ companySize }}</span>
                  </div>
                  <div class="w-1 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                  <div class="flex items-center gap-1">
                    <i class='bx bx-map-pin text-[#193CB8] dark:text-blue-200'></i>
                    <span>{{ company.location || 'Ubicación no especificada' }}</span>
                  </div>
                </div>
              </div>
              
              <!-- Botones de acción -->
              <div class="flex items-center gap-3 mt-4 md:mt-0">
                <button v-if="isAdmin" @click="props.openEditModal" 
                  class="flex items-center cursor-pointer gap-1.5 bg-[#193CB8] dark:bg-blue-900 hover:bg-[#2748c6] dark:hover:bg-blue-800 text-white px-3 py-2 rounded-md transition-colors">
                  <div class="flex justify-center items-center w-30 gap-3"><i class='bx bx-pencil'></i>
                  <span>Editar Perfil</span>
                </div>
                </button>
                
                
              </div>
            </div>
            
            
          </div>
        </div>
      </div>
    </div>
  </template>