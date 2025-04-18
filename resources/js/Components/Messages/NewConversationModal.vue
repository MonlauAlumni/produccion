<template>
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-800 mb-4">Nuevo mensaje</h3>
          
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar oferta (opcional)</label>
            <select 
              v-model="newMessageJobId"
              class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
            >
              <option :value="null">Sin oferta relacionada</option>
              <option v-for="job in companyJobs" :key="job.id" :value="job.id">
                {{ job.title }}
              </option>
            </select>
          </div>
          
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar alumni</label>
            <div class="relative">
              <input
                type="text"
                placeholder="Buscar por nombre..."
                class="w-full px-4 py-2 pr-8 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
                v-model="alumniSearch"
              />
              <i class='bx bx-search absolute right-3 top-2.5 text-gray-400'></i>
            </div>
          </div>
          
          <div class="mb-4 max-h-48 overflow-y-auto border border-gray-200 rounded-lg">
            <div v-if="filteredAlumni.length === 0" class="p-4 text-center text-gray-500">
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
                <p class="text-sm text-gray-500">{{ alumni.profile.headline || 'Alumni' }}</p>
              </div>
            </button>
          </div>
          
          <div v-if="selectedAlumni" class="mb-4 ">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
            <textarea
              v-model="newConversationMessage"
              placeholder="Escribe tu mensaje..."
              class="w-full px-4 py-3  rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#193CB8] resize-none"
              rows="3"
            ></textarea>
          </div>
          
          <div class="flex justify-end gap-2 mt-6">
            <button 
              @click="$emit('close')"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Cancelar
            </button>
            <button 
              @click="createConversation"
              :disabled="!selectedAlumni || !newConversationMessage.trim()"
              :class="[
                'px-4 py-2 rounded-lg',
                selectedAlumni && newConversationMessage.trim()
                  ? 'bg-[#193CB8] text-white hover:bg-[#142d8c]'
                  : 'bg-gray-100 text-gray-400 cursor-not-allowed'
              ]"
            >
              Enviar mensaje
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue'
  
  const props = defineProps({
    companyJobs: {
      type: Array,
      default: () => []
    }
  })
  
  const emit = defineEmits(['close', 'create'])
  
  // Estado
  const alumniSearch = ref('')
  const selectedAlumni = ref(null)
  const newConversationMessage = ref('')
  const newMessageJobId = ref(null)
  
  // Datos simulados de alumni
  const filteredAlumni = computed(() => {
    // En producción, esto sería una llamada a la API
    const alumni = [
      { 
        id: 1, 
        name: 'Juan', 
        last_name_1: 'Pérez',
        profile: { 
          headline: 'Desarrollador Full Stack',
          profile_picture: null
        } 
      },
      { 
        id: 2, 
        name: 'María', 
        last_name_1: 'García',
        profile: { 
          headline: 'UX/UI Designer',
          profile_picture: null
        } 
      },
      { 
        id: 3, 
        name: 'Carlos', 
        last_name_1: 'López',
        profile: { 
          headline: 'Data Scientist',
          profile_picture: null
        } 
      }
    ]
    
    if (!alumniSearch.value) return alumni
    
    const query = alumniSearch.value.toLowerCase()
    return alumni.filter(a => 
      `${a.name} ${a.last_name_1}`.toLowerCase().includes(query) ||
      (a.profile.headline && a.profile.headline.toLowerCase().includes(query))
    )
  })
  
  // Métodos
  const selectAlumni = (alumni) => {
    selectedAlumni.value = alumni
  }
  
  const createConversation = () => {
    if (!selectedAlumni.value || !newConversationMessage.value.trim()) return
    
    emit('create', {
      recipient: selectedAlumni.value,
      message: newConversationMessage.value,
      jobId: newMessageJobId.value
    })
  }
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