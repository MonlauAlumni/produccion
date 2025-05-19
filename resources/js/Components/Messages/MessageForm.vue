<template>
  <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
    <form @submit.prevent="sendMessage" class="flex items-end gap-2">
      <div class="flex-1 relative">
        <textarea
          v-model="messageText"
          placeholder="Escribe un mensaje..."
          class="w-full px-4 py-3 pr-10 rounded-lg border border-gray-200 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-[#193CB8] dark:focus:ring-blue-400 resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
          :rows="textareaRows"
          @input="adjustTextareaHeight"
          @keydown.enter.exact.prevent="sendMessage"
          ref="textarea"
        ></textarea>
        
        <!-- Botones de acción adicionales (emoji, adjuntos, etc.) -->
        <div class="absolute bottom-3 right-3 flex gap-2">
          <button 
            type="button"
            class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
          >
            <i class='bx bx-smile text-xl'></i>
          </button>
        </div>
      </div>
      
      <button 
        type="submit"
        :disabled="!messageText.trim() || sending"
        :class="[
          'w-12 h-12 rounded-full flex items-center justify-center transition-all',
          messageText.trim() && !sending
            ? 'bg-[#193CB8] text-white hover:bg-[#142d8c]' 
            : 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed'
        ]"
      >
        <i class='bx bx-send text-xl' v-if="!sending"></i>
        <i class='bx bx-loader-alt animate-spin text-xl' v-else></i>
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
const sending = ref(false);

const sendMessage = async () => {
  if (!messageText.value.trim() || sending.value) return;
  
  sending.value = true;
  
  try {
    await emit('send', messageText.value);
    messageText.value = '';
    textareaRows.value = 1;
    
    // Enfocar el textarea después de enviar
    if (textarea.value) {
      textarea.value.focus();
    }
  } catch (error) {
    console.error('Error al enviar mensaje:', error);
  } finally {
    sending.value = false;
  }
};

const adjustTextareaHeight = () => {
  const lines = messageText.value.split('\n').length;
  const calculatedRows = Math.min(5, Math.max(1, lines));
  textareaRows.value = calculatedRows;
};
</script>