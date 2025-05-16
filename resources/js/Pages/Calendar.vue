<script setup>
    import { ref, onMounted, computed, nextTick } from 'vue';
    import { usePage, router } from '@inertiajs/vue3';
    import FullCalendar from '@fullcalendar/vue3';
    import dayGridPlugin from '@fullcalendar/daygrid';
    import timeGridPlugin from '@fullcalendar/timegrid';
    import interactionPlugin from '@fullcalendar/interaction';
    import Layout from '../Components/Layout.vue';
    
    // Referencia al componente FullCalendar
    const fullCalendarRef = ref(null);
    
    // Eventos iniciales que se cargan desde la base de datos (a través de Inertia)
    const events = ref([]);
    const page = usePage();
    
    // Estado para el modal de añadir evento
    const showEventModal = ref(false);
    const newEvent = ref({
      title: '',
      date: '',
      time: ''
    });
    
    // Función para abrir el modal de evento
    const openEventModal = () => {
      // Establecer la fecha actual como valor predeterminado
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, '0');
      const day = String(today.getDate()).padStart(2, '0');
      
      newEvent.value = {
        title: '',
        date: `${year}-${month}-${day}`,
        time: '12:00'
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
    const addEvent = () => {
      // Combinar fecha y hora
      const dateTime = `${newEvent.value.date}T${newEvent.value.time}:00`;
    
      // Crear objeto de evento para enviar al servidor
      const eventData = {
        title: newEvent.value.title,
        start: dateTime
      };
    
      // Enviar datos al servidor usando Inertia
      router.post('/calendar', eventData, {
        onSuccess: () => {
          // Añadir el evento a la lista local para actualización inmediata
          events.value.push(eventData);
          
          // Cerrar el modal
          closeEventModal();
        }
      });
    };
    
    // Configuración del calendario
    const calendarOptions = computed(() => ({
      plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
      initialView: 'dayGridMonth',
      events: events.value,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      // Configuración para prevenir el diálogo predeterminado
      editable: false, // Desactivar la edición para evitar diálogos predeterminados
      selectable: true, // Permitir selección de fechas
      selectMirror: false, // No mostrar "espejo" al seleccionar
      navLinks: false, // Desactivar enlaces de navegación
      eventStartEditable: false, // No permitir editar el inicio del evento
      eventDurationEditable: false, // No permitir editar la duración del evento
    
      // Manejadores de eventos personalizados
      dateClick: (info) => {
        if (info.jsEvent) info.jsEvent.preventDefault();
       
        const [year, month, day] = info.dateStr.split('-');
        newEvent.value = {
          title: '',
          date: info.dateStr,
          time: '12:00'
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
          time: '12:00'
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
      }
    }));
    
    // Cargar los eventos cuando el componente se monta
    onMounted(() => {
      events.value = page.props.events;
    
      nextTick(() => {
        const popups = document.querySelectorAll('.fc-popover, .fc-event-mirror');
        popups.forEach(popup => {
          popup.style.display = 'none';
        });
      });
    });
    </script>
<template>

      <Layout >
  
      <div class="flex flex-col justify-center items-center py-5 gap-4">
        <div class="w-full max-w-3xl">
          <!-- Botón para añadir evento -->
          <div class="mb-4 flex justify-end">
            <button
              @click="openEventModal"
              class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
              Añadir Evento
            </button>
          </div>
  
          <!-- Calendario -->
          <div id="calendar-container">
            <FullCalendar
              ref="fullCalendarRef"
              v-if="calendarOptions"
              :options="calendarOptions"
            />
          </div>
        </div>
      </div>
  
      <!-- Modal para añadir evento -->
      <div v-if="showEventModal" class="fixed inset-0 bg-gray-600/80 backdrop-blur-sm  bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Añadir Nuevo Evento</h3>
            <div class="mt-2 px-7 py-3">
              <form @submit.prevent="addEvent">
                <!-- Título del evento -->
                <div class="mb-4">
                  <label for="title" class="block text-sm font-medium text-gray-700 text-left">Título</label>
                  <input
                    type="text"
                    id="title"
                    v-model="newEvent.title"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    required
                  >
                </div>
  
                <!-- Fecha del evento -->
                <div class="mb-4">
                  <label for="date" class="block text-sm font-medium text-gray-700 text-left">Fecha</label>
                  <input
                    type="date"
                    id="date"
                    v-model="newEvent.date"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    required
                  >
                </div>
  
                <!-- Hora del evento -->
                <div class="mb-4">
                  <label for="time" class="block text-sm font-medium text-gray-700 text-left">Hora</label>
                  <input
                    type="time"
                    id="time"
                    v-model="newEvent.time"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    required
                  >
                </div>
  
                <!-- Botones de acción -->
                <div class="flex justify-end space-x-3">
                  <button
                    type="button"
                    @click="closeEventModal"
                    class="bg-gray-200 px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    class="bg-blue-600 px-4 py-2 rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    Guardar
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  </template>
  
 
  
  <style scoped>
  /* Ocultar los diálogos predeterminados de FullCalendar */
  :deep(.fc-popover),
  :deep(.fc-event-mirror),
  :deep(.fc-daygrid-event-harness) {
    pointer-events: none;
  }
  
  :deep(.fc-popover) {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
  }
  
  :deep(.fc) {
    z-index: 10;
    position: relative;
  }
  
  .fixed {
    z-index: 9999 !important;
  }
  </style>
  