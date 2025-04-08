<template>
  <div class="w-1/3 border-r border-gray-200 overflow-hidden flex flex-col">
    <!-- Encabezado -->
    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
      <h2 class="text-xl font-bold text-gray-800">Mensajes</h2>
      <button 
        @click="$emit('new-conversation')"
        class="w-10 h-10 rounded-full bg-[#193CB8] text-white flex items-center justify-center hover:bg-[#142d8c] transition-colors"
      >
        <i class='bx bx-plus'></i>
      </button>
    </div>
    
    <!-- Buscador -->
    <div class="p-4 border-b border-gray-200">
      <div class="relative">
        <input
          type="text"
          placeholder="Buscar conversaciones..."
          v-model="searchQuery"
          class="w-full px-4 py-2 pr-10 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
        />
        <i class='bx bx-search absolute right-3 top-2.5 text-gray-400'></i>
      </div>
    </div>
    
    <!-- Lista de conversaciones -->
    <div class="flex-1 overflow-y-auto">
      <div v-if="loading" class="p-4 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#193CB8]"></div>
        <p class="mt-2 text-gray-500">Cargando conversaciones...</p>
      </div>
      
      <div v-else-if="filteredConversations.length === 0" class="p-4 text-center text-gray-500">
        <i class='bx bx-message-x text-4xl mb-2'></i>
        <p>No se encontraron conversaciones</p>
      </div>
      
      <ConversationItem 
        v-for="conversation in filteredConversations" 
        :key="conversation.id"
        :conversation="conversation"
        :isActive="activeConversationId === conversation.id"
        @click="$emit('select-conversation', conversation.id)"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import ConversationItem from './ConversationItem.vue';

const props = defineProps({
  conversations: {
    type: Array,
    required: true
  },
  activeConversationId: {
    type: Number,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['select-conversation', 'new-conversation']);

const searchQuery = ref('');

const filteredConversations = computed(() => {
  if (!searchQuery.value.trim()) return props.conversations;
  
  const query = searchQuery.value.toLowerCase();
  return props.conversations.filter(conversation => {
    const participantName = conversation.participant.name.toLowerCase();
    const lastMessage = (conversation.last_message || '').toLowerCase();
    
    return participantName.includes(query) || lastMessage.includes(query);
  });
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
</style>