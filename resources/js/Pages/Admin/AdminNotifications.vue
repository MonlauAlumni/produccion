<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AdminLayout from './Layouts/AdminLayout.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';

const page = usePage();

const props = defineProps({
    users: Array,
});

const newNotification = ref({
    title: '',
    content: '',
    type: 'info',
    target: 'all',
    target_details: '',
    expires_at: '',
    schedule_for: null,
});

const selectedUsers = ref([]);
const selectAllUsers = ref(false);

const modalFilters = ref({
    role: '',
    area: '',
    name: ''
});

const isLoaded = ref(false);

const notificationTypes = [
    { value: 'info', label: 'Información', icon: 'bx-info-circle', color: 'text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-300' },
    { value: 'success', label: 'Éxito', icon: 'bx-check-circle', color: 'text-green-600 bg-green-100 dark:bg-green-900 dark:text-green-300' },
    { value: 'warning', label: 'Advertencia', icon: 'bx-error', color: 'text-amber-600 bg-amber-100 dark:bg-amber-900 dark:text-amber-300' },
    { value: 'error', label: 'Error', icon: 'bx-x-circle', color: 'text-red-600 bg-red-100 dark:bg-red-900 dark:text-red-300' }
];

const targetOptions = [
    { value: 'all', label: 'Todos los usuarios', icon: 'bx-globe' },
    { value: 'custom', label: 'Selección manual', icon: 'bx-user-check' }
];

const roleOptions = [
    { value: 'alumne', label: 'Alumno' },
    { value: 'exalumne', label: 'Exalumno' },
    { value: 'empresa', label: 'Empresa' },
    { value: 'admin', label: 'Administrador' }
];

const areaOptions = [
    { value: 'Informatica', label: 'Informática' },
    { value: 'Marketing', label: 'Marketing' },
    { value: 'Automocion', label: 'Automoción' }
];

const modalFilteredUsers = computed(() => {
    if (!props.users) return [];

    let filtered = [...props.users];

    if (modalFilters.value.name) {
        const searchTerm = modalFilters.value.name.toLowerCase();
        filtered = filtered.filter(user => {
            const fullName = `${user.name || ''} ${user.last_name_1 || ''} ${user.last_name_2 || ''}`.toLowerCase();
            const email = (user.email || '').toLowerCase();
            return fullName.includes(searchTerm) || email.includes(searchTerm);
        });
    }

    if (modalFilters.value.role) {
        filtered = filtered.filter(user => {
            return user.roles && user.roles.some(role => role.name === modalFilters.value.role);
        });
    }

    if (modalFilters.value.area) {
        filtered = filtered.filter(user => {
            return user.training_area === modalFilters.value.area;
        });
    }

    return filtered;
});

const activeModalFilters = computed(() => {
    const active = [];

    if (modalFilters.value.name) {
        active.push({
            type: 'name',
            label: `Nombre: ${modalFilters.value.name}`,
            value: modalFilters.value.name
        });
    }

    if (modalFilters.value.role) {
        const roleOption = roleOptions.find(r => r.value === modalFilters.value.role);
        active.push({
            type: 'role',
            label: `Rol: ${roleOption ? roleOption.label : modalFilters.value.role}`,
            value: modalFilters.value.role
        });
    }

    if (modalFilters.value.area) {
        const areaOption = areaOptions.find(a => a.value === modalFilters.value.area);
        active.push({
            type: 'area',
            label: `Área: ${areaOption ? areaOption.label : modalFilters.value.area}`,
            value: modalFilters.value.area
        });
    }

    return active;
});

const modalFilteredUserIds = computed(() => {
    return modalFilteredUsers.value.map(user => user.id);
});

const clearModalFilters = () => {
    modalFilters.value = {
        role: '',
        area: '',
        name: ''
    };
};

const removeModalFilter = (filterType) => {
    modalFilters.value[filterType] = '';
};

const createNotification = () => {
    if (newNotification.value.target === 'all') {
        selectedUsers.value = props.users ? props.users.map(user => user.id) : [];
    }
    router.post('/admin/notifications', {
        content: newNotification.value.content,
        type: newNotification.value.type,
        user_ids: selectedUsers.value
    });
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

const handleSelectAllUsers = () => {
    if (selectAllUsers.value) {
        selectedUsers.value = [...modalFilteredUserIds.value];
    } else {
        selectedUsers.value = [];
    }
};

watch(modalFilters, () => {
    if (modalFilteredUsers.value.length > 0) {
        const allSelected = modalFilteredUserIds.value.every(id => selectedUsers.value.includes(id));
        selectAllUsers.value = allSelected;
    } else {
        selectAllUsers.value = false;
    }
}, { deep: true });

watch(selectedUsers, () => {
    if (modalFilteredUsers.value.length > 0) {
        const allSelected = modalFilteredUserIds.value.every(id => selectedUsers.value.includes(id));
        selectAllUsers.value = allSelected;
    } else {
        selectAllUsers.value = false;
    }
});

onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);
});
</script>

