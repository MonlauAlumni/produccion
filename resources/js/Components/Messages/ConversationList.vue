<template>
    <div class="w-80 bg-white border-r border-gray-200 flex flex-col h-screen">
      <!-- Cabecera -->
      <div class="p-4 border-b border-gray-100">
        <div class="flex items-center justify-between mb-3">
          <h1 class="text-xl font-bold text-gray-800">Mensajes</h1>
          <button 
            v-if="isCompany"
            @click="$emit('new-conversation')"
            class="text-[#193CB8] hover:bg-blue-50 p-2 rounded-full"
          >
            <i class='bx bx-plus text-xl'></i>
          </button>
        </div>
        <div class="relative">
          <input
            type="text"
            placeholder="Buscar conversaciones..."
            class="w-full px-4 py-2 pr-8 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#193CB8] text-sm"
            :value="searchQuery"
            @input="$emit('search', $event.target.value)"
          />
          <i class='bx bx-search absolute right-3 top-2.5 text-gray-400'></i>
        </div>
      </div>
  
      <!-- Lista de conversaciones -->
      <div class="flex-1 overflow-y-auto">
        <div v-if="loading" class="flex justify-center items-center h-32">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#193CB8]"></div>
        </div>
        
        <div v-else-if="filteredConversations.length === 0" class="flex flex-col items-center justify-center h-64 px-4 text-center">
          <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <i class='bx bx-message-square-dots text-3xl text-gray-400'></i>
          </div>
          <h3 class="text-lg font-medium text-gray-800 mb-2">No hay mensajes</h3>
          <p class="text-gray-500 text-sm">
            {{ isCompany ? 'Contacta con alumni para iniciar una conversación' : 'Las empresas te contactarán cuando estén interesadas' }}
          </p>
        </div>
        
        <div v-else>
          <ConversationItem
            v-for="conversation in filteredConversations"
            :key="conversation.id"
            :conversation="conversation"
            :is-active="activeConversationId === conversation.id"
            @click="$emit('select-conversation', conversation)"
          />
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  import ConversationItem from './ConversationItem.vue'
  
  const props = defineProps({
    conversations: {
      type: Array,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    },
    searchQuery: {
      type: String,
      default: ''
    },
    isCompany: {
      type: Boolean,
      default: false
    },
    activeConversationId: {
      type: Number,
      default: null
    }
  })
  
  defineEmits(['select-conversation', 'search', 'new-conversation'])
  
  const filteredConversations = computed(() => {
    if (!props.searchQuery) return props.conversations
    
    const query = props.searchQuery.toLowerCase()
    return props.conversations.filter(conversation => {
      const contactName = conversation.with_company 
        ? conversation.with_company.company_name.toLowerCase()
        : `${conversation.with_user.name} ${conversation.with_user.last_name_1}`.toLowerCase()
      
      return contactName.includes(query) || 
             (conversation.last_message && conversation.last_message.content.toLowerCase().includes(query)) ||
             (conversation.related_job && conversation.related_job.title.toLowerCase().includes(query))
    })
  })
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