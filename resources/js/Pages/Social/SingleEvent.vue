<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Layout from '@/Components/Layout.vue';

const props = defineProps({
    auth: {
        type: Object,
        default: () => ({})
    },
    event: {
        type: Object,
        required: true
    },
    isOrganizer: {
        type: Boolean,
        default: false
    },
    isAttending: {
        type: Boolean,
        default: false
    }
});

const page = usePage();
const auth = computed(() => page.props.auth);

const showShareModal = ref(false);
const showAttendeesList = ref(false);
const showConfirmCancelModal = ref(false);

const eventUrl = `${window.location.origin}/eventos/${props.event.slug}`;


const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const attendEvent = () => {
    router.post(`/eventos/${props.event.slug}/attend`, {}, {
        onSuccess: () => {
        }
    });
};

const cancelAttendance = () => {
    showConfirmCancelModal.value = true;
};

const confirmCancelAttendance = () => {
    router.post(`/eventos/${props.event.slug}/cancel-attendance`, {}, {
        onSuccess: () => {
            showConfirmCancelModal.value = false;
        }
    });
};

const shareEvent = () => {
    showShareModal.value = true;
};

const copyEventLink = () => {
    navigator.clipboard.writeText(eventUrl);
    const toast = document.createElement('div');
    toast.textContent = 'Enlace copiado';
    Object.assign(toast.style, {
        position: 'fixed',
        bottom: '40%',
        left: '50%',
        transform: 'translate(-50%, 50%)',
        backgroundColor: '#4ade80',
        border: '1px solid #16a34a',
        color: '#fff',
        padding: '0.5rem 1rem',
        borderRadius: '0.25rem',
        fontWeight: 'bold',
        transition: 'opacity 0.4s, transform 0.4s',
        opacity: '1',
        zIndex: '9999'
    });
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transform = 'translate(-50%, 30%)';
        toast.style.opacity = '0';
        setTimeout(() => document.body.removeChild(toast), 800);
    }, 500);
};

const editEvent = () => {
    router.visit(`/eventos/${props.event.slug}/editar`);
};

const deleteEvent = () => {
    if (confirm('¿Estás seguro de que quieres eliminar este evento? Esta acción no se puede deshacer.')) {
        router.delete(`/eventos/${props.event.slug}`, {
            onSuccess: () => {
                router.visit('/connect', {
                    replace: true
                });
            }
        });
    }
};

const remainingDays = computed(() => {
    const eventDate = new Date(props.event.event_date);
    const today = new Date();
    const diffTime = eventDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
});

const eventStatus = computed(() => {
    if (remainingDays.value < 0) {
        return 'finalizado';
    } else if (remainingDays.value === 0) {
        return 'hoy';
    } else {
        return 'próximo';
    }
});

const statusColor = computed(() => {
    if (eventStatus.value === 'finalizado') {
        return 'bg-gray-100 text-gray-700';
    } else if (eventStatus.value === 'hoy') {
        return 'bg-green-100 text-green-700';
    } else {
        return 'bg-blue-100 text-blue-700';
    }
});

const attendeesPreview = computed(() => {
    return props.event.attendees.slice(0, 5);
});

const additionalAttendees = computed(() => {
    return Math.max(0, props.event.attendees.length - 5);
});
</script>

