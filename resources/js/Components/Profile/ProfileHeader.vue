<script setup>
import SocialButton from '../SocialButton.vue'
import { computed, ref, onMounted } from 'vue'
import EditModal from './EditModal.vue'

const editModal = ref(null)
const isOpen = ref(false)



const openEditModal = () => {
  if (editModal.value) {
    editModal.value.openModal() // Invocando openModal desde EditModal
  }
}




const props = defineProps({
  user: Object,
  profile: Object,
  isSameUser: Boolean,
  openEditModal: Function,
});


</script>
<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6">
      <div class="flex flex-row">
        <div class="flex flex-col items-center md:flex-row md:items-start md:space-x-6">
          <div class="h-24 w-24 rounded-full bg-gray-200 overflow-hidden">
            <img src="" alt="Profile" class="h-full w-full object-cover" />
          </div>
          <div class="mt-4 text-center md:mt-0 md:text-left">
            <h1 class="text-2xl font-bold">{{ user.name }} {{ user.last_name_1 }} {{ user.last_name_2 }}</h1>
            <p class="mt-1">Exalumno de Desarrollo de Aplicaciones Web</p>
            <div class="mt-4 flex items-center justify-center space-x-2 md:justify-start">
              <SocialButton icon="Github">GitHub</SocialButton>
              <SocialButton icon="Linkedin">LinkedIn</SocialButton>
              <SocialButton icon="MessageCircle">Mensaje</SocialButton>
            </div>
          </div>
        </div>
        <div class="flex w-full justify-end mt-6 md:mt-0">
          <div>
            <button @click="props.openEditModal" v-if="isSameUser"
              class="cursor-pointer text-blue-500 hover:text-blue-600">
              <i class='bx bx-pencil'></i>
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>

</template>
