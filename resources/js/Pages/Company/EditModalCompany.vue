<script setup>
import { ref, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  company: {
    type: Object,
    required: true
  },
  profile: {
    type: Object,
    required: true
  },
  isCompany: {
    type: Boolean,
    default: true
  }
});

const isOpen = ref(false);
const isLoading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

// Formulario con los datos de la empresa
const form = ref({
  company_name:  '',
    company_phone:  '',
    description: '',
    sector: '',
    size: '',
    location: '',
    linkedin:  '',
    twitter:  '',
    facebook:  '',
    instagram:  '',
    website:  '',
    founded_year:  '',
    email: '',
});

// Opciones para industrias
const industries = [
  'Tecnología',
  'Finanzas',
  'Salud',
  'Educación',
  'Marketing',
  'Consultoría',
  'Retail',
  'Manufactura',
  'Construcción',
  'Otros'
];

// Opciones para tamaño de empresa
const companySizes = [
  'Startup (1-10)',
  'Pequeña (11-50)',
  'Mediana (51-200)',
  'Grande (201-1000)',
  'Corporación (1000+)'
];

// Tag temporal para añadir
const newTag = ref('');

// Inicializar el formulario con los datos actuales
onMounted(() => {
  form.value = {
    company_name: props.company.company_name || '',
    company_phone: props.company.company_phone || '',
    description: props.company.description || '',
    sector: props.company.sector || '',
    size: props.company.size || '',
    location: props.company.location || '',
    linkedin: props.company.linkedin || '',
    twitter: props.company.twitter || '',
    facebook: props.company.facebook || '',
    instagram: props.company.instagram || '',
    website: props.company.website || '',
    founded_year: props.company.founded_year || '',
    email: props.company.email || '',

  };
});

// Método para abrir el modal
const openModal = () => {
  isOpen.value = true;
  document.body.classList.add('overflow-hidden');
};

// Método para cerrar el modal
const closeModal = () => {
  isOpen.value = false;
  document.body.classList.remove('overflow-hidden');
  successMessage.value = '';
  errorMessage.value = '';
};

// Método para añadir un tag
const addTag = () => {
  if (newTag.value.trim() && !form.value.tags.includes(newTag.value.trim())) {
    form.value.tags.push(newTag.value.trim());
    newTag.value = '';
  }
};

// Método para eliminar un tag
const removeTag = (index) => {
  form.value.tags.splice(index, 1);
};

// Método para guardar los cambios
const saveChanges = () => {
  isLoading.value = true;
  errorMessage.value = '';
  
  router.post(`/empresa/${props.company.slang}/update`, form.value, {
    onSuccess: () => {
      successMessage.value = 'Perfil actualizado correctamente';
      setTimeout(() => {
        closeModal();
      }, 2000);
    },
    onError: (errors) => {
      errorMessage.value = 'Ha ocurrido un error al actualizar el perfil';
      console.error(errors);
    },
    onFinish: () => {
      isLoading.value = false;
    }
  });
};

// Computed para verificar si el formulario es válido
const isFormValid = computed(() => {
  return form.value.company_name && form.value.sector && form.value.location;
});

// Método para manejar la tecla Escape
const handleEscape = (e) => {
  if (e.key === 'Escape' && isOpen.value) {
    closeModal();
  }
};

// Añadir y eliminar event listener para la tecla Escape
onMounted(() => {
  document.addEventListener('keydown', handleEscape);
  
  return () => {
    document.removeEventListener('keydown', handleEscape);
  };
});

// Exponer métodos para el componente padre
defineExpose({
  openModal
});
</script>

