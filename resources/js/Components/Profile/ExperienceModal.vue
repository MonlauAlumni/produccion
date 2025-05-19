<script setup>
  import { ref, defineProps, defineEmits, watch } from "vue";
  import { router } from '@inertiajs/vue3';
  
  const props = defineProps({ 
    isOpen: Boolean,
    slang: String,
    type: String,
    item: Object, // Nuevo prop para recibir el item a editar
    mode: {
      type: String,
      default: 'create' // 'create' o 'edit'
    }
  });
  
  const emit = defineEmits(["close", "save"]);
  
  const isLoading = ref(false);
  const errorMessage = ref('');
  
  const experience = ref({
    id: null,
    user_id: null,
    company_name: "", // Para experiencia laboral
    position: "", // Para experiencia laboral
    institution: "", // Para educación
    degree: "", // Para educación
    start_date: "",
    end_date: "",
    description: "",
    location: "",
    current: false
  });
  
  // Observar cambios en el item para cargar los datos cuando se abre en modo edición
  watch(() => props.item, (newItem) => {
    if (newItem && props.mode === 'edit') {
      experience.value = {
        id: newItem.id,
        user_id: newItem.user_id,
        company_name: newItem.company_name || "",
        position: newItem.position || "",
        institution: newItem.institution || "",
        degree: newItem.degree || "",
        start_date: newItem.start_date || "",
        end_date: newItem.end_date || "",
        description: newItem.description || "",
        location: newItem.location || "",
        current: newItem.current || false
      };
    }
  }, { immediate: true });
  
  // También observar cuando se abre el modal
  watch(() => props.isOpen, (isOpen) => {
    if (isOpen && props.item && props.mode === 'edit') {
      experience.value = {
        id: props.item.id,
        user_id: props.item.user_id,
        company_name: props.item.company_name || "",
        position: props.item.position || "",
        institution: props.item.institution || "",
        degree: props.item.degree || "",
        start_date: props.item.start_date || "",
        end_date: props.item.end_date || "",
        description: props.item.description || "",
        location: props.item.location || "",
        current: props.item.current || false
      };
    }
  });
  
  const closeModal = () => {
    emit("close");
    // Resetear el formulario al cerrar
    resetForm();
  };
  
  const resetForm = () => {
    if (props.mode !== 'edit') {
      experience.value = {
        id: null,
        user_id: null,
        company_name: "",
        position: "",
        institution: "",
        degree: "",
        start_date: "",
        end_date: "",
        description: "",
        location: "",
        current: false
      };
    }
    errorMessage.value = '';
  };
  
  const submitExperience = async () => {
    // Validar campos obligatorios
    if ((props.type === 'work' && (!experience.value.company_name || !experience.value.position || !experience.value.start_date)) || 
        (props.type === 'education' && (!experience.value.degree || !experience.value.institution || !experience.value.start_date))) {
      errorMessage.value = "Por favor, completa todos los campos obligatorios.";
      return;
    }
  
    if (!props.slang) {
      console.error("Error: slang aún no está disponible");
      errorMessage.value = "Error al obtener la información necesaria. Intenta de nuevo.";
      return;
    }
  
    isLoading.value = true;
  
    // Preparar los datos según el tipo
    const data = {
      user_id: experience.value.user_id,
      start_date: experience.value.start_date,
      end_date: experience.value.current ? null : experience.value.end_date,
      description: experience.value.description,
      location: experience.value.location,
      current: experience.value.current
    };
  
    // Si es experiencia laboral
    if (props.type === 'work') {
      data.company_name = experience.value.company_name;
      data.position = experience.value.position;
    }
  
    // Si es formación académica
    if (props.type === 'education') {
      data.degree = experience.value.degree;
      data.institution = experience.value.institution;
    }
  
    // Emitir los datos para guardarlos en el componente padre
    emit("save", data);
  
    try {
      // Determinar si es creación o actualización
      if (props.mode === 'edit' && experience.value.id) {
        // Actualizar registro existente
        if (props.type === 'work') {
          await router.put(`/perfil/${props.slang}/experiencia/${experience.value.id}`, data, {
            onSuccess: () => {
              closeModal();
              router.reload();
            },
            onError: (errors) => {
              console.error("Errores:", errors);
              errorMessage.value = "Ha ocurrido un error al actualizar. Intenta de nuevo.";
            }
          });
        } else if (props.type === 'education') {
          await router.put(`/perfil/${props.slang}/educacion/${experience.value.id}`, data, {
            onSuccess: () => {
              closeModal();
              router.reload();
            },
            onError: (errors) => {
              console.error("Errores:", errors);
              errorMessage.value = "Ha ocurrido un error al actualizar. Intenta de nuevo.";
            }
          });
        }
      } else {
        // Crear nuevo registro
        if (props.type === 'work') {
          await router.post(`/perfil/${props.slang}/experiencia`, data, {
            onSuccess: () => {
              closeModal();
              router.reload();
            },
            onError: (errors) => {
              console.error("Errores:", errors);
              errorMessage.value = "Ha ocurrido un error al guardar. Intenta de nuevo.";
            }
          });
        } else if (props.type === 'education') {
          await router.post(`/perfil/${props.slang}/educacion`, data, {
            onSuccess: () => {
              closeModal();
              router.reload();
            },
            onError: (errors) => {
              console.error("Errores:", errors);
              errorMessage.value = "Ha ocurrido un error al guardar. Intenta de nuevo.";
            }
          });
        }
      }
    } catch (error) {
      console.error("Error al guardar la experiencia o educación", error);
      errorMessage.value = "Ha ocurrido un error al guardar. Intenta de nuevo.";
    } finally {
      isLoading.value = false;
    }
  };
  
  // Método para manejar la tecla Escape
  const handleEscape = (e) => {
    if (e.key === 'Escape' && props.isOpen) {
      closeModal();
    }
  };
  
  // Añadir y eliminar event listener para la tecla Escape
  if (typeof window !== 'undefined') {
    document.addEventListener('keydown', handleEscape);
  }
