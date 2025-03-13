<script setup>
  import { ref, defineProps, defineEmits } from "vue";
  import { router } from '@inertiajs/vue3';
  
  const props = defineProps({ 
    isOpen: Boolean,
    slang: String,
    type: String,
  });
  
  const emit = defineEmits(["close", "save"]);
  
  const experience = ref({
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
  
  const closeModal = () => {
    emit("close");
  };
  
  const submitExperience = async () => {
    // Validar campos obligatorios
    if ((props.type === 'work' && (!experience.value.company_name || !experience.value.position)) || 
        (props.type === 'education' && (!experience.value.degree || !experience.value.institution))) {
      alert("Por favor, completa todos los campos obligatorios.");
      return;
    }
  
    if (!props.slang) {
      console.error("Error: slang aún no está disponible");
      alert("Error al obtener la información necesaria. Intenta de nuevo.");
      return;
    }
  
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
    closeModal();
  
    // Enviar los datos al backend (para educación o experiencia laboral)
    try {
      if (props.type === 'work') {
        await router.post(`/perfil/${props.slang}/experiencia`, data);
      } else if (props.type === 'education') {
        await router.post(`/perfil/${props.slang}/educacion`, data);
      }
  
      // Redirigir o hacer cualquier otra acción después de guardar
      router.visit(`/perfil/${props.slang}`);
    } catch (error) {
      console.error("Error al guardar la experiencia o educación", error);
    }
  };
  </script>
  
  <template>
    <div v-if="isOpen" class="fixed inset-0  flex items-center justify-center bg-black/50 min-w-screen z-50">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
        <div class="bg-[#193CB8] px-6 py-4 flex justify-between items-center">
          <h2 class="text-xl font-semibold text-white">
            {{ props.type === 'work' ? 'Añadir Experiencia Laboral' : 'Añadir Formación Académica' }}
          </h2>
          <button @click="closeModal" class="text-white cursor-pointer hover:text-blue-200 transition-colors">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>
     
        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
          <form @submit.prevent="submitExperience" class="space-y-4">
            <!-- Campos para experiencia laboral -->
            <div v-if="props.type === 'work'">
              <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">
                Empresa <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="experience.company_name" 
                id="company_name" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                placeholder="Nombre de la empresa"
              >
            </div>
  
            <div v-if="props.type === 'work'">
              <label for="position" class="block text-sm font-medium text-gray-700 mb-1">
                Puesto <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="experience.position" 
                id="position" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                placeholder="Título del puesto"
              >
            </div>
  
            <!-- Campos para formación académica -->
            <div v-if="props.type === 'education'">
              <label for="institution" class="block text-sm font-medium text-gray-700 mb-1">
                Institución <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="experience.institution" 
                id="institution" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                placeholder="Nombre de la institución educativa"
              >
            </div>
  
            <div v-if="props.type === 'education'">
              <label for="degree" class="block text-sm font-medium text-gray-700 mb-1">
                Título <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="experience.degree" 
                id="degree" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                placeholder="Título o certificación obtenida"
              >
            </div>
  
            <!-- Campos comunes -->
            <div>
              <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                Ubicación
              </label>
              <input 
                v-model="experience.location" 
                id="location" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                placeholder="Ciudad, País"
              >
            </div>
  
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                  Fecha de inicio <span class="text-red-500">*</span>
                </label>
                <input 
                  v-model="experience.start_date" 
                  id="start_date" 
                  type="date" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                >
              </div>
  
              <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                  Fecha de fin
                </label>
                <input 
                  v-model="experience.end_date" 
                  id="end_date" 
                  type="date" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                  :disabled="experience.current"
                >
              </div>
            </div>
  
            <div class="flex items-center">
              <input 
                v-model="experience.current" 
                id="current" 
                type="checkbox" 
                class="h-4 w-4 text-[#193CB8] focus:ring-[#193CB8] border-gray-300 rounded"
              >
              <label for="current" class="ml-2 block text-sm text-gray-700">
                {{ props.type === 'work' ? 'Trabajo actual' : 'En curso' }}
              </label>
            </div>
  
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                Descripción
              </label>
              <textarea 
                v-model="experience.description" 
                id="description" 
                rows="3" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                placeholder="Describe tus responsabilidades o logros"
              ></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
              <button 
                type="button" 
                @click="closeModal" 
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500"
              >
                Cancelar
              </button>
              <button 
                type="submit" 
                class="px-4 py-2 text-sm font-medium text-white bg-[#193CB8] rounded-md hover:bg-[#2748c6] focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
              >
                Guardar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>