<template>
  <!-- Overlay del modal -->
  <div v-if="isOpen" 
       class="fixed inset-0 bg-black/75 bg-opacity-50 z-50 flex items-center justify-center p-4 overflow-y-auto"
       @click="closeModal">
    
    <!-- Contenido del modal -->
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col"
         @click.stop>
      
      <!-- Cabecera del modal -->
      <div class="bg-[#193CB8] text-white p-6">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold">Editar Perfil de Empresa</h2>
          <button @click="closeModal" class="text-white cursor-pointer hover:text-gray-200 transition-colors">
            <i class='bx bx-x text-2xl'></i>
          </button>
        </div>
      </div>
      
      <!-- Cuerpo del modal con scroll -->
      <div class="flex-1 overflow-y-auto p-6">
        <!-- Mensajes de éxito o error -->
        <div v-if="successMessage" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
          <span class="block sm:inline">{{ successMessage }}</span>
        </div>
        
        <div v-if="errorMessage" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
          <span class="block sm:inline">{{ errorMessage }}</span>
        </div>
        
        <!-- Formulario -->
        <form @submit.prevent="saveChanges" class="space-y-6">
          <!-- Información básica -->
          <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Información Básica</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Empresa *</label>
                <input 
                  v-model="form.company_name" 
                  type="text" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                  required
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Industria/Sector *</label>
                <select 
                  v-model="form.sector" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                  required
                >
                  <option value="" disabled>Seleccionar industria</option>
                  <option v-for="industry in industries" :key="industry" :value="industry">
                    {{ industry }}
                  </option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tamaño de la Empresa</label>
                <select 
                  v-model="form.size" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                >
                  <option value="" disabled>Seleccionar tamaño</option>
                  <option v-for="size in companySizes" :key="size" :value="size">
                    {{ size }}
                  </option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación *</label>
                <input 
                  v-model="form.location" 
                  type="text" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                  required
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Año de Fundación</label>
                <input 
                  v-model="form.founded_year" 
                  type="number" 
                  min="1900" 
                  :max="new Date().getFullYear()" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sitio Web</label>
                <input 
                  v-model="form.website" 
                  type="url" 
                  placeholder="https://ejemplo.com" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                />
              </div>
            </div>
          </div>
          
          <!-- Descripción -->
          <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Descripción</h3>
            <textarea 
              v-model="form.description" 
              rows="5" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
              placeholder="Describe tu empresa, misión, valores y cultura..."
            ></textarea>
          </div>
          
          <!-- Información de contacto -->
          <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Información de Contacto</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input 
                  v-model="form.email" 
                  type="email" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input 
                  v-model="form.company_phone" 
                  type="tel" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                />
              </div>
            </div>
          </div>
     
          <div>
             <h3 class="text-lg font-semibold text-gray-800 mb-4">Redes Sociales</h3>

             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <div class="flex items-center gap-1 ">
                  <i class='bx bxl-linkedin text-[#193CB8] mb-1'></i>
                  <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn</label>
              </div>
                <input 
                  v-model="form.linkedin" 
                  type="linkedin" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                />
              </div>
              <div>
                <div class="flex items-center gap-1">
                  <i class='bx bxl-facebook text-[#193CB8] mb-1'></i>

                <label class="block text-sm font-medium text-gray-700 mb-1">Facebook</label>
                </div>
                <input 
                  v-model="form.facebook" 
                  type="facebook" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                />
              </div>
              <div>
                <div class="flex items-center gap-1">
                  <i class='bx bxl-instagram text-[#193CB8] mb-1'></i>

                <label class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
                </div>
                <input 
                  v-model="form.instagram" 
                  type="instagram" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                />
              </div>
              <div>
                
                <div class="flex items-center gap-1">
                  <i class='bx bxl-twitter text-[#193CB8] mb-1'></i>

                <label class="block text-sm font-medium text-gray-700 mb-1">Twitter</label>
                </div>
                <input 
                  v-model="form.twitter" 
                  type="twitter" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                />
              </div>
              </div>  
          </div>
          
          <!-- Tags/Keywords -->
          <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tags/Keywords</h3>
            <div class="flex flex-wrap gap-2 mb-3">
              <div 
                v-for="(tag, index) in form.tags" 
                :key="index" 
                class="bg-blue-50 text-[#193CB8] px-3 py-1 rounded-full text-sm flex items-center"
              >
                {{ tag }}
                <button 
                  type="button"
                  @click="removeTag(index)" 
                  class="ml-2 text-[#193CB8] hover:text-red-500"
                >
                  <i class='bx bx-x'></i>
                </button>
              </div>
            </div>
            
            <div class="flex gap-2">
              <input 
                v-model="newTag" 
                type="text" 
                placeholder="Añadir nuevo tag" 
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                @keyup.enter.prevent="addTag"
              />
              <button 
                type="button"
                @click="addTag" 
                class="bg-[#193CB8] hover:bg-[#2748c6] text-white px-4 py-2 rounded-md transition-colors"
              >
                <i class='bx bx-plus'></i>
              </button>
            </div>
            <p class="text-sm text-gray-500 mt-2">Presiona Enter o haz clic en el botón para añadir un tag</p>
          </div>
        </form>
      </div>
      
      <!-- Pie del modal con botones de acción -->
      <div class="bg-gray-50 p-6 border-t border-gray-200">
        <div class="flex justify-end gap-3">
          <button 
            @click="closeModal" 
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition-colors"
          >
            Cancelar
          </button>
          <button 
            @click="saveChanges" 
            :disabled="!isFormValid || isLoading" 
            :class="[
              'px-4 py-2 rounded-md text-white transition-colors flex items-center gap-2',
              isFormValid && !isLoading ? 'bg-[#193CB8] hover:bg-[#2748c6]' : 'bg-gray-400 cursor-not-allowed'
            ]"
          >
            <i v-if="isLoading" class='bx bx-loader-alt animate-spin'></i>
            <span>{{ isLoading ? 'Guardando...' : 'Guardar Cambios' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animación para el loader */
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>