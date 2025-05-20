<template>
  <div class="flex-1 flex flex-col bg-gray-50 dark:bg-gray-900">
    <!-- Encabezado de la conversación -->
    <ConversationHeader 
      :conversation="conversation"
    />
    
    <!-- Lista de mensajes -->
    <MessageList 
      :messages="messages"
      :currentUser="currentUser"
      :loading="loading"
      @load-more="$emit('load-more')"
      :hasMoreMessages="hasMoreMessages"
    />
    
    <!-- Formulario de mensaje -->
    <MessageForm @send="$emit('send-message', $event)" />
  </div>
</template>

<script setup>
import ConversationHeader from './ConversationHeader.vue';
import MessageList from './MessageList.vue';
import MessageForm from './MessageForm.vue';

defineProps({
  conversation: {
    type: Object,
    required: true
  },
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

defineEmits(['send-message', 'load-more']);
</script>