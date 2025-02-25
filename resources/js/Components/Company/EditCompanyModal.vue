<script setup>
    import { ref, defineProps, defineExpose, watchEffect } from 'vue'
    import { router } from '@inertiajs/vue3'
    
    const isOpen = ref(false)
    const props = defineProps({
      company: Object,
      profile: Object
    })
    
    const createSlang = (company) => {
      return company.slang
    }
    
    const oldSlang = ref(createSlang(props.company))
    const newSlang = ref(oldSlang.value)
    
    const form = ref({})
    
    watchEffect(() => {
      form.value = {
        name: props.company.name || '',
        industry: props.company.industry || '',
        description: props.profile?.description || '',
        website: props.company.website || '',
        linkedin: props.company.linkedin || '',
      }
    })
    
    const handleSubmit = () => {
      console.log(props.company)
      newSlang.value = createSlang(form.value)
      router.put(`/empresa/${oldSlang.value}`, form.value)
    
      isOpen.value = false
    
      router.visit(`/empresa/${newSlang.value}`)
    }
    
    const openModal = () => (isOpen.value = true)
    const closeModal = () => (isOpen.value = false)
    
    defineExpose({ openModal })
    </script>
    
    <template>
      <div v-if="isOpen" class="fixed inset-0 bg-[rgba(0,0,0,0.5)] flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
          <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-white">Editar Perfil de Empresa</h2>
            <button @click="closeModal" class="text-white hover:text-blue-200 transition-colors">
              <i class='bx bx-x'></i>
            </button>
          </div>
       
          <div class="modal-content p-6 space-y-4 max-h-96 overflow-y-auto">
            <form @submit.prevent="handleSubmit">
              <div class="space-y-2">
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Empresa</label>
                <input v-model="form.name" id="nombre" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <div class="space-y-2">
                <label for="industry" class="block text-sm font-medium text-gray-700">Industria</label>
                <input v-model="form.industry" id="industry" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <div class="space-y-2">
                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea v-model="form.description" id="descripcion" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
              </div>
              <div class="space-y-2">
                <label for="website" class="block text-sm font-medium text-gray-700">Sitio Web</label>
                <input v-model="form.website" id="website" type="url" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <div class="space-y-2">
                <label for="linkedin" class="block text-sm font-medium text-gray-700">LinkedIn</label>
                <input v-model="form.linkedin" id="linkedin" type="url" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              
              <div class="flex justify-end space-x-3 mt-6">
                <button @click="closeModal" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 cursor-pointer focus:ring-gray-500">
                  Cancelar
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md cursor-pointer hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                  Guardar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </template>