<template>
    <Layout :auth="auth">
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col">
            <div class="relative w-full h-64 md:h-80 bg-gradient-to-r from-[#193CB8] to-[#2748c6] overflow-hidden">
                <img v-if="event.photos && event.photos.length > 0" :src="event.photos[0].photo_path" :alt="event.title"
                    class="w-full h-full object-cover opacity-80" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>

                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <div class="max-w-5xl mx-auto">
                        <div class="flex items-center mb-2">
                            <span :class="['px-3 py-1 rounded-full text-sm font-medium', statusColor]">
                                {{ eventStatus === 'finalizado' ? 'Finalizado' :
                                    eventStatus === 'hoy' ? 'Hoy' :
                                        `Faltan ${remainingDays} días` }}
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ event.title }}</h1>
                        <div class="flex flex-wrap items-center text-sm md:text-base gap-4">
                            <span class="flex items-center">
                                <i class='bx bx-calendar mr-1'></i> {{ formatDate(event.event_date) }}
                            </span>
                            <span class="flex items-center">
                                <i class='bx bx-time mr-1'></i> {{ formatTime(event.event_date) }}
                            </span>
                            <span class="flex items-center">
                                <i class='bx bx-map mr-1'></i> {{ event.location }}
                            </span>
                            <span class="flex items-center">
                                <i class='bx bx-user mr-1'></i>
                                {{ event.attendees_count === 1 ? '1 asistente' : `${event.attendees_count} asistentes`
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <main class="flex-1 py-8">
                <div class="max-w-5xl mx-auto px-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 space-y-6">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 flex flex-wrap gap-3">
                                <button v-if="!isAttending && eventStatus !== 'finalizado'" @click="attendEvent"
                                    class="flex-1 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors flex items-center justify-center">
                                    <i class='bx bx-calendar-check mr-2'></i> Asistir al evento
                                </button>
                                <button v-if="isAttending && eventStatus !== 'finalizado'" @click="cancelAttendance"
                                    class="flex-1 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center justify-center">
                                    <i class='bx bx-calendar-x mr-2'></i> Cancelar asistencia
                                </button>
                                <button @click="shareEvent"
                                    class="flex-1 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center justify-center">
                                    <i class='bx bx-share-alt mr-2'></i> Compartir
                                </button>
                                <button v-if="isOrganizer" @click="editEvent"
                                    class="flex-1 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center justify-center">
                                    <i class='bx bx-edit mr-2'></i> Editar
                                </button>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                                <div class="p-6">
                                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">Acerca de este
                                        evento</h2>

                                    <p class="max-w-none text-gray-600 dark:text-gray-300 dark:prose-invert">{{
                                        event.description }}</p>

                                </div>
                            </div>

                            <div v-if="event.photos && event.photos.length > 0"
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                                <div class="p-6">
                                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">Galería de
                                        imágenes</h2>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        <div v-for="photo in event.photos" :key="photo.id" class="relative">
                                            <img :src="photo.photo_path" :alt="event.title"
                                                class="w-full h-40 object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity" />
                                            <button v-if="isOrganizer" @click="removePhoto(photo.id)"
                                                class="absolute top-2 right-2 bg-white/80 dark:bg-black/50 text-gray-700 dark:text-gray-300 rounded-full p-1 hover:bg-white dark:hover:bg-black transition-colors">
                                                <i class='bx bx-x text-xl'></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                                <div class="p-6">
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Organizador</h3>
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0">
                                            <img v-if="event.organizer.profile && event.organizer.profile.profile_picture"
                                                :src="event.organizer.profile.profile_picture"
                                                :alt="event.organizer.name" class="w-full h-full object-cover" />
                                            <div v-else
                                                class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-[#193CB8] dark:text-blue-200">
                                                <i class='bx bxs-user text-xl'></i>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="font-medium text-gray-800 dark:text-gray-200">{{
                                                event.organizer.name }} {{
                                                    event.organizer.last_name_1 }} {{ event.organizer.last_name_2 }}</h4>
                                            <p v-if="event.organizer.profile && event.organizer.profile.job_title"
                                                class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ event.organizer.profile.job_title }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                                <div class="p-6">
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Fecha y
                                        ubicación</h3>
                                    <div class="space-y-4">
                                        <div class="flex items-start">
                                            <div class="bg-blue-50 dark:bg-blue-900 p-2 rounded-full mr-3">
                                                <i class='bx bx-calendar text-[#193CB8] dark:text-blue-200 text-xl'></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800 dark:text-gray-200">{{
                                                    formatDate(event.event_date) }}
                                                </p>
                                                <p class="text-gray-500 dark:text-gray-400">{{
                                                    formatTime(event.event_date) }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="bg-blue-50 dark:bg-blue-900 p-2 rounded-full mr-3">
                                                <i class='bx bx-map text-[#193CB8] dark:text-blue-200 text-xl'></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800 dark:text-gray-200">{{
                                                    event.location }}</p>
                                                <a :href="`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(event.location)}`"
                                                    target="_blank"
                                                    class="text-[#193CB8] dark:text-blue-200 text-sm hover:underline">
                                                    Ver en mapa
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                                <div class="p-6">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Asistentes</h3>
                                        <button @click="showAttendeesList = true"
                                            class="text-[#193CB8] dark:text-blue-200 text-sm hover:underline">
                                            Ver todos
                                        </button>
                                    </div>

                                    <div class="space-y-3">
                                        <div v-for="attendee in attendeesPreview" :key="attendee.id"
                                            class="flex items-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg cursor-pointer">
                                            <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                                                <img v-if="attendee.user.profile && attendee.user.profile.profile_picture"
                                                    :src="attendee.user.profile.profile_picture"
                                                    :alt="attendee.user.name" class="w-full h-full object-cover" />
                                                <div v-else
                                                    class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-[#193CB8] dark:text-blue-200">
                                                    <i class='bx bxs-user text-xl'></i>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <h4 class="font-medium text-gray-800 dark:text-gray-200">{{
                                                    attendee.user.name }} {{
                                                        attendee.user.last_name_1 }}</h4>
                                                <p v-if="attendee.user.profile && attendee.user.profile.job_title"
                                                    class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ attendee.user.profile.job_title }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="additionalAttendees > 0" class="mt-3 text-center">
                                        <button @click="showAttendeesList = true"
                                            class="text-[#193CB8] dark:text-blue-200 text-sm hover:underline">
                                            +{{ additionalAttendees }} asistentes más
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="event.group"
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                                <div class="p-6">
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Grupo
                                        organizador</h3>
                                    <div @click="router.visit('/grupos/' + event.group.slug)"
                                        class="flex items-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 p-2 rounded-lg">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0">
                                            <img v-if="event.group.group_logo" :src="event.group.group_logo"
                                                :alt="event.group.name" class="w-full h-full object-cover" />
                                            <div v-else
                                                class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-300">
                                                <i class='bx bx-group text-xl'></i>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="font-medium text-gray-800 dark:text-gray-200">{{ event.group.name
                                                }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ event.group.members_count }} miembros
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="isOrganizer"
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                                <div class="p-6">
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Opciones de
                                        administrador</h3>
                                    <div class="space-y-3">
                                        <button @click="editEvent"
                                            class="w-full py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center justify-center">
                                            <i class='bx bx-edit mr-2'></i> Editar evento
                                        </button>
                                        <button @click="deleteEvent"
                                            class="w-full py-2 bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-700 transition-colors flex items-center justify-center">
                                            <i class='bx bx-trash mr-2'></i> Eliminar evento
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <div v-if="showShareModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200">Compartir evento</h3>
                    <button @click="showShareModal = false"
                        class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Comparte este evento con tus contactos.
                    </p>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Enlace del evento
                        </label>
                        <div class="flex">
                            <input type="text" readonly :value="eventUrl"
                                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 rounded-l-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors" />
                            <button @click="copyEventLink"
                                class="px-4 py-2 bg-[#193CB8] text-white rounded-r-lg hover:bg-[#142d8c] transition-colors">
                                <i class='bx bx-copy'></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button @click="showShareModal = false"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showAttendeesList" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full max-h-[80vh] flex flex-col">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200">Asistentes ({{ event.attendees.length }})
                    </h3>
                    <button @click="showAttendeesList = false"
                        class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                <div class="p-4 overflow-y-auto flex-1">
                    <div v-for="attendee in event.attendees" :key="attendee.id"
                        class="flex items-center p-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg cursor-pointer mb-2">
                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0"
                            @click="router.get(`/perfil/${attendee.user.profile.slang}`)">
                            <img v-if="attendee.user.profile && attendee.user.profile.profile_picture"
                                :src="attendee.user.profile.profile_picture" :alt="attendee.user.name"
                                class="w-full h-full object-cover" />
                            <div v-else
                                class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-[#193CB8] dark:text-blue-200">
                                <i class='bx bxs-user text-xl'></i>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <h4 class="font-medium text-gray-800 dark:text-gray-200">{{ attendee.user.name }} {{
                                attendee.user.last_name_1
                                }} {{ attendee.user.last_name_2 }}</h4>
                            <p v-if="attendee.user.profile && attendee.user.profile.job_title"
                                class="text-xs text-gray-500 dark:text-gray-400">
                                {{ attendee.user.profile.job_title }}
                            </p>
                        </div>
                        <div v-if="attendee.user.id === event.organizer_id"
                            class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 rounded-full text-xs">
                            Organizador
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <button @click="showAttendeesList = false"
                        class="w-full py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showConfirmCancelModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200">Cancelar asistencia</h3>
                    <button @click="showConfirmCancelModal = false"
                        class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        ¿Estás seguro de que quieres cancelar tu asistencia a este evento?
                    </p>

                    <div class="flex justify-end space-x-3">
                        <button @click="showConfirmCancelModal = false"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            No, mantener asistencia
                        </button>
                        <button @click="confirmCancelAttendance"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                            Sí, cancelar asistencia
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

.prose {
    max-width: 65ch;
    color: inherit;
}

.prose p {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
}

.prose strong {
    font-weight: 600;
    color: inherit;
}

.prose a {
    color: #193CB8;
    text-decoration: underline;
    font-weight: 500;
}

.prose ul {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
    list-style-type: disc;
    padding-left: 1.625em;
}

.prose ol {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
    list-style-type: decimal;
    padding-left: 1.625em;
}

.prose li {
    margin-top: 0.5em;
    margin-bottom: 0.5em;
}
</style>
