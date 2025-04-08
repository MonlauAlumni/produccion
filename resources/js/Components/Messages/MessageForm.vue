<template>
  <div class="p-4 border-t border-gray-200">
    <form @submit.prevent="sendMessage" class="flex items-end gap-2">
      <div class="flex-1 relative">
        <textarea
          v-model="messageText"
          placeholder="Escribe un mensaje..."
          class="w-full px-4 py-3 pr-10 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#193CB8] resize-none"
          :rows="textareaRows"
          @input="adjustTextareaHeight"
          @keydown.enter.exact.prevent="sendMessage"
          ref="textarea"
        ></textarea>
        
        <!-- Botones de acción adicionales (emoji, adjuntos, etc.) -->
        <div class="absolute bottom-3 right-3 flex gap-2">
          <button 
            type="button"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <i class='bx bx-smile text-xl'></i>
          </button>
        </div>
      </div>
      
      <button 
        type="submit"
        :disabled="!messageText.trim()"
        :class="[
          'w-12 h-12 rounded-full flex items-center justify-center',
          messageText.trim() 
            ? 'bg-[#193CB8] text-white hover:bg-[#142d8c]' 
            : 'bg-gray-100 text-gray-400 cursor-not-allowed'
        ]"
      >
        <i class='bx bx-send text-xl'></i>
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const emit = defineEmits(['send']);

const messageText = ref('');
const textareaRows = ref(1);
const textarea = ref(null);

const sendMessage = () => {
  if (!messageText.value.trim()) return;
  
  emit('send', messageText.value);
  messageText.value = '';
  textareaRows.value = 1;
  
  // Enfocar el textarea después de enviar
  if (textarea.value) {
    textarea.value.focus();
  }
};

const adjustTextareaHeight = () => {
  const lines = messageText.value.split('\n').length;
  const calculatedRows = Math.min(5, Math.max(1, lines));
  textareaRows.value = calculatedRows;
};
</script>