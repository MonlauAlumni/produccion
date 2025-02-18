<script setup>
import { ref, defineProps, defineExpose, watchEffect } from 'vue'
import { router } from '@inertiajs/vue3'

const isOpen = ref(false)
const props = defineProps({
  user: Object,
  profile: Object,
  isCompany: Boolean
})


const createSlang = (user) => {
  if(props.isCompany === true) {
    return props.profile.slang
  }
  const { name, last_name_1, last_name_2 } = user
  let slang = `${name}-${last_name_1}`
  if (last_name_2) slang += `-${last_name_2}`
  return slang.toLowerCase().replace(/\s+/g, '-')
}

const oldSlang = ref(createSlang(props.user))
const newSlang = ref(oldSlang.value)

const form = ref({})

watchEffect(() => {
if(props.isCompany === false) {
  form.value = {
    name: props.user.name || '',
    last_name_1: props.user.last_name_1 || '',
    last_name_2: props.user.last_name_2 || '',
    description: props.profile?.description || '',
  
  }
} else if(props.isCompany === true) {
  form.value = {
    name: props.profile.company_name || '',
    description: props.profile?.description || '',
    
   
  }

}
 
  
})

const handleSubmit =  () => {
    console.log(props.user)
    newSlang.value = createSlang(form.value)
   router.put(`/perfil/${oldSlang.value}`, form.value)

    isOpen.value = false

 
    router.visit(`/perfil/${newSlang.value}`)

 

  
}


const openModal = () => (isOpen.value = true)
const closeModal = () => (isOpen.value = false)

defineExpose({ openModal })
</script>


<template>
  <div v-if="isOpen" class="fixed inset-0 bg-[rgba(0,0,0,0.5)] flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
      <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-white">Editar Perfil</h2>
        <button @click="closeModal" class="text-white hover:text-blue-200 transition-colors">
          <i class='bx bx-x'></i>
        </button>
      </div>
   
      <div class="modal-content p-6 space-y-4 max-h-96 overflow-y-auto">
        <form @submit.prevent="handleSubmit">
          <div class="space-y-2">
            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input v-model="form.name" id="nombre" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>

          <!-- Condicional para mostrar los apellidos solo si no es empresa -->
          <div v-if="!isCompany" class="space-y-2">
            <label for="apellido1" class="block text-sm font-medium text-gray-700">Primer Apellido</label>
            <input v-model="form.last_name_1" id="apellido1" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>

          <div v-if="!isCompany" class="space-y-2">
            <label for="apellido2" class="block text-sm font-medium text-gray-700">Segundo Apellido</label>
            <input v-model="form.last_name_2" id="apellido2" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>

          <!-- Descripción (siempre visible) -->
          <div class="space-y-2">
            <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
            <textarea v-model="form.description" id="descripcion" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
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

  
 