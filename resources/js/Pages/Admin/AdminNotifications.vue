<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AdminLayout from './Layouts/AdminLayout.vue';
import Table from '@/Components/Admin/Table.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';

const page = usePage();

// Notification states
const notifications = ref([
    {
        id: 1,
        title: 'Actualización de plataforma',
        content: 'La plataforma estará en mantenimiento el día 15 de junio de 2023 de 2:00 a 4:00 AM.',
        type: 'info',
        target: 'all',
        status: 'active',
        created_at: '2023-06-10T10:30:00',
        expires_at: '2023-06-16T23:59:59',
        read_count: 145,
        total_recipients: 200
    },
    {
        id: 2,
        title: 'Nuevas ofertas de empleo disponibles',
        content: 'Se han publicado 15 nuevas ofertas de empleo en el sector de informática.',
        type: 'success',
        target: 'area',
        target_details: 'Informatica',
        status: 'active',
        created_at: '2023-06-08T14:20:00',
        expires_at: '2023-06-30T23:59:59',
        read_count: 78,
        total_recipients: 120
    },
    {
        id: 3,
        title: 'Recordatorio: Actualiza tu perfil',
        content: 'Recuerda mantener tu perfil actualizado para aumentar tus posibilidades de ser contactado por empresas.',
        type: 'warning',
        target: 'role',
        target_details: 'Alumno',
        status: 'expired',
        created_at: '2023-05-20T09:15:00',
        expires_at: '2023-06-05T23:59:59',
        read_count: 95,
        total_recipients: 150
    },
    {
        id: 4,
        title: 'Evento de networking',
        content: 'Te invitamos al evento de networking que se realizará el próximo viernes en el campus.',
        type: 'info',
        target: 'custom',
        target_details: 'Selección manual',
        status: 'scheduled',
        created_at: '2023-06-12T16:45:00',
        expires_at: '2023-06-25T23:59:59',
        read_count: 0,
        total_recipients: 50
    }
]);

// Filters
const filters = ref({
    title: '',
    type: '',
    status: '',
    target: '',
    pagination: '10',
});

// Modals
const createNotificationModal = ref(false);
const deleteNotificationModal = ref(false);
const previewNotificationModal = ref(false);
const selectedNotificationId = ref(null);

// New notification form
const newNotification = ref({
    title: '',
    content: '',
    type: 'info',
    target: 'all',
    target_details: '',
    expires_at: '',
    schedule_for: null,
});

// Selected users for custom targeting
const selectedUsers = ref([]);

// Animation state
const isLoaded = ref(false);

// Computed properties
const selectedNotification = computed(() => {
    return notifications.value.find(n => n.id === selectedNotificationId.value) || {};
});

const filteredNotifications = computed(() => {
    let filtered = [...notifications.value];

    if (filters.value.title) {
        filtered = filtered.filter(n =>
            n.title.toLowerCase().includes(filters.value.title.toLowerCase())
        );
    }

    if (filters.value.type) {
        filtered = filtered.filter(n => n.type === filters.value.type);
    }

    if (filters.value.status) {
        filtered = filtered.filter(n => n.status === filters.value.status);
    }

    if (filters.value.target) {
        filtered = filtered.filter(n => n.target === filters.value.target);
    }

    return filtered;
});

// Methods
const searchNotifications = () => {
    // In a real implementation, this would call an API
    console.log('Searching with filters:', filters.value);
};

const clearFilters = () => {
    filters.value = {
        title: '',
        type: '',
        status: '',
        target: '',
        pagination: '10',
    };
    searchNotifications();
};

const openCreateModal = () => {
    newNotification.value = {
        title: '',
        content: '',
        type: 'info',
        target: 'all',
        target_details: '',
        expires_at: '',
        schedule_for: null,
    };
    createNotificationModal.value = true;
};

const confirmDelete = (id) => {
    selectedNotificationId.value = id;
    deleteNotificationModal.value = true;
};

const deleteNotification = () => {
    // In a real implementation, this would call an API
    notifications.value = notifications.value.filter(n => n.id !== selectedNotificationId.value);
    deleteNotificationModal.value = false;
};

