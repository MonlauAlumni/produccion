<template>
    <div class="bg-white border-t border-gray-200 p-4">
      <form @submit.prevent="sendMessage" class="flex items-end gap-2">
        <div class="flex-1 relative overflow-hidden">
          <textarea
            v-model="newMessage"
            placeholder="Escribe un mensaje..."
            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#193CB8] resize-none"
            rows="1"
            ref="messageInput"
            @input="autoResizeTextarea"
            @keydown.enter.exact.prevent="sendMessage"
          ></textarea>
          
          <div class="absolute right-3 bottom-3 flex items-center gap-2">
            <button 
              type="button"
              class="text-gray-400 hover:text-gray-600"
            >
              <i class='bx bx-smile text-xl'></i>
            </button>
            <button 
              type="button"
              class="text-gray-400 hover:text-gray-600"
            >
              <i class='bx bx-paperclip text-xl'></i>
            </button>
          </div>
        </div>
        
        <button 
          type="submit"
          :disabled="!newMessage.trim()"
          :class="[
            'p-3 rounded-full flex items-center justify-center',
            newMessage.trim() 
              ? 'bg-[#193CB8] text-white hover:bg-[#142d8c]' 
              : 'bg-gray-100 text-gray-400 cursor-not-allowed'
          ]"
        >
        <div class="">
          <i class='bx bx-send text-xl '></i>
        </div>
        </button>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  
  const newMessage = ref('')
  const messageInput = ref(null)
  
  const autoResizeTextarea = () => {
    if (!messageInput.value) return
    
    messageInput.value.style.height = 'auto'
    messageInput.value.style.height = `${messageInput.value.scrollHeight}px`
  }
  
  const sendMessage = () => {
    if (!newMessage.value.trim()) return
    
    const message = newMessage.value
    newMessage.value = ''
    
    // Ajustar altura del textarea
    if (messageInput.value) {
      messageInput.value.style.height = 'auto'
    }
    
    // Emitir evento con el mensaje
    emit('send', message)
  }
  
  const emit = defineEmits(['send'])
  </script>