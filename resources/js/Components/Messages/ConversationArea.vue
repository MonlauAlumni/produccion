<template>
    <div class="flex-1 flex flex-col h-235">
      <div v-if="!conversation" class="flex-1 flex flex-col items-center justify-center bg-gray-50 p-8 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
          <i class='bx bx-message-square-detail text-4xl text-gray-400'></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Tus mensajes</h2>
        <p class="text-gray-500 max-w-md mb-6">
          {{ isCompany 
            ? 'Selecciona una conversación o inicia una nueva para contactar con alumni.' 
            : 'Selecciona una conversación para ver tus mensajes. Las empresas te contactarán cuando estén interesadas en tu perfil.' 
          }}
        </p>
        <button 
          v-if="isCompany"
          @click="$emit('new-conversation')"
          class="bg-[#193CB8] text-white px-4 py-2 rounded-lg hover:bg-[#142d8c] transition-colors flex items-center"
        >
          <i class='bx bx-plus mr-2'></i>
          Nuevo mensaje
        </button>
      </div>
  
      <div v-else class="flex-1 flex flex-col h-full">
        <!-- Cabecera de la conversación -->
        <ConversationHeader 
          :conversation="conversation"
          @view-job-offer="$emit('view-job-offer', conversation.related_job.id)"
        />
        
        <!-- Mensajes -->
        <MessageList 
          :conversation="conversation"
          :messages="messages"
          :loading="loading"
          :is-typing="isTyping"
        />
        
        <!-- Formulario de mensaje -->
        <MessageForm @send="$emit('send-message', $event)" />
      </div>
    </div>
  </template>
  
  <script setup>
  import ConversationHeader from './ConversationHeader.vue'
  import MessageList from './MessageList.vue'
  import MessageForm from './MessageForm.vue'
  
  defineProps({
    conversation: {
      type: Object,
      default: null
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
    },
    isCompany: {
      type: Boolean,
      default: false
    }
  })
  
  defineEmits(['send-message', 'view-job-offer', 'new-conversation'])
  </script>