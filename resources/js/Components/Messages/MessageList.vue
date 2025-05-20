<template>
  <div 
    ref="messagesContainer"
    class="flex-1 p-4 overflow-y-auto flex flex-col-reverse bg-gray-50 dark:bg-gray-900"
  >
    <!-- Loader para cargar más mensajes -->
    <div v-if="loading" class="py-4 text-center">
      <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-[#193CB8] dark:border-blue-400"></div>
    </div>
    
    <!-- Botón para cargar más mensajes -->
    <div v-if="hasMoreMessages && !loading" class="text-center py-2">
      <button 
        @click="$emit('load-more')"
        class="px-4 py-1 text-sm text-[#193CB8] dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900 rounded-full"
      >
        Cargar mensajes anteriores
      </button>
    </div>
    
    <!-- Mensajes -->
    <div class="space-y-4">
      <MessageItem 
        v-for="(message, index) in messages" 
        :key="message.id"
        :message="message"
        :showAvatar="shouldShowAvatar(message, index)"
        :showDate="shouldShowDate(message, index)"
        :currentUser="currentUser"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import MessageItem from './MessageItem.vue';

const props = defineProps({
  messages: {
    type: Array,
    required: true
  },
  currentUser: {
    type: Object,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  hasMoreMessages: {
    type: Boolean,
    default: false
  }
});

defineEmits(['load-more']);

const messagesContainer = ref(null);

// Determinar si se debe mostrar el avatar para un mensaje
const shouldShowAvatar = (message, index) => {
  // Mostrar avatar si es el primer mensaje o si el mensaje anterior es de otro usuario
  if (index === props.messages.length - 1) return true;
  
  const nextMessage = props.messages[index + 1];
  return message.user_id !== nextMessage.user_id;
};

// Determinar si se debe mostrar la fecha para un mensaje
const shouldShowDate = (message, index) => {
  // Mostrar fecha si es el primer mensaje o si hay un cambio de día entre mensajes
  if (index === props.messages.length - 1) return true;
  
  const nextMessage = props.messages[index + 1];
  const messageDate = new Date(message.created_at).toDateString();
  const nextMessageDate = new Date(nextMessage.created_at).toDateString();
  
  return messageDate !== nextMessageDate;
};

// Scroll al final cuando se cargan mensajes nuevos
watch(() => props.messages, (newMessages, oldMessages) => {
  if (newMessages.length > oldMessages.length && 
      newMessages[newMessages.length - 1]?.id !== oldMessages[oldMessages.length - 1]?.id) {
    setTimeout(() => {
      if (messagesContainer.value) {
        messagesContainer.value.scrollTop = 0;
      }
    }, 100);
  }
}, { deep: true });

onMounted(() => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = 0;
  }
});
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

/* Dark mode scrollbar */
.dark ::-webkit-scrollbar-track {
  background: #1f2937;
}

.dark ::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.dark ::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}
</style>