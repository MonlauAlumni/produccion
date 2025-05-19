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
  const activeTab = ref('basic');
  
  // Formulario con los datos de la empresa
  const form = ref({
    company_name: '',
    company_phone: '',
    description: '',
    sector: '',
    size: '',
    location: '',
    linkedin: '',
    twitter: '',
    facebook: '',
    instagram: '',
    website: '',
    founded_year: '',
    email: '',
    tags: []
  });
  
  // Opciones para industrias
  const industries = [
    'Tecnología',
    'Finanzas',
    'Automoción',
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
      tags: props.company.tags || []
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
      form.value.tags = [...(form.value.tags || []), newTag.value.trim()];
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
          closeModal();
   
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
    <!-- Overlay del modal con animación de fade -->
    <transition name="fade">
      <div v-if="isOpen" 
           class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto"
           @click="closeModal">
        
        <!-- Contenido del modal con animación de slide -->
        <transition name="slide-up">
          <div v-if="isOpen" 
               class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col"
               @click.stop>
            
            <!-- Cabecera del modal con diseño mejorado -->
            <div class="relative">
              <!-- Banner decorativo -->
              <div class="h-16 bg-gradient-to-r from-[#193CB8] to-[#2748c6] relative overflow-hidden">
                <div class="absolute inset-0 opacity-20">
                  <div class="absolute top-0 left-0 w-40 h-40 bg-white/20 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                  <div class="absolute bottom-0 right-0 w-32 h-32 bg-white/10 rounded-full translate-x-1/3 translate-y-1/3"></div>
                </div>
              </div>
              
              <!-- Título y botón de cierre -->
              <div class="absolute top-0 inset-x-0 h-16 flex items-center justify-between px-6">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                  <i class='bx bx-building-house'></i>
                  Editar Perfil de Empresa
                </h2>
                <button @click="closeModal" class="text-white cursor-pointer hover:text-gray-200 transition-colors bg-white/20 rounded-full p-1.5">
                  <i class='bx bx-x text-xl'></i>
                </button>
              </div>
            </div>
            
            <!-- Navegación por pestañas -->
           <div class="bg-gray-50 border-b border-gray-200 w-full">
  <div class="flex overflow-x-auto hide-scrollbar w-full">
    <button 
      @click="activeTab = 'basic'" 
      :class="[
        'flex-grow px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors border-b-2 text-center',
        activeTab === 'basic' 
          ? 'border-[#193CB8] text-[#193CB8]' 
          : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
      ]"
    >
      <i class='bx bx-building mr-1'></i>
      Información Básica
    </button>
    
    <button 
      @click="activeTab = 'description'" 
      :class="[
        'flex-grow px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors border-b-2 text-center',
        activeTab === 'description' 
          ? 'border-[#193CB8] text-[#193CB8]' 
          : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
      ]"
    >
      <i class='bx bx-info-circle mr-1'></i>
      Descripción
    </button>
    
    <button 
      @click="activeTab = 'contact'" 
      :class="[
        'flex-grow px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors border-b-2 text-center',
        activeTab === 'contact' 
          ? 'border-[#193CB8] text-[#193CB8]' 
          : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
      ]"
    >
      <i class='bx bx-envelope mr-1'></i>
      Contacto
    </button>
    
    <button 
      @click="activeTab = 'social'" 
      :class="[
        'flex-grow px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors border-b-2 text-center',
        activeTab === 'social' 
          ? 'border-[#193CB8] text-[#193CB8]' 
          : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
      ]"
    >
      <i class='bx bx-link mr-1'></i>
      Redes Sociales
    </button>
  </div>
