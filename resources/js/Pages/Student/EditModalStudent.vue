<script setup>
  import { ref, onMounted, computed } from 'vue';
  import { router } from '@inertiajs/vue3';
  
  const props = defineProps({
    user: {
      type: Object,
      required: true
    },
    profile: {
      type: Object,
      required: true
    }
  });
  
  const file = ref(null);
  const fileName = ref(props.profile.cv_path ? props.profile.cv_path.split('/').pop() : "Subir CV (PDF)");
  
  const isOpen = ref(false);
  const isLoading = ref(false);
  const successMessage = ref('');
  const errorMessage = ref('');
  const activeTab = ref('personal');
  
  // Formulario con los datos del estudiante
  const form = ref({
    name: '',
    last_name_1: '',
    last_name_2: '',
    location: '',
    description: '',
    job_title: '',
    email: '',
    phone: '',
    availability: '',
    degree: '',
    graduation_year: '',
    linkedin: '',
    github: '',
    website: '',
  });
  
  // Opciones para disponibilidad
  const availabilityOptions = [
    'Disponible para ofertas',
    'Abierto a oportunidades',
    'No disponible actualmente',
    'Solo ofertas remotas',
    'Solo ofertas a tiempo parcial'
  ];
  
  // Opciones para grados/cursos
  const degreeOptions = [
    'Desarrollo de Aplicaciones Web',
    'Desarrollo de Aplicaciones Multiplataforma',
    'Administración de Sistemas Informáticos en Red',
    'Ingeniería Informática',
    'Otro'
  ];
  
  // Inicializar el formulario con los datos actuales
  onMounted(() => {
    form.value = {
      name: props.user.name || '',
      last_name_1: props.user.last_name_1 || '',
      last_name_2: props.user.last_name_2 || '',
      location: props.profile?.location || '',
      description: props.profile?.description || '',
      email: props.user.email || '',
      phone: props.profile?.phone || '',
      availability: props.profile?.availability || 'Disponible para ofertas',
      degree: props.profile?.degree || '',
      job_title: props.profile?.job_title || '',
      graduation_year: props.profile?.graduation_year || '',
      linkedin: props.profile?.linkedin || '',
      github: props.profile?.github || '',
      website: props.profile?.website || '',
    };
  });
  
  const handleFileUpload = (event) => {
    const selectedFile = event.target.files[0];
    if (selectedFile) {
      file.value = selectedFile;
      fileName.value = selectedFile.name;
    }
  };
  
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
  
  // Método para guardar los cambios
  const saveChanges = () => {
    isLoading.value = true;
    errorMessage.value = '';
  
    // Crear un nuevo FormData
    const formData = new FormData();
    
    // Añadir todos los campos del formulario al FormData
    Object.keys(form.value).forEach(key => {
      formData.append(key, form.value[key] || '');
    });
  
    // Añadir el archivo CV si existe
    if (file.value) {
      // Usar 'cv' como nombre del campo para que coincida con lo que espera el controlador
      formData.append('cv', file.value);
    }
  
    // Enviar la solicitud al servidor
    router.post(`/perfil/${props.user.profile.slang}`, formData, {
      onSuccess: () => {
          closeModal();

      },
      onError: (errors) => {
        errorMessage.value = 'Ha ocurrido un error al actualizar el perfil';
        console.error(errors);
      },
      onFinish: () => {
        isLoading.value = false;
      },
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
  };
  
  // Computed para verificar si el formulario es válido
  const isFormValid = computed(() => {
    return form.value.name && form.value.last_name_1;
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
  
  // Generar años para el selector de año de graduación
  const graduationYears = computed(() => {
    const currentYear = new Date().getFullYear();
    const years = [];
    
    for (let i = currentYear - 10; i <= currentYear + 5; i++) {
      years.push(i);
    }
    
    return years;
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
                  <i class='bx bx-user-circle'></i>
                  Editar Perfil
                </h2>
                <button @click="closeModal" class="text-white flex cursor-pointer hover:text-gray-200 transition-colors bg-white/20 rounded-full p-1.5">
                  <i class='bx bx-x text-xl'></i>
                </button>
              </div>
            </div>
            
            <!-- Navegación por pestañas -->
            <div class="bg-gray-50 border-b border-gray-200">
              <div class="flex overflow-x-auto justify-between hide-scrollbar">
                <button 
                  @click="activeTab = 'personal'" 
                  :class="[
                    'px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors border-b-2',
                    activeTab === 'personal' 
                      ? 'border-[#193CB8] text-[#193CB8]' 
                      : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
                  ]"
                >
                  <i class='bx bx-user mr-1'></i>
                  Información Personal
                </button>
               
                <button 
                  @click="activeTab = 'academic'" 
                  :class="[
                    'px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors border-b-2',
                    activeTab === 'academic' 
                      ? 'border-[#193CB8] text-[#193CB8]' 
                      : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'
                  ]"
                >
                  <i class='bx bxs-graduation mr-1'></i>
                  Formación Académica
                </button>
                
                <button 
                  @click="activeTab = 'contact'" 
                  :class="[
                    'px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors border-b-2',
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
                    'px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors border-b-2',
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
                <!-- Información personal -->
                <div v-show="activeTab === 'personal'">
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Nombre <span class="text-red-500">*</span>
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-user'></i>
                        </div>
                        <input 
                          v-model="form.name" 
                          type="text" 
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                          required
                        />
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Primer Apellido <span class="text-red-500">*</span>
                      </label>
                      <input 
                        v-model="form.last_name_1" 
                        type="text" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        required
                      />
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Segundo Apellido
                      </label>
                      <input 
                        v-model="form.last_name_2" 
                        type="text" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                      />
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Título Profesional
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-briefcase'></i>
                        </div>
                        <input 
                          v-model="form.job_title" 
                          type="text" 
                          placeholder="Ej: Desarrollador Web, Estudiante de DAW..."
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        />
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Ubicación
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
                        />
                      </div>
                    </div>
                  </div>
                  
                  <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">CV</label>
                    <div class="relative">
                      <label class="flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-[#193CB8] transition-colors bg-gray-50 hover:bg-blue-50/50">
                        <div class="flex items-center gap-2 text-gray-600 hover:text-[#193CB8]">
                          <i class='bx bx-upload text-xl'></i>
                          <span>{{ fileName }}</span>
                        </div>
                        <input type="file" accept=".pdf" class="hidden" @change="handleFileUpload" />
                      </label>
                    </div>
                  </div>
                  
                  <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sobre mí</label>
                    <div class="relative">
                      <textarea 
                        v-model="form.description" 
                        rows="5" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        placeholder="Describe tu perfil profesional, intereses y objetivos..."
                      ></textarea>
                      <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                        {{ form.description.length }}/500
                      </div>
                    </div>
                  </div>
                  
                  <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Disponibilidad</label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class='bx bx-time'></i>
                      </div>
                      <select 
                        v-model="form.availability" 
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all appearance-none bg-no-repeat bg-[right_0.5rem_center]"
                        style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-size: 1em;"
                      >
                        <option v-for="option in availabilityOptions" :key="option" :value="option">
                          {{ option }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
                
                <!-- Formación académica -->
                <div v-show="activeTab === 'academic'">
                  <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-100 flex items-start gap-3">
                    <i class='bx bx-info-circle text-[#193CB8] text-xl mt-0.5'></i>
                    <p class="text-sm text-gray-700">
                      La información académica que proporciones será visible en tu perfil y ayudará a las empresas a conocer tu formación.
                    </p>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Grado/Curso
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-book'></i>
                        </div>
                        <select 
                          v-model="form.degree" 
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all appearance-none bg-no-repeat bg-[right_0.5rem_center]"
                          style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-size: 1em;"
                        >
                          <option value="" disabled>Seleccionar grado</option>
                          <option v-for="degree in degreeOptions" :key="degree" :value="degree">
                            {{ degree }}
                          </option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Año de Graduación
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-calendar'></i>
                        </div>
                        <select 
                          v-model="form.graduation_year" 
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all appearance-none bg-no-repeat bg-[right_0.5rem_center]"
                          style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-size: 1em;"
                        >
                          <option value="" disabled>Seleccionar año</option>
                          <option v-for="year in graduationYears" :key="year" :value="year">
                            {{ year }}
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Información de contacto -->
                <div v-show="activeTab === 'contact'">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Email
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-envelope'></i>
                        </div>
                        <input 
                          v-model="form.email" 
                          type="email" 
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        />
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Teléfono
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#193CB8]">
                          <i class='bx bx-phone'></i>
                        </div>
                        <input 
                          v-model="form.phone" 
                          type="tel" 
                          class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        />
                      </div>
                    </div>
                  </div>
                  
                  
                </div>
                
                <!-- Redes sociales -->
                <div v-show="activeTab === 'social'">
                  <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-100 flex items-start gap-3">
                    <i class='bx bx-info-circle text-[#193CB8] text-xl mt-0.5'></i>
                    <p class="text-sm text-gray-700">
                      Añade tus perfiles profesionales para que las empresas puedan conocer más sobre tu trabajo y proyectos.
                    </p>
                  </div>
                  
                  <div class="space-y-4">
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        LinkedIn
                      </label>
                      <div class="flex items-center gap-3">
                        <div class="bg-blue-50 p-2 rounded-full flex-shrink-0">
                          <i class='bx bxl-linkedin text-[#193CB8] text-xl'></i>
                        </div>
                        <input 
                          v-model="form.linkedin" 
                          type="url" 
                          placeholder="https://linkedin.com/in/tu-perfil" 
                          class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        />
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        GitHub
                      </label>
                      <div class="flex items-center gap-3">
                        <div class="bg-blue-50 p-2 rounded-full flex-shrink-0">
                          <i class='bx bxl-github text-[#193CB8] text-xl'></i>
                        </div>
                        <input 
                          v-model="form.github" 
                          type="url" 
                          placeholder="https://github.com/tu-usuario" 
                          class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        />
                      </div>
                    </div>
                    
                    <div class="group">
                      <label class="block text-sm font-medium text-gray-700 mb-1 group-focus-within:text-[#193CB8]">
                        Sitio Web Personal
                      </label>
                      <div class="flex items-center gap-3">
                        <div class="bg-blue-50 p-2 rounded-full flex-shrink-0">
                          <i class='bx bx-globe text-[#193CB8] text-xl'></i>
                        </div>
                        <input 
                          v-model="form.website" 
                          type="url" 
                          placeholder="https://tu-sitio-web.com" 
                          class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] transition-all"
                        />
                      </div>
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