<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Layout from '../../Components/Layout.vue';
import ConversationList from '../../Components/Messages/ConversationList.vue';
import ConversationArea from '../../Components/Messages/ConversationArea.vue';
import NewConversationModal from '../../Components/Messages/NewConversationModal.vue';
import MessageService from '../../services/MessageService';

const props = defineProps({
  companyJobs: {
    type: Array,
    default: () => []
  },
});

// Estado
const conversations = ref([]);
const activeConversationId = ref(null);
const messages = ref([]);
const currentUser = ref(null);
const showNewConversationModal = ref(false);
const companyJobs = ref(props.companyJobs || []);
const currentPage = ref(1);
const hasMoreMessages = ref(false);
const loadingConversations = ref(true);
const loadingMessages = ref(false);

// Para preseleccionar usuario y oferta
const preselectedUserId = ref(null);
const preselectedJobId = ref(null);
const preselectedUserName = ref('');

// Obtener la página actual
const page = usePage();

// Computed
const activeConversation = computed(() => {
  if (!activeConversationId.value) return null;
  return conversations.value.find(conv => conv.id === activeConversationId.value);
});

// Métodos
const fetchConversations = async () => {
  try {
    loadingConversations.value = true;
    const data = await MessageService.getConversations();
    conversations.value = data.conversations;
    currentUser.value = data.currentUser;
    
    // Si hay conversaciones, seleccionar la primera por defecto
    if (conversations.value.length > 0 && !activeConversationId.value) {
      activeConversationId.value = conversations.value[0].id;
    }
  } catch (error) {
    console.error('Error al cargar conversaciones:', error);
  } finally {
    loadingConversations.value = false;
  }
};

const fetchMessages = async (conversationId, page = 1) => {
  if (!conversationId) return;
  
  try {
    loadingMessages.value = true;
    const data = await MessageService.getMessages(conversationId, page);
    
    // Invertir el orden de los mensajes para que los más recientes estén abajo
    const receivedMessages = [...data.messages.data].reverse();
    
    if (page === 1) {
      messages.value = receivedMessages;
    } else {
      // Para paginación, añadir mensajes más antiguos arriba
      messages.value = [...receivedMessages, ...messages.value];
    }
    
    hasMoreMessages.value = data.messages.current_page < data.messages.last_page;
    currentPage.value = data.messages.current_page;
    
    // Marcar como leída
    if (page === 1) {
      markConversationAsRead(conversationId);
    }
  } catch (error) {
    console.error('Error al cargar mensajes:', error);
  } finally {
    loadingMessages.value = false;
  }
};

const loadMoreMessages = () => {
  if (hasMoreMessages.value && !loadingMessages.value) {
    fetchMessages(activeConversationId.value, currentPage.value + 1);
  }
};

const selectConversation = (conversationId) => {
  if (activeConversationId.value === conversationId) return;
  
  activeConversationId.value = conversationId;
  messages.value = [];
  currentPage.value = 1;
  hasMoreMessages.value = false;
  fetchMessages(conversationId);
};

const sendMessage = async (messageText) => {
  if (!activeConversationId.value || !messageText.trim()) return;
  
  try {
    // Optimistic UI update - añadir mensaje inmediatamente AL FINAL (más reciente abajo)
    const newMessage = {
      id: 'temp-' + Date.now(),
      conversation_id: activeConversationId.value,
      user_id: currentUser.value.id,
      content: messageText,
      message: messageText, // Para compatibilidad
      created_at: new Date().toISOString(),
      user: currentUser.value,
      is_sender: true
    };
    
    // Añadir al final para que aparezca abajo
    messages.value.push(newMessage);
    
    // Actualizar la conversación en la lista
    const conversationIndex = conversations.value.findIndex(c => c.id === activeConversationId.value);
    if (conversationIndex !== -1) {
      const updatedConversation = { 
        ...conversations.value[conversationIndex],
        last_message: messageText,
        updated_at: new Date().toISOString()
      };
      
      // Eliminar y añadir al principio
      conversations.value.splice(conversationIndex, 1);
      conversations.value.unshift(updatedConversation);
    }
    
    // Enviar mensaje al servidor
    const response = await MessageService.sendMessage(activeConversationId.value, messageText);
    
    // Actualizar el mensaje temporal con el real
    const tempIndex = messages.value.findIndex(m => m.id === newMessage.id);
    if (tempIndex !== -1) {
      messages.value[tempIndex] = response.message;
    }
  } catch (error) {
    console.error('Error al enviar mensaje:', error);
    // Eliminar el mensaje optimista en caso de error
    messages.value = messages.value.filter(m => m.id !== 'temp-' + Date.now());
  }
};