</div>

            
            <!-- Mensajes de éxito o error -->
            <transition name="fade">
              <div v-if="successMessage" class="absolute top-16 inset-x-0 bg-green-100 border-b border-green-400 text-green-700 px-4 py-3 flex items-center justify-between">
                <span class="flex items-center">
                  <i class='bx bx-check-circle mr-2 text-xl'></i>
                  {{ successMessage }}
                </span>
                <button @click="successMessage = ''" class="text-green-700">
                  <i class='bx bx-x'></i>
                </button>
              </div>
            </transition>
            
            <transition name="fade">
              <div v-if="errorMessage" class="absolute top-16 inset-x-0 bg-red-100 border-b border-red-400 text-red-700 px-4 py-3 flex items-center justify-between">
                <span class="flex items-center">
                  <i class='bx bx-error-circle mr-2 text-xl'></i>
                  {{ errorMessage }}
                </span>
                <button @click="errorMessage = ''" class="text-red-700">
                  <i class='bx bx-x'></i>
                </button>
              </div>
            </transition>
            
            <!-- Cuerpo del modal con scroll -->
            <div class="flex-1 overflow-y-auto p-6">
              <!-- Formulario -->
              <form @submit.prevent="saveChanges" class="space-y-6">
                <!-- Información básica -->
                <div v-show="activeTab === 'basic'">
                  <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-100 flex items-start gap-3">
                    <i class='bx bx-info-circle text-[#193CB8] text-xl mt-0.5'></i>
                    <p class="text-sm text-gray-700">
                      La información básica de tu empresa es fundamental para que los candidatos puedan conocerte mejor.
                    </p>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="group md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Nombre de la Empresa <span class="text-red-500">*</span>
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-building'></i>
                        </div>
                        <input 
                          v-model="form.company_name" 
                          type="text" 
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                          required
                        />
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Industria/Sector <span class="text-red-500">*</span>
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-category'></i>
                        </div>
                        <select 
                          v-model="form.sector" 
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all appearance-none bg-no-repeat bg-[right_0.5rem_center]"
                          style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-size: 1em;"
                          required
                        >
                          <option value="" disabled>Seleccionar industria</option>
                          <option v-for="industry in industries" :key="industry" :value="industry">
                            {{ industry }}
                          </option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Tamaño de la Empresa
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-group'></i>
                        </div>
                        <select 
                          v-model="form.size" 
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all appearance-none bg-no-repeat bg-[right_0.5rem_center]"
                          style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-size: 1em;"
                        >
                          <option value="" disabled>Seleccionar tamaño</option>
                          <option v-for="size in companySizes" :key="size" :value="size">
                            {{ size }}
                          </option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Ubicación <span class="text-red-500">*</span>
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-map-pin'></i>
                        </div>
                        <input 
                          v-model="form.location" 
                          type="text" 
                          placeholder="Ej: Madrid, España"
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                          required
                        />
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Año de Fundación
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-calendar'></i>
                        </div>
                        <input 
                          v-model="form.founded_year" 
                          type="number" 
                          min="1900" 
                          :max="new Date().getFullYear()" 
                          placeholder="Ej: 2010"
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        />
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Sitio Web
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-globe'></i>
                        </div>
                        <input 
                          v-model="form.website" 
                          type="url" 
                          placeholder="https://ejemplo.com" 
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        />
                      </div>
                    </div>
                  </div>
                  
                </div>
                
                <!-- Descripción -->
                <div v-show="activeTab === 'description'">
                  <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-100 flex items-start gap-3">
                    <i class='bx bx-bulb text-[#193CB8] text-xl mt-0.5'></i>
                    <div class="text-sm text-gray-700">
                      <p class="mb-2">Una buena descripción de empresa debe incluir:</p>
                      <ul class="list-disc pl-5 space-y-1">
                        <li>Misión y visión de la empresa</li>
                        <li>Cultura y valores</li>
                        <li>Qué hace la empresa y a qué se dedica</li>
                        <li>Beneficios de trabajar en la empresa</li>
                      </ul>
                    </div>
                  </div>
                  
                  <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción de la empresa</label>
                    <textarea 
                      v-model="form.description" 
                      rows="12" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                      placeholder="Describe tu empresa, misión, valores y cultura..."
                    ></textarea>
                    <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                      {{ form.description.length }}/1000
                    </div>
                  </div>
                </div>
                
                <!-- Información de contacto -->
                <div v-show="activeTab === 'contact'">
                  <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-100 flex items-start gap-3">
                    <i class='bx bx-info-circle text-[#193CB8] text-xl mt-0.5'></i>
                    <p class="text-sm text-gray-700">
                      Esta información de contacto será visible en tu perfil de empresa y será utilizada por los candidatos para comunicarse contigo.
                    </p>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Email de contacto
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-envelope'></i>
                        </div>
                        <input 
                          v-model="form.email" 
                          type="email" 
                          placeholder="contacto@empresa.com"
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        />
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Teléfono de contacto
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-phone'></i>
                        </div>
                        <input 
                          v-model="form.company_phone" 
                          type="tel" 
                          placeholder="+34 XXX XXX XXX"
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        />
                      </div>
                    </div>
                  </div>
                
                </div>
                
                <!-- Redes sociales -->
                <div v-show="activeTab === 'social'">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8] flex items-center gap-1">
                        <i class='bx bxl-linkedin text-[#193CB8]'></i>
                        LinkedIn
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-link'></i>
                        </div>
                        <input 
                          v-model="form.linkedin" 
                          type="url" 
                          placeholder="https://linkedin.com/company/tu-empresa"
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        />
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8] flex items-center gap-1">
                        <i class='bx bxl-facebook text-[#193CB8]'></i>
                        Facebook
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-link'></i>
                        </div>
                        <input 
                          v-model="form.facebook" 
                          type="url" 
                          placeholder="https://facebook.com/tu-empresa"
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        />
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8] flex items-center gap-1">
                        <i class='bx bxl-instagram text-[#193CB8]'></i>
                        Instagram
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-link'></i>
                        </div>
                        <input 
                          v-model="form.instagram" 
                          type="url" 
                          placeholder="https://instagram.com/tu-empresa"
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        />
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8] flex items-center gap-1">
                        <i class='bx bxl-twitter text-[#193CB8]'></i>
                        Twitter
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-link'></i>
                        </div>
                        <input 
                          v-model="form.twitter" 
                          type="url" 
                          placeholder="https://twitter.com/tu-empresa"
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        />
                      </div>
                    </div>
                  </div>
                  
                  <div class="mt-6 bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <h3 class="text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                      <i class='bx bx-bulb text-[#193CB8]'></i>
                      Consejo
                    </h3>
                    <p class="text-sm text-gray-600">
                      Mantener tus redes sociales actualizadas aumenta la visibilidad de tu empresa y ayuda a los candidatos a conocer mejor tu cultura y valores.
                    </p>
                  </div>
                </div>
                
                <!-- Tags/Keywords -->
                <div v-show="activeTab === 'tags'">
                  <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-100 flex items-start gap-3">
                    <i class='bx bx-info-circle text-[#193CB8] text-xl mt-0.5'></i>
                    <p class="text-sm text-gray-700">
                      Los tags o palabras clave ayudan a que tu empresa sea más fácil de encontrar por los candidatos que buscan oportunidades en tu sector.
                    </p>
                  </div>
                  
                  <div class="flex flex-wrap gap-2 mb-4 min-h-[100px] p-4 border border-gray-200 rounded-lg bg-white">
                    <div 
                      v-for="(tag, index) in form.tags" 
                      :key="index" 
                      class="bg-blue-50 text-[#193CB8] px-3 py-1.5 rounded-full text-sm flex items-center gap-1 transition-all hover:bg-blue-100"
                    >
                      <i class='bx bx-purchase-tag-alt'></i>
                      {{ tag }}
                      <button 
                        type="button"
                        @click="removeTag(index)" 
                        class="ml-1 text-[#193CB8] hover:text-red-500 transition-colors"
                      >
                        <i class='bx bx-x'></i>
                      </button>
                    </div>
                    
                    <div v-if="!form.tags || form.tags.length === 0" class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                      No hay tags añadidos
                    </div>
                  </div>
                  
                  <div class="flex gap-2">
                    <div class="relative flex-1">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class='bx bx-purchase-tag'></i>
                      </div>
                      <input 
                        v-model="newTag" 
                        type="text" 
                        placeholder="Añadir nuevo tag (ej: tecnología, desarrollo, remoto)" 
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        @keyup.enter.prevent="addTag"
                      />
                    </div>
                    <button 
                      type="button"
                      @click="addTag" 
                      class="bg-[#193CB8] hover:bg-[#2748c6] text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-1"
                    >
                      <i class='bx bx-plus'></i>
                      <span>Añadir</span>
                    </button>
                  </div>
                  <p class="text-sm text-gray-500 mt-2 flex items-center gap-1">
                    <i class='bx bx-info-circle'></i>
                    Presiona Enter o haz clic en el botón para añadir un tag
                  </p>
                  
                  <div class="mt-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Tags sugeridos para tu sector</h3>
                    <div class="flex flex-wrap gap-2">
                      <button 
                        v-for="tag in ['Tecnología', 'Desarrollo', 'Remoto', 'Startup', 'Innovación', 'Software', 'Web', 'Mobile', 'AI', 'Cloud']" 
                        :key="tag"
                        type="button"
                        @click="newTag = tag; addTag()"
                        class="bg-white text-gray-600 px-3 py-1 rounded-full text-sm border border-gray-200 hover:border-[#193CB8] hover:text-[#193CB8] transition-colors"
                      >
                        {{ tag }}
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            
            <!-- Pie del modal con botones de acción -->
            <div class="bg-gray-50 p-6 border-t border-gray-200">
              <div class="flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-3">
                <div class="flex items-center text-sm text-gray-500">
                  <i class='bx bx-info-circle mr-1'></i>
                  <span>Los campos marcados con <span class="text-red-500">*</span> son obligatorios</span>
                </div>
                
                <div class="flex justify-end gap-3">
                  <button 
                    @click="closeModal" 
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors flex items-center gap-1"
                  >
                    <i class='bx bx-x'></i>
                    Cancelar
                  </button>
                  <button 
                    @click="saveChanges" 
                    :disabled="!isFormValid || isLoading" 
                    :class="[
                      'px-4 py-2 rounded-lg text-white transition-colors flex items-center gap-2',
                      isFormValid && !isLoading ? 'bg-[#193CB8] hover:bg-[#2748c6]' : 'bg-gray-400 cursor-not-allowed'
                    ]"
                  >
                    <i v-if="isLoading" class='bx bx-loader-alt animate-spin'></i>
                    <i v-else class='bx bx-save'></i>
                    <span>{{ isLoading ? 'Guardando...' : 'Guardar Cambios' }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </template>
  
  <style scoped>
  /* Animaciones */
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.3s ease;
  }
  
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
  
  .slide-up-enter-active,
  .slide-up-leave-active {
    transition: all 0.3s ease;
  }
  
  .slide-up-enter-from,
  .slide-up-leave-to {
    opacity: 0;
    transform: translateY(30px);
  }
  
  /* Animación para el loader */
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  
  .animate-spin {
    animation: spin 1s linear infinite;
  }
  
  /* Ocultar scrollbar pero mantener funcionalidad */
  .hide-scrollbar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
  }
  
  .hide-scrollbar::-webkit-scrollbar {
    display: none;  /* Chrome, Safari and Opera */
  }
  
  /* Estilos para inputs */
  input:focus, select:focus, textarea:focus {
    border-color: #193CB8;
    box-shadow: 0 0 0 2px rgba(25, 60, 184, 0.1);
  }
  
  /* Estilos para checkbox */
  input[type="checkbox"] {
    accent-color: #193CB8;
  }
  </style>