const previewNotification = (id) => {
    selectedNotificationId.value = id;
    previewNotificationModal.value = true;
};

const createNotification = () => {
    // In a real implementation, this would call an API
    const newId = Math.max(...notifications.value.map(n => n.id)) + 1;

    notifications.value.unshift({
        id: newId,
        ...newNotification.value,
        status: newNotification.value.schedule_for ? 'scheduled' : 'active',
        created_at: new Date().toISOString(),
        read_count: 0,
        total_recipients: newNotification.value.target === 'all' ? 200 :
            newNotification.value.target === 'area' ? 120 :
                newNotification.value.target === 'role' ? 150 :
                    selectedUsers.value.length
    });

    createNotificationModal.value = false;
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric'
    });
};

// Type options
const notificationTypes = [
    { value: 'info', label: 'Información', icon: 'bx-info-circle', color: 'text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-300' },
    { value: 'success', label: 'Éxito', icon: 'bx-check-circle', color: 'text-green-600 bg-green-100 dark:bg-green-900 dark:text-green-300' },
    { value: 'warning', label: 'Advertencia', icon: 'bx-error', color: 'text-amber-600 bg-amber-100 dark:bg-amber-900 dark:text-amber-300' },
    { value: 'error', label: 'Error', icon: 'bx-x-circle', color: 'text-red-600 bg-red-100 dark:bg-red-900 dark:text-red-300' }
];

// Target options
const targetOptions = [
    { value: 'all', label: 'Todos los usuarios', icon: 'bx-globe' },
    { value: 'role', label: 'Por rol', icon: 'bx-shield-quarter' },
    { value: 'area', label: 'Por área formativa', icon: 'bx-book' },
    { value: 'custom', label: 'Selección manual', icon: 'bx-user-check' }
];

// Status options
const statusOptions = [
    { value: 'active', label: 'Activa', color: 'text-green-600 bg-green-100 dark:bg-green-900 dark:text-green-300' },
    { value: 'scheduled', label: 'Programada', color: 'text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-300' },
    { value: 'expired', label: 'Expirada', color: 'text-gray-600 bg-gray-100 dark:bg-gray-700 dark:text-gray-300' }
];

// Role options for targeting
const roleOptions = [
    { value: 'Alumno', label: 'Alumno' },
    { value: 'Exalumno', label: 'Exalumno' },
    { value: 'Empresa', label: 'Empresa' },
    { value: 'Admin', label: 'Administrador' }
];

// Area options for targeting
const areaOptions = [
    { value: 'Informatica', label: 'Informática' },
    { value: 'Marketing', label: 'Marketing' },
    { value: 'Automocion', label: 'Automoción' }
];

// Close modal when clicking outside
const closeOnClickOutside = (e, modalRef, modalState) => {
    if (modalRef.value && !modalRef.value.contains(e.target)) {
        modalState.value = false;
    }
};

// Modal refs for click outside detection
const createModalRef = ref(null);
const deleteModalRef = ref(null);
const previewModalRef = ref(null);

// Add event listeners for click outside
watch(createNotificationModal, (newVal) => {
    if (newVal) {
        setTimeout(() => {
            document.addEventListener('mousedown', (e) => closeOnClickOutside(e, createModalRef, createNotificationModal));
        }, 0);
    } else {
        document.removeEventListener('mousedown', (e) => closeOnClickOutside(e, createModalRef, createNotificationModal));
    }
});

watch(deleteNotificationModal, (newVal) => {
    if (newVal) {
        setTimeout(() => {
            document.addEventListener('mousedown', (e) => closeOnClickOutside(e, deleteModalRef, deleteNotificationModal));
        }, 0);
    } else {
        document.removeEventListener('mousedown', (e) => closeOnClickOutside(e, deleteModalRef, deleteNotificationModal));
    }
});

watch(previewNotificationModal, (newVal) => {
    if (newVal) {
        setTimeout(() => {
            document.addEventListener('mousedown', (e) => closeOnClickOutside(e, previewModalRef, previewNotificationModal));
        }, 0);
    } else {
        document.removeEventListener('mousedown', (e) => closeOnClickOutside(e, previewModalRef, previewNotificationModal));
    }
});

