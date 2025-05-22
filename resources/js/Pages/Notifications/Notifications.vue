<script setup>
import { ref, onMounted, computed, watch } from 'vue';
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
const allNotifications = ref(props.notifications.data || []);

const pagination = computed(() => ({
  currentPage: props.notifications.current_page,
  lastPage: props.notifications.last_page,
  from: props.notifications.from,
  to: props.notifications.to,
  total: props.notifications.total || 0
}));

const notificationTabs = [
  { id: 'all', name: 'Todas', icon: 'bx-bell' },
  { id: 'unread', name: 'No leídas', icon: 'bx-envelope' },
  { id: 'system', name: 'Sistema', icon: 'bx-info-circle' },
  { id: 'jobs', name: 'Ofertas', icon: 'bx-briefcase' }
];

const isSystemNotification = (notification) => {
  return ['info', 'success', 'warning', 'error'].includes(notification.type);
};

const systemNotifications = computed(() => {
  return allNotifications.value.filter(notification => isSystemNotification(notification));
});

const systemNotificationsCount = computed(() => {
  return systemNotifications.value.filter(notification => !notification.is_read).length;
});

const filteredNotifications = computed(() => {
  if (activeTab.value === 'all') {
    return allNotifications.value;
  } else if (activeTab.value === 'unread') {
    return allNotifications.value.filter(notification => !notification.is_read);
  } else if (activeTab.value === 'system') {
    return allNotifications.value.filter(notification => isSystemNotification(notification));
  } else if (activeTab.value === 'jobs') {
    return allNotifications.value.filter(notification => notification.type === 'job');
  }
  return allNotifications.value;
});

const loadMoreNotifications = async () => {
  if (isLoading.value || pagination.value.currentPage >= pagination.value.lastPage) return;

  isLoading.value = true;

  try {
    const nextPage = pagination.value.currentPage + 1;
    await router.get(
      '/notificaciones',
      { page: nextPage },
      {
        preserveState: true,
        preserveScroll: true,
        only: ['notifications'],
        onSuccess: (page) => {
          if (page.props.notifications && page.props.notifications.data) {
            allNotifications.value = [...allNotifications.value, ...page.props.notifications.data];
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

const handleScroll = () => {
  const scrollPosition = window.scrollY + window.innerHeight;
  const pageHeight = document.documentElement.scrollHeight;

  showScrollTopButton.value = window.scrollY > 500;

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
};

const markAsRead = (notificationId) => {
  router.put(`/notificaciones/${notificationId}/mark-as-read`, {}, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      const index = allNotifications.value.findIndex(n => n.id === notificationId);
      if (index !== -1 && !allNotifications.value[index].is_read) {
        const updatedList = [...allNotifications.value];
        updatedList[index] = {
          ...updatedList[index],
          is_read: true,
          read_at: new Date().toISOString()
        };
        allNotifications.value = updatedList;
      }
    }
  });
};

const deleteAllNotifications = () => {
  const notificationIds = filteredNotifications.value.map(n => n.id);
  
  if (notificationIds.length === 0) return;
  
  router.delete('/notificaciones', {
    ids: notificationIds
  }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      allNotifications.value = allNotifications.value.filter(
        notification => !notificationIds.includes(notification.id)
      );
    }
  });
};

const markAllAsRead = () => {
  const unreadNotifications = filteredNotifications.value.filter(n => !n.is_read);
  const notificationIds = unreadNotifications.map(n => n.id);
  
  if (notificationIds.length === 0) return;
  
  router.put('/notificaciones/mark-all-as-read', {
    ids: notificationIds
  }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      allNotifications.value = allNotifications.value.map(notification => {
        if (notificationIds.includes(notification.id)) {
          return {
            ...notification,
            is_read: true,
            read_at: notification.read_at || new Date().toISOString()
          };
        }
        return notification;
      });
    }
  });
};

