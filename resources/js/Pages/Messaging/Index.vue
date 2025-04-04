<template>
  <Layout>
    <div class="min-h-screen bg-gray-50 flex">
 
      <ConversationList 
        :conversations="conversations" 
        :loading="loading"
        :search-query="searchQuery"
        :is-company="isCompany"
        :active-conversation-id="activeConversation?.id"
        @select-conversation="selectConversation"
        @search="searchQuery = $event"
        @new-conversation="showNewMessageModal = true"
      />
  
      <!-- Área de chat (siempre visible) -->
      <ConversationArea 
        :conversation="activeConversation"
        :messages="messages"
        :loading="loadingMessages"
        :is-typing="isTyping"
        :is-company="isCompany"
        @send-message="sendMessage"
        @view-job-offer="viewJobOffer"
      />
  
      <!-- Modal para nuevo mensaje (solo para empresas) -->
      <NewConversationModal
        v-if="showNewMessageModal"
        :company-jobs="companyJobs"
        @close="showNewMessageModal = false"
        @create="startNewConversation"
      />
  
    </div>
  </Layout>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue'
  import { router } from '@inertiajs/vue3'
  import ConversationList from '@/Components/Messages/ConversationList.vue'
  import ConversationArea from '@/Components/Messages/ConversationArea.vue'
  import NewConversationModal from '@/Components/Messages/NewConversationModal.vue'
  import Layout from '@/Components/Layout.vue'
  // Estado
  const loading = ref(false)
  const loadingMessages = ref(false)
  const conversations = ref([])
  const messages = ref([])
  const activeConversation = ref(null)
  const searchQuery = ref('')
  const isTyping = ref(false)
  const showNewMessageModal = ref(false)
  const companyJobs = ref([])
  
  // Simulación de datos de usuario (en producción, esto vendría de Inertia)
  const currentUser = ref({
    id: 1,
    name: 'Usuario',
    roles: [{ name: 'empresa' }] // o 'alumne'
  })
  
  // Computed properties
  const isCompany = computed(() => {
    return currentUser.value.roles && 
           currentUser.value.roles.length > 0 && 
           currentUser.value.roles[0].name === 'empresa'
  })
  
  // Métodos
  const fetchConversations = async () => {
    loading.value = true
    
    try {
      // En producción, esto sería una llamada a la API
      // const response = await axios.get('/api/conversations')
      // conversations.value = response.data
      
      // Simulación de datos
      await new Promise(resolve => setTimeout(resolve, 1000))
      conversations.value = [
        {
          id: 1,
          with_company: null,
          with_user: {
            id: 2,
            name: 'María',
            last_name_1: 'García',
            profile: {
              profile_picture: null
            }
          },
          last_message: {
            content: 'Gracias por la información, revisaré la oferta y te responderé pronto.',
            created_at: '2023-04-01T10:30:00Z'
          },
          unread_count: 2,
          created_at: '2023-03-28T14:20:00Z',
          related_job: {
            id: 101,
            title: 'UX/UI Designer',
            company: {
              name: 'TechCorp'
            }
          }
        },
        {
          id: 2,
          with_company: null,
          with_user: {
            id: 1,
            name: 'Juan',
            last_name_1: 'Pérez',
            profile: {
              profile_picture: null
            }
          },
          last_message: {
            content: 'Me gustaría programar una entrevista para la próxima semana.',
            created_at: '2023-03-31T16:45:00Z'
          },
          unread_count: 0,
          created_at: '2023-03-25T09:15:00Z',
          related_job: {
            id: 102,
            title: 'Desarrollador Full Stack',
            company: {
              name: 'TechCorp'
            }
          }
        }
      ]
    } catch (error) {
      console.error('Error al cargar conversaciones:', error)
    } finally {
      loading.value = false
    }
  }
  
  const fetchMessages = async (conversationId) => {
    loadingMessages.value = true
    
    try {
      // En producción, esto sería una llamada a la API
      // const response = await axios.get(`/api/conversations/${conversationId}/messages`)
      // messages.value = response.data
      
      // Simulación de datos
      await new Promise(resolve => setTimeout(resolve, 800))
      messages.value = [
        {
          id: 1,
          content: 'Hola, estamos interesados en tu perfil para nuestra oferta de UX/UI Designer.',
          created_at: '2023-03-28T14:20:00Z',
          is_from_me: true,
          read_at: '2023-03-28T14:25:00Z'
        },
        {
          id: 2,
          content: 'Hola, gracias por contactarme. Me interesa mucho la posición.',
          created_at: '2023-03-28T14:30:00Z',
          is_from_me: false,
          read_at: '2023-03-28T14:31:00Z'
        },
        {
          id: 3,
          content: 'Genial. ¿Podrías contarme un poco más sobre tu experiencia en diseño de interfaces?',
          created_at: '2023-03-28T14:35:00Z',
          is_from_me: true,
          read_at: '2023-03-28T14:40:00Z'
        },
        {
          id: 4,
          content: 'Claro. Tengo 3 años de experiencia trabajando con Figma y Adobe XD. He diseñado interfaces para aplicaciones web y móviles, enfocándome en la experiencia de usuario y la accesibilidad.',
          created_at: '2023-03-28T14:45:00Z',
          is_from_me: false,
          read_at: '2023-03-28T14:46:00Z'
        },
        {
          id: 5,
          content: 'Suena muy bien. ¿Te gustaría programar una entrevista para hablar más en detalle?',
          created_at: '2023-03-28T14:50:00Z',
          is_from_me: true,
          read_at: '2023-03-28T15:00:00Z'
        },
        {
          id: 6,
          content: 'Por supuesto, me encantaría. ¿Qué días y horarios tienen disponibles?',
          created_at: '2023-03-31T09:20:00Z',
          is_from_me: false,
          read_at: '2023-03-31T09:25:00Z'
        },
        {
          id: 7,
          content: 'Tenemos disponibilidad el martes o jueves de la próxima semana, entre las 10:00 y 16:00. ¿Alguna preferencia?',
          created_at: '2023-03-31T10:15:00Z',
          is_from_me: true,
          read_at: null
        }
      ]
    } catch (error) {
      console.error('Error al cargar mensajes:', error)
    } finally {
      loadingMessages.value = false
      
      // Marcar como leídos
      if (activeConversation.value) {
        activeConversation.value.unread_count = 0
      }
    }
  }
  
  const fetchCompanyJobs = async () => {
    try {
      // En producción, esto sería una llamada a la API
      // const response = await axios.get('/api/company/jobs')
      // companyJobs.value = response.data
      
      // Simulación de datos
      companyJobs.value = [
        { id: 101, title: 'UX/UI Designer' },
        { id: 102, title: 'Desarrollador Full Stack' },
        { id: 103, title: 'Data Scientist' }
      ]
    } catch (error) {
      console.error('Error al cargar ofertas:', error)
    }
  }
  
  const selectConversation = (conversation) => {
    activeConversation.value = conversation
    fetchMessages(conversation.id)
  }
  
  const sendMessage = async (messageContent) => {
    if (!messageContent.trim() || !activeConversation.value) return
    
    // Optimistic UI update
    const tempId = Date.now()
    messages.value.push({
      id: tempId,
      content: messageContent,
      created_at: new Date().toISOString(),
      is_from_me: true,
      read_at: null
    })
    
    // Actualizar última mensaje en la conversación
    activeConversation.value.last_message = {
      content: messageContent,
      created_at: new Date().toISOString()
    }
    
    try {
      // En producción, esto sería una llamada a la API
      // const response = await axios.post(`/api/conversations/${activeConversation.value.id}/messages`, {
      //   content: messageContent
      // })
      
      // Simulación de envío
      await new Promise(resolve => setTimeout(resolve, 500))
      
      // Simular respuesta después de un tiempo
      simulateResponse()
    } catch (error) {
      console.error('Error al enviar mensaje:', error)
      
      // Eliminar mensaje optimista en caso de error
      messages.value = messages.value.filter(m => m.id !== tempId)
    }
  }
  
  const simulateResponse = () => {
    // Solo para demo - simular "escribiendo..."
    setTimeout(() => {
      isTyping.value = true
      
      setTimeout(() => {
        isTyping.value = false
        
        // Agregar respuesta simulada
        const responses = [
          "Gracias por tu mensaje. Lo revisaré y te responderé pronto.",
          "Perfecto, ¿podríamos programar una llamada para discutir esto?",
          "Entendido. ¿Podrías proporcionar más detalles sobre tu experiencia?",
          "Excelente, estamos interesados en tu perfil. ¿Cuál sería tu disponibilidad para una entrevista?"
        ]
        
        const randomResponse = responses[Math.floor(Math.random() * responses.length)]
        
        messages.value.push({
          id: Date.now(),
          content: randomResponse,
          created_at: new Date().toISOString(),
          is_from_me: false,
          read_at: new Date().toISOString()
        })
        
        // Actualizar última mensaje en la conversación
        activeConversation.value.last_message = {
          content: randomResponse,
          created_at: new Date().toISOString()
        }
      }, 2000)
    }, 1000)
  }
  
  const viewJobOffer = (jobId) => {
    router.get(`/ofertas/${jobId}`)
  }
  
  const startNewConversation = async (data) => {
    try {
      // En producción, esto sería una llamada a la API
      // const response = await axios.post('/api/conversations', {
      //   recipient_id: data.recipient.id,
      //   message: data.message,
      //   job_id: data.jobId
      // })
      
      // Simulación
      await new Promise(resolve => setTimeout(resolve, 800))
      
      // Crear nueva conversación simulada
      const newConversation = {
        id: Date.now(),
        with_company: null,
        with_user: data.recipient,
        last_message: {
          content: data.message,
          created_at: new Date().toISOString()
        },
        unread_count: 0,
        created_at: new Date().toISOString(),
        related_job: data.jobId ? companyJobs.value.find(j => j.id === data.jobId) : null
      }
      
      // Agregar a la lista y seleccionar
      conversations.value.unshift(newConversation)
      selectConversation(newConversation)
      
      // Cerrar modal
      showNewMessageModal.value = false
    } catch (error) {
      console.error('Error al crear conversación:', error)
    }
  }
  
  // Lifecycle hooks
  onMounted(() => {
    fetchConversations()
    
    if (isCompany.value) {
      fetchCompanyJobs()
    }
  })
  </script>