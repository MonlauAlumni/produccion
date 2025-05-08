<script setup>
import { ref, onMounted, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Layout from '@/Components/Layout.vue';

const props = defineProps({
  notifications: {
    type: Object,
    default: () => ({
      data: [],
      current_page: 1,
      last_page: 1,
      from: null,
      to: null,
      total: 0
    })
  },
  unreadCount: {
    type: Number,
    default: 0
  }
});

const isLoading = ref(false);
const showScrollTopButton = ref(false);
const activeTab = ref('all');
const notificationsList = ref(props.notifications.data || []);

const pagination = computed(() => ({
  currentPage: props.notifications.current_page,
  lastPage: props.notifications.last_page,
  from: props.notifications.from,
  to: props.notifications.to,
  total: props.notifications.total || 0
}));

// Categorías de notificaciones
const notificationTabs = [
  { id: 'all', name: 'Todas', icon: 'bx-bell' },
  { id: 'unread', name: 'No leídas', icon: 'bx-envelope' },
  { id: 'mentions', name: 'Menciones', icon: 'bx-at' },
  { id: 'events', name: 'Eventos', icon: 'bx-calendar-event' },
  { id: 'jobs', name: 'Ofertas', icon: 'bx-briefcase' }
];

const loadMoreNotifications = async () => {
  if (isLoading.value || pagination.value.currentPage >= pagination.value.lastPage) return;

  isLoading.value = true;

  try {
    const nextPage = pagination.value.currentPage + 1;
    await router.get(
      '/notificaciones',
      {
        page: nextPage,
        filter: activeTab.value !== 'all' ? activeTab.value : undefined
      },
      {
        preserveState: true,
        preserveScroll: true,
        only: ['notifications'],
        onSuccess: (page) => {
          if (page.props.notifications && page.props.notifications.data) {
            notificationsList.value = [...notificationsList.value, ...page.props.notifications.data];
          }
        }
      }
    );
  } catch (error) {
    console.error('Error al cargar más notificaciones:', error);
  } finally {
    isLoading.value = false;
  }
};

// Manejar scroll infinito
const handleScroll = () => {
  const scrollPosition = window.scrollY + window.innerHeight;
  const pageHeight = document.documentElement.scrollHeight;

  // Mostrar/ocultar botón para volver arriba
  showScrollTopButton.value = window.scrollY > 500;

  // Cargar más notificaciones cuando estamos cerca del final
  if (scrollPosition > pageHeight - 500 && !isLoading.value && pagination.value.currentPage < pagination.value.lastPage) {
    loadMoreNotifications();
  }
};

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
};

const setFilter = (filter) => {
  activeTab.value = filter;
  applyFilters();
};

const applyFilters = () => {
  router.get('/notificaciones', {
    filter: activeTab.value !== 'all' ? activeTab.value : undefined
  }, {
    preserveScroll: true,
    only: ['notifications']
  });
};

const markAsRead = (notificationId) => {
  router.post(`/notificaciones/${notificationId}/marcar-leida`, {}, {
    preserveScroll: true,
    only: ['notifications', 'unreadCount']
  });
};

const markAllAsRead = () => {
  router.post('/notificaciones/marcar-todas-leidas', {}, {
    preserveScroll: true,
    only: ['notifications', 'unreadCount'],
    onSuccess: () => {
      notificationsList.value = notificationsList.value.map(notification => ({
        ...notification,
        read_at: notification.read_at || new Date().toISOString()
      }));
    }
  });
};

const deleteNotification = (notificationId) => {
  router.delete(`/notificaciones/${notificationId}`, {
    preserveScroll: true,
    only: ['notifications'],
    onSuccess: () => {
      notificationsList.value = notificationsList.value.filter(
        notification => notification.id !== notificationId
      );
    }
  });
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  
  const date = new Date(dateString);
  const now = new Date();
  const diffTime = Math.abs(now - date);
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays === 0) {
    // Hoy - mostrar hora
    return date.toLocaleTimeString('es-ES', {
      hour: '2-digit',
      minute: '2-digit'
    });
  } else if (diffDays === 1) {
    // Ayer
    return 'Ayer';
  } else if (diffDays < 7) {
    // Esta semana - mostrar día
    return date.toLocaleDateString('es-ES', {
      weekday: 'long'
    });
  } else {
    // Más de una semana - mostrar fecha completa
    return date.toLocaleDateString('es-ES', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  }
};