const deleteNotification = (notificationId) => {
  router.delete(`/notificaciones/${notificationId}`, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      allNotifications.value = allNotifications.value.filter(
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
    return date.toLocaleTimeString('es-ES', {
      hour: '2-digit',
      minute: '2-digit'
    });
  } else if (diffDays === 1) {
    return 'Ayer';
  } else if (diffDays < 7) {
    return date.toLocaleDateString('es-ES', {
      weekday: 'long'
    });
  } else {
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
    case 'info':
      return 'bx-info-circle';
    case 'success':
      return 'bx-check-circle';
    case 'warning':
      return 'bx-error';
    case 'error':
      return 'bx-x-circle';
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
    case 'info':
      return 'bg-blue-500';
    case 'success':
      return 'bg-green-500';
    case 'warning':
      return 'bg-amber-500';
    case 'error':
      return 'bg-red-500';
    default:
      return 'bg-gray-500';
  }
};

const getSystemNotificationClass = (type) => {
  switch (type) {
    case 'info':
      return 'border-l-blue-500 bg-blue-50 dark:bg-blue-900/20';
    case 'success':
      return 'border-l-green-500 bg-green-50 dark:bg-green-900/20';
    case 'warning':
      return 'border-l-amber-500 bg-amber-50 dark:bg-amber-900/20';
    case 'error':
      return 'border-l-red-500 bg-red-50 dark:bg-red-900/20';
    default:
      return '';
  }
};

const unreadNotificationsCount = computed(() => {
  return allNotifications.value.filter(notification => !notification.is_read).length;
});

onMounted(() => {
  window.addEventListener('scroll', handleScroll);

  const page = usePage();
  watch(() => page.props.notifications, (newNotifications) => {
    if (newNotifications && newNotifications.data && newNotifications.current_page === 1) {
      allNotifications.value = newNotifications.data;
    }
  }, { deep: true });

  return () => {
    window.removeEventListener('scroll', handleScroll);
  };
});
</script>

<template>
  <Layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col">

      <div class="bg-[#193CB8] dark:bg-[#142d8c] text-white py-8">
        <div class="max-w-6xl mx-auto px-4">
          <div class="flex flex-col md:flex-row items-start justify-between">
            <div class="mb-6 md:mb-0">
              <h1 class="text-3xl font-bold mb-2">Notificaciones</h1>
              <p class="text-blue-100 mb-4">Mantente al día con todas tus actualizaciones</p>
            </div>

            <div class="flex gap-4">
              <div class="bg-white/10 backdrop-blur-sm p-4 rounded-lg border border-white/20 text-center">
                <div class="text-3xl font-bold">{{ unreadNotificationsCount }}</div>
                <div class="text-blue-100">No leídas</div>
              </div>

              <div v-if="systemNotificationsCount > 0"
                class="bg-white/10 backdrop-blur-sm p-4 rounded-lg border border-white/20 text-center">
                <div class="text-3xl font-bold">{{ systemNotificationsCount }}</div>
                <div class="text-blue-100">Sistema</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex-1 py-6">
        <div class="max-w-4xl mx-auto px-4">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm mb-6 overflow-hidden">
            <div class="flex overflow-x-auto hide-scrollbar">
              <button v-for="tab in notificationTabs" :key="tab.id" @click="setFilter(tab.id)" :class="[
                'px-4 py-3 flex items-center whitespace-nowrap transition-colors cursor-pointer',
                activeTab === tab.id
                  ? 'text-[#193CB8] dark:text-blue-200 border-b-2 border-[#193CB8] dark:border-blue-200 font-medium'
                  : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700'
              ]">
                <i :class="['bx mr-2', tab.icon]"></i>
                {{ tab.name }}
                <span v-if="tab.id === 'unread' && unreadNotificationsCount > 0"
                  class="ml-2 bg-[#193CB8] dark:bg-blue-200 text-white dark:text-gray-900 text-xs rounded-full px-2 py-0.5">
                  {{ unreadNotificationsCount }}
                </span>
                <span v-if="tab.id === 'system' && systemNotificationsCount > 0"
                  class="ml-2 bg-[#193CB8] dark:bg-blue-200 text-white dark:text-gray-900 text-xs rounded-full px-2 py-0.5">
                  {{ systemNotificationsCount }}
                </span>
              </button>
            </div>
          </div>

          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">
              {{ activeTab === 'all' ? 'Todas las notificaciones' :
                activeTab === 'unread' ? 'Notificaciones no leídas' :
                  activeTab === 'system' ? 'Notificaciones del sistema' :
                    activeTab === 'jobs' ? 'Ofertas de trabajo' : 'Notificaciones' }}
            </h2>
            <div class="flex items-center space-x-4">
              <button @click="markAllAsRead" v-if="filteredNotifications.some(n => !n.is_read)"
                class="text-[#193CB8] dark:text-blue-200 hover:text-[#142d8c] dark:hover:text-blue-300 text-sm font-medium flex items-center cursor-pointer">
                <i class='bx bx-check-double mr-1'></i>
                Marcar todas como leídas
              </button>
              <button @click="deleteAllNotifications" v-if="filteredNotifications.length > 0"
                class="text-red-500 dark:text-red-400 hover:text-red-600 dark:hover:text-red-500 text-sm font-medium flex items-center cursor-pointer">
                <i class="bx bx-trash mr-1"></i>
                Borrar todas
              </button>
            </div>
          </div>

          <!-- Lista de notificaciones filtradas -->
          <div class="space-y-3">
            <div v-for="notification in filteredNotifications" :key="notification.id" :class="[
              'bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transition-all duration-300 border border-gray-200 dark:border-gray-700',
              !notification.is_read ? 'border-l-4 border-l-[#193CB8] dark:border-l-blue-200' : '',
              !notification.is_read && isSystemNotification(notification) ? `border-l-4 ${getSystemNotificationClass(notification.type)}` : ''
            ]">
              <div class="p-4 flex">
                <div :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 mr-4',
                  getNotificationColor(notification.type)
                ]">
                  <i :class="['bx text-white text-xl', getNotificationIcon(notification.type)]"></i>
                </div>

                <div class="flex-1">
                  <div class="flex justify-between items-start">
                    <div>
                      <!-- Para notificaciones del sistema -->
                      <template v-if="isSystemNotification(notification)">
                        <h3 class="font-medium text-gray-800 dark:text-gray-200">
                          {{ notification.title || notification.message }}
                        </h3>
                        <p v-if="notification.content" class="text-gray-600 dark:text-gray-400 mt-1">
                          {{ notification.content }}
                        </p>
                      </template>
                      <!-- Para notificaciones normales -->
                      <template v-else>
                        <p class="text-gray-800 dark:text-gray-200 font-medium">{{ notification.message }}</p>
                      </template>
                    </div>
                    <div class="flex items-center space-x-2 ml-2">
                      <span class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(notification.created_at)
                        }}</span>
                      <div class="flex space-x-1">
                        <button v-if="!notification.is_read" @click="markAsRead(notification.id)"
                          class="text-gray-400 dark:text-gray-500 hover:text-[#193CB8] dark:hover:text-blue-200 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
                          title="Marcar como leída">
                          <i class='bx bx-check text-lg'></i>
                        </button>
                        <button @click="deleteNotification(notification.id)"
                          class="text-gray-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
                          title="Eliminar">
                          <i class='bx bx-x text-lg'></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="mt-2">
                    <div v-if="notification.type === 'message'" class="mt-2">
                      <button class="text-[#193CB8] dark:text-blue-200 text-sm hover:underline cursor-pointer"
                        @click="router.get('/mensajes')">Ver mensaje</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="isLoading" class="flex justify-center py-4">
              <div class="animate-pulse flex space-x-2">
                <div class="w-2 h-2 bg-[#193CB8] dark:bg-blue-200 rounded-full"></div>
                <div class="w-2 h-2 bg-[#193CB8] dark:bg-blue-200 rounded-full animation-delay-200"></div>
                <div class="w-2 h-2 bg-[#193CB8] dark:bg-blue-200 rounded-full animation-delay-400"></div>
              </div>
            </div>

            <div v-if="pagination.currentPage >= pagination.lastPage && !isLoading && filteredNotifications.length > 0"
              class="text-center py-8">
              <div
                class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class='bx bx-check-circle text-3xl text-[#193CB8] dark:text-blue-200'></i>
              </div>
              <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">¡Has visto todas las notificaciones!
              </h3>
              <p class="text-gray-500 dark:text-gray-400 mb-4">Estás al día con todas tus actualizaciones</p>
              <button @click="scrollToTop"
                class="text-[#193CB8] dark:text-blue-200 font-medium hover:underline flex items-center justify-center mx-auto">
                <i class='bx bx-chevron-up mr-1'></i>
                Volver arriba
              </button>
            </div>

            <div v-if="filteredNotifications.length === 0 && !isLoading" class="text-center py-8">
              <div
                class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class='bx bx-bell-off text-3xl text-[#193CB8] dark:text-blue-200'></i>
              </div>
              <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">No hay notificaciones</h3>
              <p class="text-gray-500 dark:text-gray-400 mb-4">Estás al día con todas tus actualizaciones</p>
              <button @click="router.get('/connect')"
                class="bg-[#193CB8] dark:bg-blue-200 text-white dark:text-gray-900 px-4 py-2 rounded-lg font-medium hover:bg-[#142d8c] dark:hover:bg-blue-300 transition-colors cursor-pointer">
                Explorar comunidad
              </button>
            </div>
          </div>
        </div>
      </div>

      <button v-show="showScrollTopButton" @click="scrollToTop"
        class="fixed bottom-6 right-6 w-12 h-12 rounded-full bg-[#193CB8] dark:bg-blue-200 text-white dark:text-gray-900 shadow-lg flex items-center justify-center hover:bg-[#142d8c] dark:hover:bg-blue-300 transition-all duration-300 z-50 animate-fade-in">
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