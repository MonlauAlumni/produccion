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
    
    const isOpen = ref(false);
    const isLoading = ref(false);
    const successMessage = ref('');
    const errorMessage = ref('');
    
    // Formulario con los datos del estudiante
    const form = ref({
      name: '',
      last_name_1: '',
      last_name_2: '',
      job_title: '',
      location: '',
      description: '',
      email: '',
      phone: '',
      availability: '',
      degree: '',
      graduation_year: '',
      social_links: {
        linkedin: '',
        github: '',
        twitter: '',
        website: ''
      }
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
        job_title: props.profile?.job_title || '',
        location: props.profile?.location || '',
        description: props.profile?.description || '',
        email: props.user.email || '',
        phone: props.profile?.phone || '',
        availability: props.profile?.availability || 'Disponible para ofertas',
        degree: props.profile?.degree || '',
        graduation_year: props.profile?.graduation_year || '',
        social_links: {
          linkedin: props.profile?.social_links?.linkedin || '',
          github: props.profile?.social_links?.github || '',
          twitter: props.profile?.social_links?.twitter || '',
          website: props.profile?.social_links?.website || ''
        }
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
    
    // Método para guardar los cambios
    const saveChanges = () => {
      isLoading.value = true;
      errorMessage.value = '';
      
      router.post(`/perfil/${props.user.slang}/update`, form.value, {
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
      <!-- Overlay del modal -->
      <div v-if="isOpen" 
           class="fixed inset-0 bg-black/75 bg-opacity-50 z-50 flex items-center justify-center p-4 overflow-y-auto"
           @click="closeModal">
        
        <!-- Contenido del modal -->
        <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col"
             @click.stop>
          
          <!-- Cabecera del modal -->
          <div class="bg-gradient-to-r from-[#193CB8] to-[#2748c6] text-white p-6">
            <div class="flex items-center justify-between">
              <h2 class="text-xl font-bold">Editar Perfil</h2>
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
              <!-- Información personal -->
              <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información Personal</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input 
                      v-model="form.name" 
                      type="text" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                      required
                    />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Primer Apellido *</label>
                    <input 
                      v-model="form.last_name_1" 
                      type="text" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                      required
                    />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Segundo Apellido</label>
                    <input 
                      v-model="form.last_name_2" 
                      type="text" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                    />
                  </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título Profesional</label>
                    <input 
                      v-model="form.job_title" 
                      type="text" 
                      placeholder="Ej: Desarrollador Web, Estudiante de DAW..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                    />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                    <input 
                      v-model="form.location" 
                      type="text" 
                      placeholder="Ej: Madrid, España"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                    />
                  </div>
                </div>
              </div>
              
              <!-- Formación académica -->
              <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Formación Académica</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grado/Curso</label>
                    <select 
                      v-model="form.degree" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                    >
                      <option value="" disabled>Seleccionar grado</option>
                      <option v-for="degree in degreeOptions" :key="degree" :value="degree">
                        {{ degree }}
                      </option>
                    </select>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Año de Graduación</label>
                    <select 
                      v-model="form.graduation_year" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                    >
                      <option value="" disabled>Seleccionar año</option>
                      <option v-for="year in graduationYears" :key="year" :value="year">
                        {{ year }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
              
              <!-- Descripción -->
              <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Sobre mí</h3>
                <textarea 
                  v-model="form.description" 
                  rows="5" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                  placeholder="Describe tu perfil profesional, intereses y objetivos..."
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
                      v-model="form.phone" 
                      type="tel" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                    />
                  </div>
                </div>
              </div>
              
              <!-- Disponibilidad -->
              <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Disponibilidad</h3>
                <select 
                  v-model="form.availability" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                >
                  <option v-for="option in availabilityOptions" :key="option" :value="option">
                    {{ option }}
                  </option>
                </select>
              </div>
              
              <!-- Redes sociales -->
              <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Redes Sociales</h3>
                <div class="space-y-4">
                  <div class="flex items-center gap-3">
                    <div class="bg-blue-50 p-2 rounded-full">
                      <i class='bx bxl-linkedin text-[#193CB8] text-xl'></i>
                    </div>
                    <input 
                      v-model="form.social_links.linkedin" 
                      type="url" 
                      placeholder="URL de LinkedIn" 
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                    />
                  </div>
                  
                  <div class="flex items-center gap-3">
                    <div class="bg-blue-50 p-2 rounded-full">
                      <i class='bx bxl-github text-[#193CB8] text-xl'></i>
                    </div>
                    <input 
                      v-model="form.social_links.github" 
                      type="url" 
                      placeholder="URL de GitHub" 
                    
                     
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                    />
                  </div>
                  
                  <div class="flex items-center gap-3">
                    <div class="bg-blue-50 p-2 rounded-full">
                      <i class='bx bxl-twitter text-[#193CB8] text-xl'></i>
                    </div>
                    <input 
                      v-model="form.social_links.twitter" 
                      type="url" 
                      placeholder="URL de Twitter" 
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                    />
                  </div>
                  
                  <div class="flex items-center gap-3">
                    <div class="bg-blue-50 p-2 rounded-full">
                      <i class='bx bx-globe text-[#193CB8] text-xl'></i>
                    </div>
                    <input 
                      v-model="form.social_links.website" 
                      type="url" 
                      placeholder="URL de sitio web personal" 
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                    />
                  </div>
                </div>
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