<template>
    <AdminLayout>
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 rounded-xl shadow-sm p-8 mb-6 border border-gray-200 dark:border-gray-700"
            :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-4 opacity-0': !isLoaded }"
            style="transition: all 0.5s ease; transition-delay: 100ms;">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <i class='bx bx-bell text-gray-700 dark:text-gray-300 text-xl'></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Enviar Notificación</h1>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        Envía notificaciones a los usuarios de Alumni.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6 border border-gray-200 dark:border-gray-700"
            :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-4 opacity-0': !isLoaded }"
            style="transition: all 0.5s ease; transition-delay: 200ms;">
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Contenido de la notificación
                    </label>
                    <textarea v-model="newNotification.content" rows="4"
                        class="w-full p-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition duration-200"
                        placeholder="Escriba el mensaje de la notificación..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Tipo de notificación
                        </label>
                        <div class="flex flex-wrap gap-3">
                            <label v-for="type in notificationTypes" :key="type.value"
                                class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors"
                                :class="newNotification.type === type.value ?
                                    'border-blue-500 bg-blue-50 dark:bg-blue-900/30 dark:border-blue-600' :
                                    'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800'">
                                <input type="radio" :value="type.value" v-model="newNotification.type" class="hidden" />
                                <i :class="`bx ${type.icon} mr-2 ${type.color.split(' ')[0]}`"></i>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ type.label }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Destinatarios
                        </label>
                        <div class="space-y-2">
                            <label v-for="target in targetOptions" :key="target.value"
                                class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors"
                                :class="newNotification.target === target.value ?
                                    'border-blue-500 bg-blue-50 dark:bg-blue-900/30 dark:border-blue-600' :
                                    'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800'">
                                <input type="radio" :value="target.value" v-model="newNotification.target" class="hidden" />
                                <i :class="`bx ${target.icon} mr-2 text-blue-600 dark:text-blue-400`"></i>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ target.label }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div v-if="newNotification.target === 'custom'">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h4 class="text-base font-medium text-gray-700 dark:text-gray-300 mb-4 flex items-center">
                            <i class='bx bx-filter-alt mr-2 text-blue-600 dark:text-blue-400'></i>
                            Filtros de destinatarios
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">
                                    Nombre o email
                                </label>
                                <InputWithIcon 
                                    type="text" 
                                    icon="search" 
                                    placeholder="Buscar usuario..." 
                                    class="w-full"
                                    v-model="modalFilters.name" 
                                />
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">
                                    Rol
                                </label>
                                <div class="w-full relative">
                                    <i class='bx bx-shield-quarter mr-2 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl'></i>
                                    <select 
                                        v-model="modalFilters.role"
                                        class="w-full h-10 pl-10 p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition duration-200 bg-white dark:bg-gray-800 appearance-none"
                                    >
                                        <option value="">Todos los roles</option>
                                        <option v-for="role in roleOptions" :key="role.value" :value="role.value">
                                            {{ role.label }}
                                        </option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">
                                    Área formativa
                                </label>
                                <div class="w-full relative">
                                    <i class='bx bx-book mr-2 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl'></i>
                                    <select 
                                        v-model="modalFilters.area"
                                        class="w-full h-10 pl-10 p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition duration-200 bg-white dark:bg-gray-800 appearance-none"
                                    >
                                        <option value="">Todas las áreas</option>
                                        <option v-for="area in areaOptions" :key="area.value" :value="area.value">
                                            {{ area.label }}
                                        </option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="activeModalFilters.length > 0" class="flex flex-wrap gap-2 mb-4">
                            <div v-for="(filter, index) in activeModalFilters" :key="index" 
                                class="flex items-center bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1.5 rounded-full text-xs">
                                <span>{{ filter.label }}</span>
                                <button @click="removeModalFilter(filter.type)" class="ml-2 text-blue-500 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-200">
                                    <i class='bx bx-x'></i>
                                </button>
                            </div>
                            <button @click="clearModalFilters" class="flex items-center bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-3 py-1.5 rounded-full text-xs hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <i class='bx bx-reset mr-1'></i>
                                Limpiar filtros
                            </button>
                        </div>

                        <div class="flex items-center justify-between mb-3 border-b border-gray-200 dark:border-gray-700 pb-3">
                            <div class="flex items-center">
                                <label class="flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        v-model="selectAllUsers" 
                                        @change="handleSelectAllUsers"
                                        class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 rounded" 
                                    />
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Seleccionar todos los usuarios visibles ({{ modalFilteredUsers.length }})
                                    </span>
                                </label>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ selectedUsers.length }} usuario(s) seleccionado(s)
                            </div>
                        </div>

                        <div class="max-h-80 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                            <div v-if="modalFilteredUsers.length === 0" class="p-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                                No se encontraron usuarios con los filtros aplicados
                            </div>
                            <div v-else class="divide-y divide-gray-100 dark:divide-gray-700">
                                <label v-for="user in modalFilteredUsers" :key="user.id"
                                    class="flex items-center gap-2 p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <input 
                                        type="checkbox" 
                                        :value="user.id" 
                                        v-model="selectedUsers"
                                        class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 rounded" 
                                    />
                                    <div>
                                        <div class="text-sm text-gray-700 dark:text-gray-200">
                                            {{ user.name + ' ' + user.last_name_1 + ' ' + user.last_name_2 }}
                                        </div>
                                        <div v-if="user.email" class="text-xs text-gray-400">
                                            {{ user.email }}
                                        </div>
                                    </div>
                                    <div v-if="user.roles && user.roles.length" class="ml-auto">
                                        <span v-for="role in user.roles" :key="role.name" 
                                            class="inline-block px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded-full mr-1">
                                            {{ roleOptions.find(r => r.value === role.name)?.label || role.name }}
                                        </span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button 
                        @click="createNotification"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200 flex items-center shadow-sm"
                    >
                        <i class='bx bx-send mr-2'></i>
                        Enviar Notificación
                    </button>
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
</style>