const createConversation = async (data) => {
  try {
    console.log('Datos a enviar:', {
      recipient_id: data.recipient.id,
      message: data.message,
      job_id: data.jobId
    });
    
    const response = await MessageService.createConversation({
      recipient_id: data.recipient.id,
      message: data.message,
      job_id: data.jobId
    });
    
    // Añadir la nueva conversación a la lista y seleccionarla
    conversations.value.unshift(response.conversation);
    activeConversationId.value = response.conversation.id;
    showNewConversationModal.value = false;
    
    // Cargar mensajes de la nueva conversación
    fetchMessages(response.conversation.id);
  } catch (error) {
    console.error('Error al crear conversación:', error);
    // Mostrar un mensaje de error al usuario
   
  }
};

const markConversationAsRead = async (conversationId) => {
  try {
    await MessageService.markAsRead(conversationId);
    
    // Actualizar el estado de la conversación en la lista
    const conversationIndex = conversations.value.findIndex(c => c.id === conversationId);
    if (conversationIndex !== -1) {
      conversations.value[conversationIndex].unread_count = 0;
    }
  } catch (error) {
    console.error('Error al marcar como leída:', error);
  }
};



// Escuchar eventos de WebSocket
const listenForMessages = () => {
  if (window.Echo && currentUser.value) {
    window.Echo.private(`user.${currentUser.value.id}`)
      .listen('MessageSent', (e) => {
        // Si el mensaje es para la conversación activa, añadirlo AL FINAL
        if (e.message.conversation_id === activeConversationId.value) {
          messages.value.push(e.message);
          markConversationAsRead(activeConversationId.value);
        }
        
        // Resto del código igual...
      });
  }
};

// Función para preseleccionar un usuario en el modal
const preselectUserInModal = async (userId, userName) => {
  // Esperar a que se carguen los datos necesarios
  if (loadingConversations.value) {
    await new Promise(resolve => {
      const checkLoading = setInterval(() => {
        if (!loadingConversations.value) {
          clearInterval(checkLoading);
          resolve();
        }
      }, 100);
    });
  }
  
  // Guardar los datos preseleccionados
  preselectedUserId.value = userId;
  preselectedUserName.value = userName;
  
  // Abrir el modal
  showNewConversationModal.value = true;
};

// Lifecycle hooks
onMounted(async () => {
  // Verificar si hay parámetros para abrir el modal automáticamente
  const params = new URLSearchParams(window.location.search);
  const userIdParam = params.get('userId');
  const jobIdParam = params.get('jobId');
  const userNameParam = params.get('userName');

  if (params.get('openModal') === 'true' && userIdParam) {
    preselectedUserId.value = userIdParam;
    preselectedJobId.value = jobIdParam;
    preselectedUserName.value = userNameParam || '';
    
    // Mostrar el modal inmediatamente, sin esperar a que carguen otros datos
    showNewConversationModal.value = true;
    
    // Limpiar los parámetros de la URL
    //router.get('/mensajes', {}, { replace: true });
  }
  
  // Cargar los datos en segundo plano
  fetchConversations().then(() => {
    if (activeConversationId.value) {
      fetchMessages(activeConversationId.value);
    }
  });
  
  fetchCompanyJobs();
  
  if (currentUser.value) {
    listenForMessages();
  }
});

// Watchers
watch(activeConversationId, (newId) => {
  if (newId) {
    messages.value = [];
    currentPage.value = 1;
    hasMoreMessages.value = false;
    fetchMessages(newId);
  }
});
</script>

<template>
  <Layout>
    <div class="bg-gray-100 dark:bg-gray-900 min-h-screen">
      <div class="container mx-auto py-6 px-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
          <div class="flex h-[calc(100vh-120px)]">
            <!-- Lista de conversaciones -->
            <ConversationList 
              :conversations="conversations" 
              :activeConversationId="activeConversationId"
              @select-conversation="selectConversation"
              @new-conversation="showNewConversationModal = true"
              :loading="loadingConversations"
            />
            
            <!-- Área de conversación -->
            <ConversationArea 
              v-if="activeConversation" 
              :conversation="activeConversation"
              :messages="messages"
              :currentUser="currentUser"
              @send-message="sendMessage"
              :loading="loadingMessages"
              @load-more="loadMoreMessages"
              :hasMoreMessages="hasMoreMessages"
            />
            
            <!-- Estado vacío -->
            <div v-else class="flex-1 flex flex-col items-center justify-center p-6 bg-gray-50 dark:bg-gray-800">
              <div class="text-center">
                <i class='bx bx-message-square-dots text-6xl text-gray-300 dark:text-gray-600 mb-4'></i>
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">No hay conversación seleccionada</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Selecciona una conversación o inicia una nueva</p>
               
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Modal para nueva conversación -->
      <NewConversationModal 
        v-if="showNewConversationModal" 
        @close="showNewConversationModal = false"
        @create="createConversation"
        :companyJobs="companyJobs"
        :preselectedUserId="preselectedUserId"
        :preselectedJobId="preselectedJobId"
        :preselectedUserName="preselectedUserName"
      />
    </div>
  </Layout>
</template>