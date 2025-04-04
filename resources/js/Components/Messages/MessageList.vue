<template>
    <div 
      ref="messagesContainer"
      class="flex-1 overflow-y-auto p-6 bg-gray-50 space-y-4"
    >
      <div v-if="loading" class="flex justify-center items-center h-32">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#193CB8]"></div>
      </div>
      
      <template v-else>
        <!-- Fecha de inicio de conversación -->
        <div class="flex justify-center">
          <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
            {{ formatFullDate(conversation.created_at) }}
          </span>
        </div>
        
        <!-- Mensaje relacionado con la oferta (si existe) -->
        <RelatedJobCard 
          v-if="conversation.related_job" 
          :job="conversation.related_job" 
          @view="$emit('view-job-offer', conversation.related_job.id)"
        />
        
        <!-- Mensajes de la conversación -->
        <MessageItem 
          v-for="message in messages" 
          :key="message.id"
          :message="message"
        />
        
        <!-- Indicador de "escribiendo" -->
        <div v-if="isTyping" class="flex justify-start">
          <div class="bg-white rounded-lg p-3 shadow-sm rounded-bl-none">
            <div class="flex space-x-1">
              <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
              <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
              <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, watch, nextTick } from 'vue'
  import RelatedJobCard from './RelatedJobCard.vue'
  import MessageItem from './MessageItem.vue'
  
  const props = defineProps({
    conversation: {
      type: Object,
      required: true
    },
    messages: {
      type: Array,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    },
    isTyping: {
      type: Boolean,
      default: false
    }
  })
  
  defineEmits(['view-job-offer'])
  
  const messagesContainer = ref(null)
  
  const formatFullDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('es-ES', { 
      day: 'numeric', 
      month: 'long', 
      year: 'numeric' 
    })
  }
  
  const scrollToBottom = () => {
    nextTick(() => {
      if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
      }
    })
  }
  
  // Scroll al final cuando cambian los mensajes o el estado de escritura
  watch(() => props.messages.length, scrollToBottom)
  watch(() => props.isTyping, scrollToBottom)
  
  // Scroll al final al montar el componente
  onMounted(scrollToBottom)
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