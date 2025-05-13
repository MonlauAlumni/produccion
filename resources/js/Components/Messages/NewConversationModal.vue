<template>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
      <div class="p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Nuevo mensaje</h3>
        
        
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Buscar alumni</label>
          <div class="relative">
            <input
              type="text"
              placeholder="Buscar por nombre..."
              class="w-full px-4 py-2 pr-8 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
              v-model="alumniSearch"
              :disabled="sending"
            />
            <i class='bx bx-search absolute right-3 top-2.5 text-gray-400'></i>
          </div>
        </div>
        
        <div class="mb-4 max-h-48 overflow-y-auto border border-gray-200 rounded-lg">
          <div v-if="loading" class="p-4 text-center text-gray-500">
            <div class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-[#193CB8] mb-2"></div>
            <p>Cargando alumni...</p>
          </div>
          <div v-else-if="filteredAlumni.length === 0" class="p-4 text-center text-gray-500">
            No se encontraron resultados
          </div>
          <button
            v-for="alumni in filteredAlumni"
            :key="alumni.id"
            @click="selectAlumni(alumni)"
            :class="[
              'w-full text-left px-4 py-3 border-b last:border-b-0 border-gray-200 hover:bg-gray-50 transition-colors flex items-center',
              selectedAlumni && selectedAlumni.id === alumni.id ? 'bg-blue-50' : ''
            ]"
            :disabled="sending"
          >
            <div class="relative mr-3">
              <img 
                v-if="alumni.profile && alumni.profile.profile_picture" 
                :src="alumni.profile.profile_picture" 
                alt="User avatar" 
                class="w-10 h-10 rounded-lg object-cover"
              />
              <div v-else class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center text-[#193CB8]">
                <i class='bx bxs-user text-xl'></i>
              </div>
            </div>
            
            <div>
              <h4 class="font-medium text-gray-800">{{ alumni.name }} {{ alumni.last_name_1 }}</h4>
              <p class="text-sm text-gray-500">{{ alumni.profile?.headline || 'Alumni' }}</p>
            </div>
          </button>
        </div>
        
        <div v-if="selectedAlumni" class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
          <textarea
            v-model="newConversationMessage"
            placeholder="Escribe tu mensaje..."
            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#193CB8] resize-none"
            rows="3"
            :disabled="sending"
          ></textarea>
        </div>
        
        <div class="flex justify-end gap-2 mt-6">
          <button 
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            :disabled="sending"
          >
            Cancelar
          </button>
          <button 
            @click="createConversation"
            :disabled="!selectedAlumni || !newConversationMessage.trim() || sending"
            :class="[
              'px-4 py-2 rounded-lg flex items-center gap-2',
              selectedAlumni && newConversationMessage.trim() && !sending
                ? 'bg-[#193CB8] text-white hover:bg-[#142d8c]'
                : 'bg-gray-100 text-gray-400 cursor-not-allowed'
            ]"
          >
            <i class='bx bx-loader-alt animate-spin' v-if="sending"></i>
            <span>{{ sending ? 'Enviando...' : 'Enviar mensaje' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
  
<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import MessageService from '../../services/MessageService'
  
const props = defineProps({
  companyJobs: {
    type: Array,
    default: () => []
  },
  preselectedUserId: {
    type: String,
    default: null
  },
  preselectedJobId: {
    type: String,
    default: null
  },
  preselectedUserName: {
    type: String,
    default: ''
  }
})
  
const emit = defineEmits(['close', 'create'])
  
// Estado
const alumniSearch = ref(props.preselectedUserName || '')
const selectedAlumni = ref(null)
const newConversationMessage = ref('')
const newMessageJobId = ref(props.preselectedJobId || null)
const allAlumni = ref([]) // Almacena todos los alumni cargados una sola vez
const loading = ref(true)
const sending = ref(false)
  
// Computed para filtrar alumni localmente
const filteredAlumni = computed(() => {
  if (!alumniSearch.value.trim()) {
    return allAlumni.value
  }
  
  const searchTerms = alumniSearch.value.toLowerCase().split(' ').filter(term => term.length > 0)
  
  if (searchTerms.length === 0) {
    return allAlumni.value
  }
  
  return allAlumni.value.filter(user => {
    const fullName = `${user.name} ${user.last_name_1 || ''}`.toLowerCase()
    return searchTerms.every(term => fullName.includes(term))
  })
})
  
// Métodos
const selectAlumni = (alumni) => {
  selectedAlumni.value = alumni
  
  // Si hay una oferta seleccionada, crear un mensaje predeterminado
  if (newMessageJobId.value) {
    const selectedJob = props.companyJobs.find(job => job.id.toString() === newMessageJobId.value.toString())
    if (selectedJob) {
      newConversationMessage.value = `Hola, me gustaría hablar contigo sobre la oferta "${selectedJob.title}".`
    }
  }
}

// Variable para evitar múltiples envíos
let isSubmitting = false
  
const createConversation = async () => {
  // Verificar si ya se está enviando o si faltan datos
  if (!selectedAlumni.value || !newConversationMessage.value.trim() || sending.value || isSubmitting) return
  
  // Marcar como enviando para evitar múltiples clics
  sending.value = true
  isSubmitting = true
  
  try {
    // Crear un objeto con los datos necesarios
    const conversationData = {
      recipient: selectedAlumni.value,
      message: newConversationMessage.value.trim(),
      jobId: newMessageJobId.value ? Number(newMessageJobId.value) : null
    }
    
    // Emitir el evento una sola vez
    await emit('create', conversationData)
  } catch (error) {
    console.error('Error al crear conversación:', error)
    // Restablecer el estado en caso de error
    sending.value = false
    isSubmitting = false
    alert('Error al crear la conversación. Por favor, inténtalo de nuevo.')
  }
  // No reseteamos sending.value aquí, ya que el componente se cerrará si todo va bien
}

// Cargar todos los alumni una sola vez al inicio
const loadAllAlumni = async () => {
  try {
    loading.value = true
    const response = await MessageService.searchAlumni('')
    allAlumni.value = response.alumni
    
    // Si hay un usuario preseleccionado, seleccionarlo
    if (props.preselectedUserId) {
      const preselectedUser = allAlumni.value.find(user => 
        user.id.toString() === props.preselectedUserId.toString()
      )
      
      if (preselectedUser) {
        selectedAlumni.value = preselectedUser
        
        // Si hay una oferta seleccionada, crear un mensaje predeterminado
        if (props.preselectedJobId) {
          const selectedJob = props.companyJobs.find(job => job.id.toString() === props.preselectedJobId.toString())
          if (selectedJob) {
            newConversationMessage.value = `Hola, me gustaría hablar contigo sobre la oferta "${selectedJob.title}".`
          }
        }
      }
    }
  } catch (error) {
    console.error('Error al cargar alumni:', error)
  } finally {
    loading.value = false
  }
}

// Cargar todos los alumni al montar el componente
onMounted(() => {
  loadAllAlumni()
})

// Observar cambios en companyJobs para actualizar el mensaje predeterminado si es necesario
watch(() => props.companyJobs, (newJobs) => {
  if (selectedAlumni.value && newMessageJobId.value) {
    const selectedJob = newJobs.find(job => job.id.toString() === newMessageJobId.value.toString())
    if (selectedJob && !newConversationMessage.value) {
      newConversationMessage.value = `Hola, me gustaría hablar contigo sobre la oferta "${selectedJob.title}".`
    }
  }
}, { deep: true })

// Observar cambios en newMessageJobId para actualizar el mensaje predeterminado
watch(newMessageJobId, (newJobId) => {
  if (selectedAlumni.value && newJobId) {
    const selectedJob = props.companyJobs.find(job => job.id.toString() === newJobId.toString())
    if (selectedJob) {
      newConversationMessage.value = `Hola, me gustaría hablar contigo sobre la oferta "${selectedJob.title}".`
    }
  }
})
</script>
  
<style scoped>
::-webkit-scrollbar {
  width: 6px;
}
  
::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}
  
::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 10px;
}
  
::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}
</style>