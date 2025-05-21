<script setup>
    import { ref, onMounted, computed, nextTick } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import axios from 'axios';
    import FullCalendar from '@fullcalendar/vue3';
    import dayGridPlugin from '@fullcalendar/daygrid';
    import timeGridPlugin from '@fullcalendar/timegrid';
    import interactionPlugin from '@fullcalendar/interaction';
    import Layout from '../Components/Layout.vue';
    
    // Configure Axios for Laravel CSRF and AJAX
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    const tokenMeta = document.head.querySelector('meta[name="csrf-token"]');
    if (tokenMeta) {
      axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.content;
    }
    
    // Referencia al componente FullCalendar
    const fullCalendarRef = ref(null);
    
    // Eventos iniciales que se cargan desde la base de datos (a través de Inertia)
    const events = ref([]);
    const page = usePage();
    
    // Estado para el modal de añadir evento
    const showEventModal = ref(false);
    // Model for new event including description
    const newEvent = ref({
      title: '',
      date: '',
      time: '',
      description: ''
    });
    
    // Add edit state
    const showEditModal = ref(false);
    const editEvent = ref({ id: null, title: '', date: '', time: '', description: '' });
    
    // Función para abrir el modal de evento
    const openEventModal = () => {
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, '0');
      const day = String(today.getDate()).padStart(2, '0');
      newEvent.value = {
        title: '',
        date: `${year}-${month}-${day}`,
        time: '12:00',
        description: ''
      };
      showEventModal.value = true;
    };
    
    // Función para cerrar el modal de evento
    const closeEventModal = () => {
      showEventModal.value = false;
    
      // Asegurarse de que cualquier selección en el calendario se limpie
      if (fullCalendarRef.value) {
        const calendarApi = fullCalendarRef.value.getApi();
        calendarApi.unselect();
      }
    };
    
    // Función para añadir un nuevo evento
    const addEvent = async () => {
      const dateTime = `${newEvent.value.date}T${newEvent.value.time}:00`;
      const eventData = { title: newEvent.value.title, start: dateTime, description: newEvent.value.description };
      try {
        const res = await axios.post('/calendario', eventData);
        events.value.push({ id: res.data.event.id, title: res.data.event.title, start: res.data.event.start_date, description: res.data.event.description });
        closeEventModal();
      } catch (e) {
        console.error(e);
      }
    };
    
    // Open edit modal from eventClick
    const openEditModal = info => {
      info.jsEvent?.preventDefault();
      const ev = info.event;
      const dt = ev.start.toISOString();
      const [date, time] = dt.split('T');
      editEvent.value = {
        id: ev.id,
        title: ev.title,
        date,
        time: time.slice(0,5),
        description: ev.extendedProps.description || ''
      };
      showEditModal.value = true;
    };
    
    // Función para actualizar el evento en el servidor
    const updateEvent = async () => {
      const datetime = `${editEvent.value.date}T${editEvent.value.time}:00`;
      try {
        const res = await axios.put(`/calendario/${editEvent.value.id}`, { title: editEvent.value.title, start: datetime, description: editEvent.value.description });
        const idx = events.value.findIndex(e => e.id == editEvent.value.id);
        if (idx !== -1) {
          events.value[idx] = { id: res.data.event.id, title: res.data.event.title, start: res.data.event.start_date, description: res.data.event.description };
        }
        showEditModal.value = false;
      } catch (e) {
        console.error(e);
      }
    };
    
    // Delete event
    const deleteEvent = async () => {
      try {
        await axios.delete(`/calendario/${editEvent.value.id}`);
        // Remove from local state
        events.value = events.value.filter(e => e.id != editEvent.value.id);
        // Remove from calendar UI
        if (fullCalendarRef.value) {
          const calApi = fullCalendarRef.value.getApi();
          const ev = calApi.getEventById(String(editEvent.value.id));
          if (ev) ev.remove();
        }
        showEditModal.value = false;
      } catch (e) {
        console.error(e);
      }
    };
    
    // Configuración del calendario
    const calendarOptions = computed(() => ({
      plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
      initialView: 'dayGridMonth',
      height: 'auto',
      contentHeight: 'auto',
      aspectRatio: 1.5,
      views: {
        dayGridMonth: { 
          fixedWeekCount: false 
        },
        timeGridWeek: {
          height: '100%'
        },
        timeGridDay: {
          height: '100%'
        }
      },
      events: events.value,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      editable: true,
      selectable: true,
      eventStartEditable: true,
      eventDurationEditable: true,
      dateClick: (info) => {
        if (info.jsEvent) info.jsEvent.preventDefault();
       
        const [year, month, day] = info.dateStr.split('-');
        newEvent.value = {
          title: '',
          date: info.dateStr,
          time: '12:00',
          description: ''
        };
    
        nextTick(() => {
          showEventModal.value = true;
        });
    
        // Limpiar cualquier selección
        if (fullCalendarRef.value) {
          const calendarApi = fullCalendarRef.value.getApi();
          calendarApi.unselect();
        }
    
        return false; // Indicar que hemos manejado el evento
      },
    
      select: (info) => {
        if (info.jsEvent) info.jsEvent.preventDefault();
       
        newEvent.value = {
          title: '',
          date: info.startStr,
          time: '12:00',
          description: ''
        };
    
        nextTick(() => {
          showEventModal.value = true;
        });
    
        // Limpiar la selección
        if (fullCalendarRef.value) {
          const calendarApi = fullCalendarRef.value.getApi();
          calendarApi.unselect();
        }
    
        return false; // Indicar que hemos manejado el evento
      },
    
      eventClick: openEditModal,
      eventDrop: async info => {
        const ev = info.event;
        const dt = ev.start.toISOString();
        try {
          await axios.put(`/calendario/${ev.id}`, { start: dt });
        } catch { info.revert(); }
      }
    }));
    
    // Cargar los eventos cuando el componente se monta
    onMounted(() => {
      events.value = page.props.events.map(e => ({
        id: e.id,
        title: e.title,
        start: e.start_date,
        description: e.description || ''
      }));
    
      nextTick(() => {
        const popups = document.querySelectorAll('.fc-popover, .fc-event-mirror');
        popups.forEach(popup => {
          popup.style.display = 'none';
        });
      });
    });