const getNotificationIcon = (type) => {
  switch (type) {
    case 'mention':
      return 'bx-at';
    case 'comment':
      return 'bx-message-dots';
    case 'like':
      return 'bx-heart';
    case 'event':
      return 'bx-calendar-event';
    case 'job':
      return 'bx-briefcase';
    case 'connection':
      return 'bx-user-plus';
    case 'group':
      return 'bx-group';
    case 'message':
      return 'bx-envelope';
    default:
      return 'bx-bell';
  }
};

const getNotificationColor = (type) => {
  switch (type) {
    case 'mention':
      return 'bg-blue-500';
    case 'comment':
      return 'bg-green-500';
    case 'like':
      return 'bg-red-500';
    case 'event':
      return 'bg-purple-500';
    case 'job':
      return 'bg-yellow-500';
    case 'connection':
      return 'bg-indigo-500';
    case 'group':
      return 'bg-teal-500';
    case 'message':
      return 'bg-blue-500';
    default:
      return 'bg-gray-500';
  }
};

const unreadNotificationsCount = computed(() => {
  return notificationsList.value.filter(notification => !notification.is_read).length;
});

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
  
  return () => {
    window.removeEventListener('scroll', handleScroll);
  };
});

const page = usePage();
</script>

<template>
  <Layout>
    <div class="min-h-screen bg-gray-50 flex flex-col">
      <!-- Header Section -->
      <div class="bg-[#193CB8] text-white py-8">
        <div class="max-w-6xl mx-auto px-4">
          <div class="flex flex-col md:flex-row items-start justify-between">
            <div class="mb-6 md:mb-0">
              <h1 class="text-3xl font-bold mb-2">Notificaciones</h1>
              <p class="text-blue-100 mb-4">Mantente al día con todas tus actualizaciones</p>
            </div>

            <!-- Stats Card -->
            <div class="bg-white/10 backdrop-blur-sm p-4 rounded-lg border border-white/20 text-center">
              <div class="text-3xl font-bold">{{ unreadNotificationsCount }}</div>
              <div class="text-blue-100">No leídas</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex-1 py-6">
        <div class="max-w-4xl mx-auto px-4">
          <!-- Tabs Navigation -->
          <div class="bg-white rounded-xl shadow-sm mb-6 overflow-hidden">
            <div class="flex overflow-x-auto hide-scrollbar">
              <button 
                v-for="tab in notificationTabs" 
                :key="tab.id"
                @click="setFilter(tab.id)"
                :class="[
                  'px-4 py-3 flex items-center whitespace-nowrap transition-colors',
                  activeTab === tab.id
                    ? 'text-[#193CB8] border-b-2 border-[#193CB8] font-medium'
                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                ]"
              >
                <i :class="['bx mr-2', tab.icon]"></i>
                {{ tab.name }}
                <span 
                  v-if="tab.id === 'unread' && unreadNotificationsCount > 0"
                  class="ml-2 bg-[#193CB8] text-white text-xs rounded-full px-2 py-0.5"
                >
                  {{ unreadNotificationsCount }}
                </span>
              </button>
            </div>
          </div>

          <!-- Actions Bar -->
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">
              {{ activeTab === 'all' ? 'Todas las notificaciones' : 
                 activeTab === 'unread' ? 'Notificaciones no leídas' : 
                 activeTab === 'mentions' ? 'Menciones' : 
                 activeTab === 'events' ? 'Eventos' : 'Ofertas de trabajo' }}
            </h2>
            <button 
              @click="markAllAsRead"
              class="text-[#193CB8] hover:text-[#142d8c] text-sm font-medium flex items-center"
            >
              <i class='bx bx-check-double mr-1'></i>
              Marcar todas como leídas
            </button>
          </div>

          <!-- Notifications List -->
          <div class="space-y-3">
            <div 
              v-for="notification in notificationsList" 
              :key="notification.id"
              :class="[
                'bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-300 border border-gray-200',
                !notification.is_read ? 'border-l-4 border-l-[#193CB8]' : ''
              ]"
            >
              <div class="p-4 flex">
                <!-- Icon -->
                <div :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 mr-4',
                  getNotificationColor(notification.type)
                ]">
                  <i :class="['bx text-white text-xl', getNotificationIcon(notification.type)]"></i>
                </div>

                <!-- Content -->
                <div class="flex-1">
                  <div class="flex justify-between items-start">
                    <p class="text-gray-800 font-medium">{{ notification.message }}</p>
                    <div class="flex items-center space-x-2 ml-2">
                      <span class="text-xs text-gray-500">{{ formatDate(notification.created_at) }}</span>
                      <div class="flex space-x-1">
                        <button 
                          v-if="!notification.is_read"
                          @click="markAsRead(notification.id)"
                          class="text-gray-400 hover:text-[#193CB8] p-1 rounded-full hover:bg-gray-100"
                          title="Marcar como leída"
                        >
                          <i class='bx bx-check text-lg'></i>
                        </button>
                        <button 
                          @click="deleteNotification(notification.id)"
                          class="text-gray-400 hover:text-red-500 p-1 rounded-full hover:bg-gray-100"
                          title="Eliminar"
                        >
                          <i class='bx bx-x text-lg'></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Notification Details -->
                  <div class="mt-2">
                    <!-- Message Notification -->
                    <div v-if="notification.type === 'message'" class="mt-2">
                      <button class="text-[#193CB8] text-sm hover:underline">Ver mensaje</button>
                    </div>
                    
                    <!-- Otros tipos de notificaciones se pueden agregar según se implementen en el backend -->
                  </div>
                </div>
              </div>
            </div>

            <!-- Loading Indicator -->
            <div v-if="isLoading" class="flex justify-center py-4">
              <div class="animate-pulse flex space-x-2">
                <div class="w-2 h-2 bg-[#193CB8] rounded-full"></div>
                <div class="w-2 h-2 bg-[#193CB8] rounded-full animation-delay-200"></div>
                <div class="w-2 h-2 bg-[#193CB8] rounded-full animation-delay-400"></div>
              </div>
            </div>

            <!-- End of Results -->
            <div v-if="pagination.currentPage >= pagination.lastPage && !isLoading && notificationsList.length > 0" class="text-center py-8">
              <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class='bx bx-check-circle text-3xl text-[#193CB8]'></i>
              </div>
              <h3 class="text-lg font-medium text-gray-800 mb-2">¡Has visto todas las notificaciones!</h3>
              <p class="text-gray-500 mb-4">Estás al día con todas tus actualizaciones</p>
              <button 
                @click="scrollToTop"
                class="text-[#193CB8] font-medium hover:underline flex items-center justify-center mx-auto"
              >
                <i class='bx bx-chevron-up mr-1'></i>
                Volver arriba
              </button>
            </div>

            <!-- No Results -->
            <div v-if="notificationsList.length === 0 && !isLoading" class="text-center py-8">
              <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class='bx bx-bell-off text-3xl text-[#193CB8]'></i>
              </div>
              <h3 class="text-lg font-medium text-gray-800 mb-2">No hay notificaciones</h3>
              <p class="text-gray-500 mb-4">Estás al día con todas tus actualizaciones</p>
              <button 
                @click="router.get('/connect')"
                class="bg-[#193CB8] text-white px-4 py-2 rounded-lg font-medium hover:bg-[#142d8c] transition-colors"
              >
                Explorar comunidad
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Scroll to Top Button -->
      <button 
        v-show="showScrollTopButton" 
        @click="scrollToTop"
        class="fixed bottom-6 right-6 w-12 h-12 rounded-full bg-[#193CB8] text-white shadow-lg flex items-center justify-center hover:bg-[#142d8c] transition-all duration-300 z-50 animate-fade-in"
      >
        <i class='bx bx-chevron-up text-xl'></i>
      </button>
    </div>
  </Layout>
</template>

<style>
.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.hide-scrollbar::-webkit-scrollbar {
  display: none;
}

@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

.animation-delay-200 {
  animation-delay: 0.2s;
}

.animation-delay-400 {
  animation-delay: 0.4s;
}
</style>