</script>
  
<template>
  <!-- Overlay del modal con animación de fade -->
  <transition name="fade">
    <div v-if="isOpen" 
         class="fixed inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto"
         @click="closeModal">
      
      <!-- Contenido del modal con animación de slide -->
      <transition name="slide-up">
        <div v-if="isOpen" 
             class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-xl mx-auto overflow-hidden transform transition-all"
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
                <i :class="['bx text-xl', props.type === 'work' ? 'bx-briefcase' : 'bx-graduation']"></i>
                {{ props.mode === 'edit' 
                  ? (props.type === 'work' ? 'Editar Experiencia Laboral' : 'Editar Formación Académica')
                  : (props.type === 'work' ? 'Añadir Experiencia Laboral' : 'Añadir Formación Académica') 
                }}
              </h2>
              <button @click="closeModal" class="text-white flex cursor-pointer hover:text-gray-200 transition-colors bg-white/20 rounded-full p-1.5">
                <i class='bx bx-x text-xl'></i>
              </button>
            </div>
          </div>
          
          <!-- Mensaje de error -->
          <transition name="fade">
            <div v-if="errorMessage" class="bg-red-100 dark:bg-red-900 border-b border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 flex items-center justify-between">
              <span class="flex items-center">
                <i class='bx bx-error-circle mr-2 text-xl'></i>
                {{ errorMessage }}
              </span>
              <button @click="errorMessage = ''" class="text-red-700 dark:text-red-300">
                <i class='bx bx-x'></i>
              </button>
            </div>
          </transition>
          
          <!-- Cuerpo del modal con scroll -->
          <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
            <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4 border border-blue-100 dark:border-blue-700 flex items-start gap-3">
              <i :class="['bx text-[#193CB8] dark:text-blue-200 text-xl mt-0.5', props.type === 'work' ? 'bx-info-circle' : 'bx-book-open']"></i>
              <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ props.mode === 'edit'
                  ? (props.type === 'work' 
                    ? 'Actualiza los detalles de tu experiencia laboral.' 
                    : 'Actualiza los detalles de tu formación académica.')
                  : (props.type === 'work' 
                    ? 'Añade tu experiencia laboral para mostrar tu trayectoria profesional a las empresas.' 
                    : 'Añade tu formación académica para mostrar tus estudios y certificaciones a las empresas.')
                }}
              </p>
            </div>
            
            <form @submit.prevent="submitExperience" class="space-y-4">
              <!-- Campos para experiencia laboral -->
              <div v-if="props.type === 'work'" class="space-y-4">
                <div class="group">
                  <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 group-focus-within:text-[#193CB8]">
                    Empresa <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500 group-focus-within:text-[#193CB8]">
                      <i class='bx bx-building'></i>
                    </div>
                    <input 
                      v-model="experience.company_name" 
                      id="company_name" 
                      type="text" 
                      class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] dark:bg-gray-700 dark:text-gray-100 transition-all"
                      placeholder="Nombre de la empresa"
                    >
                  </div>
                </div>
  
                <div class="group">
                  <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 group-focus-within:text-[#193CB8]">
                    Puesto <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500 group-focus-within:text-[#193CB8]">
                      <i class='bx bx-id-card'></i>
                    </div>
                    <input 
                      v-model="experience.position" 
                      id="position" 
                      type="text" 
                      class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] dark:bg-gray-700 dark:text-gray-100 transition-all"
                      placeholder="Título del puesto"
                    >
                  </div>
                </div>
              </div>
  
              <!-- Campos para formación académica -->
              <div v-if="props.type === 'education'" class="space-y-4">
                <div class="group">
                  <label for="institution" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 group-focus-within:text-[#193CB8]">
                    Institución <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500 group-focus-within:text-[#193CB8]">
                      <i class='bx bx-building-house'></i>
                    </div>
                    <input 
                      v-model="experience.institution" 
                      id="institution" 
                      type="text" 
                      class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] dark:bg-gray-700 dark:text-gray-100 transition-all"
                      placeholder="Nombre de la institución educativa"
                    >
                  </div>
                </div>
  
                <div class="group">
                  <label for="degree" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 group-focus-within:text-[#193CB8]">
                    Título <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500 group-focus-within:text-[#193CB8]">
                      <i class='bx bx-certification'></i>
                    </div>
                    <input 
                      v-model="experience.degree" 
                      id="degree" 
                      type="text" 
                      class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] dark:bg-gray-700 dark:text-gray-100 transition-all"
                      placeholder="Título o certificación obtenida"
                    >
                  </div>
                </div>
              </div>
  
              <div class="grid grid-cols-2 gap-4">
                <div class="group">
                  <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 group-focus-within:text-[#193CB8]">
                    Fecha de inicio <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500 group-focus-within:text-[#193CB8]">
                      <i class='bx bx-calendar'></i>
                    </div>
                    <input 
                      v-model="experience.start_date" 
                      id="start_date" 
                      type="date" 
                      class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] dark:bg-gray-700 dark:text-gray-100 transition-all"
                    >
                  </div>
                </div>
  
                <div class="group">
                  <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 group-focus-within:text-[#193CB8]">
                    Fecha de fin
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500 group-focus-within:text-[#193CB8]">
                      <i class='bx bx-calendar-check'></i>
                    </div>
                    <input 
                      v-model="experience.end_date" 
                      id="end_date" 
                      type="date" 
                      class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] dark:bg-gray-700 dark:text-gray-100 transition-all"
                      :disabled="experience.current"
                      :class="{ 'bg-gray-100 dark:bg-gray-800': experience.current }"
                    >
                  </div>
                </div>
              </div>
  
              <div class="flex items-center bg-blue-50 dark:bg-blue-900 p-3 rounded-lg">
                <input 
                  v-model="experience.current" 
                  id="current" 
                  type="checkbox" 
                  class="h-4 w-4 text-[#193CB8] focus:ring-[#193CB8] border-gray-300 dark:border-gray-600 rounded"
                >
                <label for="current" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                  {{ props.type === 'work' ? 'Actualmente trabajo aquí' : 'Actualmente estudio aquí' }}
                </label>
              </div>
  
              <div class="group">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 group-focus-within:text-[#193CB8]">
                  Descripción
                </label>
                <textarea 
                  v-model="experience.description" 
                  id="description" 
                  rows="4" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] dark:bg-gray-700 dark:text-gray-100 transition-all"
                  :placeholder="props.type === 'work' 
                    ? 'Describe tus responsabilidades, logros y proyectos relevantes...' 
                    : 'Describe lo que aprendiste, proyectos realizados o reconocimientos...'"
                ></textarea>
                <div class="flex justify-end mt-1 text-xs text-gray-500 dark:text-gray-400">
                  {{ experience.description.length }}/500 caracteres
                </div>
              </div>
              
              <div class="flex justify-end space-x-3 mt-6">
                <button 
                  type="button" 
                  @click="closeModal" 
                  class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors flex items-center gap-1"
                >
                  <i class='bx bx-x'></i>
                  Cancelar
                </button>
                <button 
                  type="submit" 
                  class="px-4 py-2 text-sm font-medium text-white bg-[#193CB8] dark:bg-blue-900 rounded-lg hover:bg-[#2748c6] dark:hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-[#193CB8] transition-colors flex items-center gap-1"
                  :disabled="isLoading"
                >
                  <i v-if="isLoading" class='bx bx-loader-alt animate-spin'></i>
                  <i v-else class='bx bx-save'></i>
                  <span>{{ isLoading ? 'Guardando...' : (props.mode === 'edit' ? 'Actualizar' : 'Guardar') }}</span>
                </button>
              </div>
            </form>
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