</script>

<template>
  <Layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col">
      <!-- Hero Section -->
      <div class="bg-[#193CB8] dark:bg-[#142d8c] text-white py-6">
        <div class="w-full max-w-[95%] mx-auto px-2">
          <div class="flex flex-col md:flex-row items-center mx-auto justify-between">
            <div class="mb-4 md:mb-0 md:mr-8">
              <h1 class="text-3xl font-bold mb-2">Calendario de Eventos</h1>
              <p class="text-blue-100 mb-2">Gestiona tus eventos y mantén tu agenda organizada</p>
            </div>
            
            <!-- Stats -->
            <div class="bg-white/10 dark:bg-gray-800/50 backdrop-blur-sm p-4 rounded-lg border border-white/20 dark:border-gray-700 text-center">
              <div class="text-3xl font-bold">{{ events.length }}</div>
              <div class="text-blue-100 dark:text-blue-300">Eventos programados</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex-1 flex flex-col">
        <div class="w-full max-w-[95%] mx-auto px-2 py-4 flex flex-col flex-grow">
          <!-- Botón para añadir evento (visible en pantallas medianas y grandes) -->
          <div class="hidden md:flex justify-end mb-4">
            <button
              @click="openEventModal"
              class="bg-[#193CB8] hover:bg-[#142d8c] dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-medium py-2 px-4 rounded-lg flex items-center shadow-sm transition-colors"
            >
              <i class='bx bx-plus-circle mr-2'></i>
              Añadir Evento
            </button>
          </div>

          <!-- Calendario Container -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
            <!-- Calendario -->
            <div class="p-2 md:p-4 w-full h-full">
              <FullCalendar
                ref="fullCalendarRef"
                v-if="calendarOptions"
                :options="calendarOptions"
                class="fc-theme-standard h-full w-full"
              />
            </div>
          </div>
          
          <!-- Botón flotante para añadir evento (visible solo en móvil) -->
          <div class="md:hidden fixed bottom-6 right-6 z-10">
            <button
              @click="openEventModal"
              class="bg-[#193CB8] hover:bg-[#142d8c] dark:bg-blue-700 dark:hover:bg-blue-800 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-colors"
            >
              <i class='bx bx-plus text-2xl'></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Modal para añadir evento -->
      <div v-if="showEventModal" class="fixed inset-0 bg-black/50 dark:bg-black/80 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl w-full max-w-md border border-gray-200 dark:border-gray-700 m-4">
          <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">Añadir Nuevo Evento</h3>
          <form @submit.prevent="addEvent">
            <!-- Título del evento -->
            <div class="mb-4">
              <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título</label>
              <input
                type="text"
                id="title"
                v-model="newEvent.title"
                class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-[#193CB8] dark:focus:ring-blue-500"
                required
              >
            </div>
  
            <!-- Fecha del evento -->
            <div class="mb-4">
              <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha</label>
              <input
                type="date"
                id="date"
                v-model="newEvent.date"
                class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-[#193CB8] dark:focus:ring-blue-500"
                required
              >
            </div>
  
            <!-- Hora del evento -->
            <div class="mb-4">
              <label for="time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hora</label>
              <input
                type="time"
                id="time"
                v-model="newEvent.time"
                class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-[#193CB8] dark:focus:ring-blue-500"
                required
              >
            </div>
  
            <!-- Descripción -->
            <div class="mb-6">
              <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
              <textarea
                id="description"
                v-model="newEvent.description"
                class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-[#193CB8] dark:focus:ring-blue-500"
                rows="3"
              ></textarea>
            </div>
  
            <!-- Botones de acción -->
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="closeEventModal"
                class="px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
              >
                Cancelar
              </button>
              <button
                type="submit"
                class="px-4 py-2 rounded-lg text-white bg-[#193CB8] hover:bg-[#142d8c] dark:bg-blue-700 dark:hover:bg-blue-800 transition-colors"
              >
                Guardar
              </button>
            </div>
          </form>
        </div>
      </div>
  
      <!-- Modal para editar evento -->
      <div v-if="showEditModal" class="fixed inset-0 bg-black/50 dark:bg-black/80 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl w-full max-w-md border border-gray-200 dark:border-gray-700 m-4">
          <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">Editar Evento</h3>
          <form @submit.prevent="updateEvent">
            <!-- Título del evento -->
            <div class="mb-4">
              <label for="edit-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título</label>
              <input
                type="text"
                id="edit-title"
                v-model="editEvent.title"
                class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-[#193CB8] dark:focus:ring-blue-500"
                required
              >
            </div>
  
            <!-- Fecha del evento -->
            <div class="mb-4">
              <label for="edit-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha</label>
              <input
                type="date"
                id="edit-date"
                v-model="editEvent.date"
                class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-[#193CB8] dark:focus:ring-blue-500"
                required
              >
            </div>
  
            <!-- Hora del evento -->
            <div class="mb-4">
              <label for="edit-time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hora</label>
              <input
                type="time"
                id="edit-time"
                v-model="editEvent.time"
                class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-[#193CB8] dark:focus:ring-blue-500"
                required
              >
            </div>
  
            <!-- Descripción -->
            <div class="mb-6">
              <label for="edit-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
              <textarea
                id="edit-description"
                v-model="editEvent.description"
                class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-[#193CB8] dark:focus:ring-blue-500"
                rows="3"
              ></textarea>
            </div>
  
            <!-- Botones de acción -->
            <div class="flex justify-between">
              <button
                type="button"
                @click="deleteEvent"
                class="px-4 py-2 rounded-lg text-white bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 transition-colors"
              >
                Eliminar
              </button>
              <div class="flex space-x-3">
                <button
                  type="button"
                  @click="showEditModal = false"
                  class="px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 rounded-lg text-white bg-[#193CB8] hover:bg-[#142d8c] dark:bg-blue-700 dark:hover:bg-blue-800 transition-colors"
                >
                  Guardar
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </Layout>
</template>
  
<style scoped>
/* Only hide default popovers and mirrors */
:deep(.fc-popover), :deep(.fc-event-mirror) {
  pointer-events: none;
}

/* Estilos para el calendario */
:deep(.fc) {
  --fc-border-color: #e5e7eb;
  --fc-button-bg-color: #ffffff;
  --fc-button-border-color: #d1d5db;
  --fc-button-text-color: #374151;
  --fc-button-hover-bg-color: #f3f4f6;
  --fc-button-hover-border-color: #9ca3af;
  --fc-button-active-bg-color: #193CB8;
  --fc-button-active-border-color: #193CB8;
  --fc-button-active-text-color: #ffffff;
  --fc-event-bg-color: #193CB8;
  --fc-event-border-color: #193CB8;
  --fc-today-bg-color: rgba(25,60,184,0.1);
  height: 100%;
  width: 100%;
}

:deep(.fc-view-harness) {
  height: 100% !important;
  width: 100% !important;
}

:deep(.fc-scrollgrid) {
  width: 100% !important;
}

:deep(.fc-scrollgrid-sync-table) {
  height: 100% !important;
  width: 100% !important;
}

:deep(.fc-button-primary) {
  text-transform: capitalize;
  font-weight: 500;
  border-radius: 0.375rem;
  padding: 0.375rem 0.75rem;
}

:deep(.fc-button-primary:not(:disabled).fc-button-active),
:deep(.fc-button-primary:not(:disabled):active) {
  background-color: #193CB8;
  border-color: #193CB8;
  color: #ffffff !important;
}

:deep(.fc-daygrid-day.fc-day-today) {
  background-color: rgba(25,60,184,0.1);
}

:deep(.fc-col-header-cell) {
  padding: 0.5rem 0;
  font-weight: 600;
}

:deep(.fc-theme-standard td, .fc-theme-standard th) {
  border-color: #e5e7eb;
}

/* Dark mode styles */
.dark :deep(.fc) {
  --fc-border-color: #374151;
  --fc-button-bg-color: #1f2937;
  --fc-button-border-color: #374151;
  --fc-button-text-color: #d1d5db;
  --fc-button-hover-bg-color: #374151;
  --fc-button-hover-border-color: #4b5563;
  --fc-button-active-bg-color: #1d4ed8;
  --fc-button-active-border-color: #1d4ed8;
  --fc-event-bg-color: #1d4ed8;
  --fc-event-border-color: #1d4ed8;
  --fc-today-bg-color: rgba(30,41,59,0.2);
  --fc-page-bg-color: #1f2937;
  color: #d1d5db;
}

.dark :deep(.fc-daygrid-day.fc-day-today) {
  background-color: rgba(30,41,59,0.2);
}

.dark :deep(.fc-theme-standard td, .fc-theme-standard th) {
  border-color: #374151;
}

/* Asegurar que el calendario ocupe todo el espacio disponible */
:deep(.fc-view) {
  height: 100% !important;
  width: 100% !important;
}

/* Ajustes para dispositivos móviles */
@media (max-width: 768px) {
  :deep(.fc-header-toolbar) {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  :deep(.fc-toolbar-chunk) {
    display: flex;
    justify-content: center;
  }
  
  :deep(.fc-view-harness) {
    min-height: 400px;
  }
}
</style>