<script setup>
    import { ref, defineProps, defineEmits } from "vue";
    import { router } from '@inertiajs/vue3';
    
    const props = defineProps({
      isOpen: Boolean,
      slang: String,
      type: String,
      item: Object
    });
    
    const emit = defineEmits(["close"]);
    
    const isLoading = ref(false);
    const errorMessage = ref('');
    
    const closeModal = () => {
      emit("close");
      errorMessage.value = '';
    };
    
    const confirmDelete = async () => {
      if (!props.item || !props.item.id) {
        errorMessage.value = "No se puede eliminar este elemento.";
        return;
      }
    
      isLoading.value = true;
    
      try {
        if (props.type === 'work') {
          await router.delete(`/perfil/${props.slang}/experiencia/${props.item.id}`, {
            onSuccess: () => {
              closeModal();
              router.reload();
            },
            onError: (errors) => {
              console.error("Errores:", errors);
              errorMessage.value = "Ha ocurrido un error al eliminar. Intenta de nuevo.";
            }
          });
        } else if (props.type === 'education') {
          await router.delete(`/perfil/${props.slang}/educacion/${props.item.id}`, {
            onSuccess: () => {
              closeModal();
              router.reload();
            },
            onError: (errors) => {
              console.error("Errores:", errors);
              errorMessage.value = "Ha ocurrido un error al eliminar. Intenta de nuevo.";
            }
          });
        }
      } catch (error) {
        console.error("Error al eliminar:", error);
        errorMessage.value = "Ha ocurrido un error al eliminar. Intenta de nuevo.";
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
                 class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-auto overflow-hidden transform transition-all"
                 @click.stop>
              
              <!-- Cabecera del modal con diseño mejorado -->
              <div class="relative">
                <!-- Banner decorativo -->
                <div class="h-16 bg-gradient-to-r from-red-600 to-red-500 relative overflow-hidden">
                  <div class="absolute inset-0 opacity-20">
                    <div class="absolute top-0 left-0 w-40 h-40 bg-white/20 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute bottom-0 right-0 w-32 h-32 bg-white/10 rounded-full translate-x-1/3 translate-y-1/3"></div>
                  </div>
                </div>
                
                <!-- Título y botón de cierre -->
                <div class="absolute top-0 inset-x-0 h-16 flex items-center justify-between px-6">
                  <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class='bx bx-trash'></i>
                    Confirmar eliminación
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
              
              <!-- Cuerpo del modal -->
              <div class="p-6 space-y-5">
                <div class="flex items-center justify-center">
                  <div class="w-16 h-16 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center text-red-500 dark:text-red-300">
                    <i class='bx bx-trash text-3xl'></i>
                  </div>
                </div>
                
                <div class="text-center">
                  <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-2">
                    ¿Estás seguro de que quieres eliminar esto?
                  </h3>
                  <p class="text-gray-500 dark:text-gray-400">
                    {{ props.type === 'work' 
                      ? `Vas a eliminar la experiencia laboral en "${props.item?.company_name}" como "${props.item?.position}".` 
                      : `Vas a eliminar la formación académica en "${props.item?.institution}" como "${props.item?.degree}".`
                    }}
                  </p>
                  <p class="text-gray-500 dark:text-gray-400 mt-2">
                    Esta acción no se puede deshacer.
                  </p>
                </div>
                
                <div class="flex justify-center space-x-3 mt-6">
                  <button 
                    type="button" 
                    @click="closeModal" 
                    class="px-4 py-2 text-sm cursor-pointer font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors flex items-center gap-1"
                  >
                    <i class='bx bx-x'></i>
                    Cancelar
                  </button>
                  <button 
                    type="button"
                    @click="confirmDelete"
                    class="px-4 py-2 text-sm cursor-pointer font-medium text-white bg-red-600 dark:bg-red-700 rounded-lg hover:bg-red-700 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors flex items-center gap-1"
                    :disabled="isLoading"
                  >
                    <i v-if="isLoading" class='bx bx-loader-alt animate-spin'></i>
                    <i v-else class='bx bx-trash'></i>
                    <span>{{ isLoading ? 'Eliminando...' : 'Eliminar' }}</span>
                  </button>
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
    </style>