// Trigger animations after component mounts
onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);
});
</script>

<template>
    <AdminLayout>
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 rounded-xl shadow-sm p-8 mb-6 border border-gray-200 dark:border-gray-700"
            :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-4 opacity-0': !isLoaded }"
            style="transition: all 0.5s ease; transition-delay: 100ms;">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <i class='bx bx-bell text-gray-700 dark:text-gray-300 text-xl'></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Gestión de Notificaciones</h1>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        Envía notificaciones a los usuarios de Alumni.
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6 border border-gray-200 dark:border-gray-700"
            :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-4 opacity-0': !isLoaded }"
            style="transition: all 0.5s ease; transition-delay: 200ms;">
            <div class="flex items-center justify-between mb-5 pb-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                    <i class='bx bx-filter mr-2 text-gray-600 dark:text-gray-400'></i>
                    Filtros de búsqueda
                </h2>
                <div class="flex gap-3">
                    <button @click="clearFilters"
                        class="bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center border border-gray-200 dark:border-gray-600 shadow-sm">
                        <i class='bx bx-reset mr-2'></i>
                        Reiniciar Filtros
                    </button>
                    <button @click="openCreateModal"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center shadow-sm">
                        <i class='bx bx-plus mr-2'></i>
                        Nueva Notificación
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-gray-500 ml-1">Título</label>
                    <InputWithIcon type="text" icon="search" placeholder="Buscar por título" class="w-full"
                        v-model="filters.title" @input="searchNotifications" />
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-gray-500 ml-1">Tipo</label>
                    <div class="w-full relative">
                        <i
                            class='bx bx-category mr-2 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl'></i>
                        <select v-model="filters.type" @input="searchNotifications"
                            class="w-full h-12 pl-10 p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-300 transition duration-200 bg-white appearance-none">
                            <option value="">Todos los tipos</option>
                            <option v-for="type in notificationTypes" :key="type.value" :value="type.value">{{
                                type.label }}</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-gray-500 ml-1">Estado</label>
                    <div class="w-full relative">
                        <i
                            class='bx bx-check-circle mr-2 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl'></i>
                        <select v-model="filters.status" @input="searchNotifications"
                            class="w-full h-12 pl-10 p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-300 transition duration-200 bg-white appearance-none">
                            <option value="">Todos los estados</option>
                            <option v-for="status in statusOptions" :key="status.value" :value="status.value">{{
                                status.label }}</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-gray-500 ml-1">Destinatarios</label>
                    <div class="w-full relative">
                        <i
                            class='bx bx-user-circle mr-2 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl'></i>
                        <select v-model="filters.target" @input="searchNotifications"
                            class="w-full h-12 pl-10 p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-300 transition duration-200 bg-white appearance-none">
                            <option value="">Todos los destinatarios</option>
                            <option v-for="target in targetOptions" :key="target.value" :value="target.value">{{
                                target.label }}</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6 border border-gray-200 dark:border-gray-700"
            :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-4 opacity-0': !isLoaded }"
            style="transition: all 0.5s ease; transition-delay: 300ms;">
            <div class="flex justify-between items-center mb-5 pb-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                    <i class='bx bx-bell mr-2 text-gray-600 dark:text-gray-400'></i>
                    Listado de Notificaciones
                </h2>
                <div class="flex items-center">
                    <div
                        class="flex items-center justify-center h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 mr-3">
                        <i class='bx bx-info-circle text-gray-600 dark:text-gray-300'></i>
                    </div>
                    <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                        Mostrando {{ filteredNotifications.length }} notificaciones
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <Table
                    :headers="['Título', 'Tipo', 'Destinatarios', 'Estado', 'Fecha de creación', 'Estadísticas', 'Acciones']">
                    <tr v-if="filteredNotifications.length === 0">
                        <td class="py-12 px-4" colspan="7">
                            <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-full mb-4">
                                    <i class='bx bx-bell-off text-5xl text-gray-400 dark:text-gray-600'></i>
                                </div>
                                <p class="text-lg font-medium text-gray-700 dark:text-gray-200">No se encontraron
                                    notificaciones</p>
                                <p class="text-sm mt-1 max-w-sm text-center text-gray-500 dark:text-gray-400">No hay
                                    resultados que coincidan con los criterios de búsqueda. Intente con otros filtros o
                                    cree una nueva notificación.</p>
                                <div class="mt-4 flex gap-3">
                                    <button @click="clearFilters"
                                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition duration-200">
                                        Reiniciar filtros
                                    </button>
                                    <button @click="openCreateModal"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-200">
                                        <i class='bx bx-plus mr-1'></i> Nueva notificación
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr v-else v-for="notification in filteredNotifications" :key="notification.id"
                        class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors duration-150">
                        <td class="py-4 px-4">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ notification.title
                                    }}</div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                    :class="notificationTypes.find(t => t.value === notification.type)?.color">
                                    <i
                                        :class="`bx ${notificationTypes.find(t => t.value === notification.type)?.icon} mr-1`"></i>
                                    {{notificationTypes.find(t => t.value === notification.type)?.label}}
                                </span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center">
                                <span class="text-sm text-gray-700 dark:text-gray-200">
                                    <i
                                        :class="`bx ${targetOptions.find(t => t.value === notification.target)?.icon} mr-1`"></i>
                                    {{targetOptions.find(t => t.value === notification.target)?.label}}
                                </span>
                                <span v-if="notification.target_details"
                                    class="ml-1 text-xs text-gray-500 dark:text-gray-400">
                                    ({{ notification.target_details }})
                                </span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                :class="statusOptions.find(s => s.value === notification.status)?.color">
                                <i class="bx bx-radio-circle-marked mr-1"></i>
                                {{statusOptions.find(s => s.value === notification.status)?.label}}
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="text-sm text-gray-700 dark:text-gray-200">{{ formatDate(notification.created_at)
                                }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Expira: {{ formatDate(notification.expires_at) }}
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <div
                                    class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 px-2 py-1 rounded-md text-xs">
                                    <i class='bx bx-user mr-1'></i>{{ notification.read_count }}/{{
                                    notification.total_recipients }}
                                </div>
                                <div
                                    class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 px-2 py-1 rounded-md text-xs">
                                    {{ Math.round((notification.read_count / notification.total_recipients) * 100) }}%
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-1">
                                <button
                                    class="p-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900 transition-colors duration-150"
                                    @click="previewNotification(notification.id)" title="Ver detalles">
                                    <i class='bx bx-show text-blue-600 dark:text-blue-400'></i>
                                </button>
                                <button
                                    class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900 transition-colors duration-150"
                                    @click="confirmDelete(notification.id)" title="Eliminar">
                                    <i class='bx bx-trash text-red-500 dark:text-red-400'></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </Table>
            </div>
        </div>

        <!-- Create Notification Modal -->
        <div v-if="createNotificationModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75  transition-opacity"
                    aria-hidden="true"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full"
                    ref="createModalRef">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 sm:mx-0 sm:h-10 sm:w-10">
                                <i class='bx bx-bell text-blue-600 dark:text-blue-400 text-xl'></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                    id="modal-title">
                                    Nueva Notificación
                                </h3>

                                <div class="mt-6 space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título
                                            de la notificación</label>
                                        <InputWithIcon type="text" icon="edit"
                                            placeholder="Ingrese un título descriptivo" class="w-full"
                                            v-model="newNotification.title" />
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contenido</label>
                                        <textarea v-model="newNotification.content" rows="4"
                                            class="w-full p-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition duration-200"
                                            placeholder="Escriba el mensaje de la notificación..."></textarea>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo
                                                de notificación</label>
                                            <div class="flex flex-wrap gap-3">
                                                <label v-for="type in notificationTypes" :key="type.value"
                                                    class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors"
                                                    :class="newNotification.type === type.value ?
                                                        'border-blue-500 bg-blue-50 dark:bg-blue-900/30 dark:border-blue-600' :
                                                        'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800'">
                                                    <input type="radio" :value="type.value"
                                                        v-model="newNotification.type" class="hidden" />
                                                    <i :class="`bx ${type.icon} mr-2 ${type.color.split(' ')[0]}`"></i>
                                                    <span
                                                        class="text-sm font-medium text-gray-700 dark:text-gray-300">{{
                                                        type.label }}</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Destinatarios</label>
                                            <div class="space-y-2">
                                                <label v-for="target in targetOptions" :key="target.value"
                                                    class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors"
                                                    :class="newNotification.target === target.value ?
                                                        'border-blue-500 bg-blue-50 dark:bg-blue-900/30 dark:border-blue-600' :
                                                        'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800'">
                                                    <input type="radio" :value="target.value"
                                                        v-model="newNotification.target" class="hidden" />
                                                    <i
                                                        :class="`bx ${target.icon} mr-2 text-blue-600 dark:text-blue-400`"></i>
                                                    <span
                                                        class="text-sm font-medium text-gray-700 dark:text-gray-300">{{
                                                        target.label }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Target details based on selection -->
                                    <div v-if="newNotification.target === 'role'">
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Seleccionar
                                            rol</label>
                                        <div class="w-full relative">
                                            <i
                                                class='bx bx-shield-quarter mr-2 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl'></i>
                                            <select v-model="newNotification.target_details"
                                                class="w-full h-12 pl-10 p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition duration-200 bg-white dark:bg-gray-800 appearance-none">
                                                <option value="">Seleccionar rol</option>
                                                <option v-for="role in roleOptions" :key="role.value"
                                                    :value="role.value">{{ role.label }}</option>
                                            </select>
                                            <div
                                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="newNotification.target === 'area'">
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Seleccionar
                                            área formativa</label>
                                        <div class="w-full relative">
                                            <i
                                                class='bx bx-book mr-2 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl'></i>
                                            <select v-model="newNotification.target_details"
                                                class="w-full h-12 pl-10 p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition duration-200 bg-white dark:bg-gray-800 appearance-none">
                                                <option value="">Seleccionar área</option>
                                                <option v-for="area in areaOptions" :key="area.value"
                                                    :value="area.value">{{ area.label }}</option>
                                            </select>
                                            <div
                                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="newNotification.target === 'custom'">
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Seleccionar
                                            usuarios</label>
                                        <div
                                            class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                                En una implementación real, aquí habría un selector de usuarios con
                                                búsqueda.
                                            </p>
                                            <button
                                                class="px-4 py-2 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-lg text-sm font-medium hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors">
                                                <i class='bx bx-user-plus mr-1'></i> Seleccionar usuarios
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha
                                                de expiración</label>
                                            <div class="w-full relative">
                                                <i
                                                    class='bx bx-calendar mr-2 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl'></i>
                                                <input type="datetime-local" v-model="newNotification.expires_at"
                                                    class="w-full h-12 pl-10 p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition duration-200" />
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Programar
                                                envío (opcional)</label>
                                            <div class="w-full relative">
                                                <i
                                                    class='bx bx-time mr-2 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl'></i>
                                                <input type="datetime-local" v-model="newNotification.schedule_for"
                                                    class="w-full h-12 pl-10 p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition duration-200" />
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                Deje en blanco para enviar inmediatamente.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="createNotification"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Enviar Notificación
                        </button>
                        <button type="button" @click="createNotificationModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Notification Modal -->
        <div v-if="deleteNotificationModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity"
                    aria-hidden="true"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full"
                    ref="deleteModalRef">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                                <i class='bx bx-trash text-red-600 dark:text-red-400 text-xl'></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                    id="modal-title">
                                    Eliminar Notificación
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        ¿Estás seguro de que deseas eliminar esta notificación? Esta acción no se puede
                                        deshacer.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="deleteNotification"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Eliminar
                        </button>
                        <button type="button" @click="deleteNotificationModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Notification Modal -->
        <div v-if="previewNotificationModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity"
                    aria-hidden="true"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full"
                    ref="previewModalRef">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 sm:mx-0 sm:h-10 sm:w-10">
                                <i class='bx bx-bell text-blue-600 dark:text-blue-400 text-xl'></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                    id="modal-title">
                                    Detalles de la Notificación
                                </h3>

                                <div v-if="selectedNotification.id" class="mt-6 space-y-6">
                                    <div
                                        class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-between mb-4">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                                :class="notificationTypes.find(t => t.value === selectedNotification.type)?.color">
                                                <i
                                                    :class="`bx ${notificationTypes.find(t => t.value === selectedNotification.type)?.icon} mr-1`"></i>
                                                {{notificationTypes.find(t => t.value ===
                                                selectedNotification.type)?.label }}
                                            </span>
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                                :class="statusOptions.find(s => s.value === selectedNotification.status)?.color">
                                                <i class="bx bx-radio-circle-marked mr-1"></i>
                                                {{statusOptions.find(s => s.value ===
                                                selectedNotification.status)?.label }}
                                            </span>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">{{
                                            selectedNotification.title }}</h3>
                                        <p class="text-gray-700 dark:text-gray-300 mb-4">{{ selectedNotification.content
                                            }}</p>
                                        <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                                            <div class="flex items-center">
                                                <i class='bx bx-calendar mr-1'></i>
                                                <span>Creada: {{ formatDate(selectedNotification.created_at) }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class='bx bx-time mr-1'></i>
                                                <span>Expira: {{ formatDate(selectedNotification.expires_at) }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i
                                                    :class="`bx ${targetOptions.find(t => t.value === selectedNotification.target)?.icon} mr-1`"></i>
                                                <span>{{targetOptions.find(t => t.value ===
                                                    selectedNotification.target)?.label }}</span>
                                                <span v-if="selectedNotification.target_details" class="ml-1">
                                                    ({{ selectedNotification.target_details }})
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div
                                            class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                                            <h4
                                                class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                                                <i
                                                    class='bx bx-bar-chart-alt-2 mr-2 text-blue-600 dark:text-blue-400'></i>
                                                Estadísticas de lectura
                                            </h4>
                                            <div class="space-y-4">
                                                <div>
                                                    <div class="flex justify-between mb-1">
                                                        <span
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-300">Tasa
                                                            de lectura</span>
                                                        <span
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            {{ Math.round((selectedNotification.read_count /
                                                            selectedNotification.total_recipients) * 100) }}%
                                                        </span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                                        <div class="bg-blue-600 h-2.5 rounded-full"
                                                            :style="`width: ${Math.round((selectedNotification.read_count / selectedNotification.total_recipients) * 100)}%`">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4">
                                                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">Leídas
                                                        </div>
                                                        <div
                                                            class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                                            {{ selectedNotification.read_count }}</div>
                                                    </div>
                                                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">Total
                                                            destinatarios</div>
                                                        <div
                                                            class="text-2xl font-bold text-gray-700 dark:text-gray-300">
                                                            {{ selectedNotification.total_recipients }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                                            <h4
                                                class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                                                <i class='bx bx-user-check mr-2 text-blue-600 dark:text-blue-400'></i>
                                                Destinatarios
                                            </h4>
                                            <div class="space-y-4">
                                                <div
                                                    class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                                    <div
                                                        class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 p-2 rounded-full">
                                                        <i
                                                            :class="`bx ${targetOptions.find(t => t.value === selectedNotification.target)?.icon}`"></i>
                                                    </div>
                                                    <div>
                                                        <div
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            {{targetOptions.find(t => t.value ===
                                                            selectedNotification.target)?.label }}
                                                        </div>
                                                        <div v-if="selectedNotification.target_details"
                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ selectedNotification.target_details }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    En una implementación real, aquí se mostraría una lista de los
                                                    destinatarios o un resumen de los mismos.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="previewNotificationModal = false"
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.translate-y-0 {
    transform: translateY(0);
}

.translate-y-4 {
    transform: translateY(1rem);
}

.opacity-0 {
    opacity: 0;
}

.opacity-100 {
    opacity: 1;
}

/* Modal animation */
@keyframes modal-fade-in {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

@keyframes modal-slide-in {
    from {
        transform: translateY(10px);
    }

    to {
        transform: translateY(0);
    }
}

.fixed.inset-0 {
    animation: modal-fade-in 0.3s ease-out forwards;
}

.inline-block.align-bottom {
    animation: modal-slide-in 0.3s ease-out forwards;
}
</style>