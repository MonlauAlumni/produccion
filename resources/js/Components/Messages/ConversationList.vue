<template>
  <div class="w-1/3 border-r border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col bg-white dark:bg-gray-800">
    <!-- Encabezado -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
      <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Mensajes</h2>
      <button 
      v-if="userRole === 'empresa'"
        @click="$emit('new-conversation')"
        class="w-10 h-10 rounded-full bg-[#193CB8] text-white flex items-center justify-center hover:bg-[#142d8c] transition-colors"
      >
        <i class='bx bx-plus'></i>
      </button>
    </div>
    
    <!-- Buscador -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
      <div class="relative">
        <input
          type="text"
          placeholder="Buscar conversaciones..."
          v-model="searchQuery"
          class="w-full px-4 py-2 pr-10 rounded-lg border border-gray-200 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-[#193CB8] dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
        />
        <i class='bx bx-search absolute right-3 top-2.5 text-gray-400 dark:text-gray-500'></i>
      </div>
    </div>
    
    <!-- Lista de conversaciones -->
    <div class="flex-1 overflow-y-auto">
      <div v-if="loading" class="p-4 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#193CB8] dark:border-blue-400"></div>
        <p class="mt-2 text-gray-500 dark:text-gray-400">Cargando conversaciones...</p>
      </div>
      
      <div v-else-if="filteredConversations.length === 0" class="p-4 text-center text-gray-500 dark:text-gray-400">
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
import { usePage, router } from '@inertiajs/vue3';

import ConversationItem from './ConversationItem.vue';
const page = usePage()
const userRole = computed(() => page.props.auth.user?.roles?.[0]?.name ?? null)
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