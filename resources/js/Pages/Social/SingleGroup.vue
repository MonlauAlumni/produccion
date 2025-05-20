<script setup>
import { ref, computed, onMounted, nextTick, onUnmounted, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Layout from '@/Components/Layout.vue';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { QuillEditor } from '@vueup/vue-quill';
import DOMPurify from 'dompurify';
import PostCard from '@/Components/Social/PostCard.vue';
import GroupPostTab from '@/Components/Social/GroupPostTab.vue';
import GroupHeader from '@/Components/Social/GroupHeader.vue';
import GroupMembersTab from '@/Components/Social/GroupMembersTab.vue';

const props = defineProps({
    group: Object,
    isAdmin: Boolean,
    isMember: Boolean,
    currentPage: {
        type: Number,
        default: 1
    },
});

const hasMorePosts = ref(true);

const page = usePage();
const auth = computed(() => page.props.auth);

const activeTab = ref('publicaciones');
const showJoinModal = ref(false);
const showInviteModal = ref(false);
const showEditModal = ref(false);
const inviteEmail = ref('');
const isUploading = ref(false);

const joinGroup = () => {
    router.post(`/grupos/${props.group.slug}/join`, {}, {
        onSuccess: () => {
            showJoinModal.value = false;
        }
    });
};

const inviteMember = () => {
    if (!inviteEmail.value) return;

    router.post(`/grupos/${props.group.slug}/invite`, {
        email: inviteEmail.value
    }, {
        onSuccess: () => {
            inviteEmail.value = '';
            showInviteModal.value = false;
        }
    });
};

const uploadBanner = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('banner', file);

    isUploading.value = true;

    router.post(`/grupos/${props.group.slug}/update-banner`, formData, {
        onSuccess: () => {
            isUploading.value = false;
        },
        onError: () => {
            isUploading.value = false;
        }
    });
};

const adminMembers = computed(() => {
    if (!props.group.members) return [];
    return props.group.members.filter(member => member.role === 'admin');
});

const regularMembers = computed(() => {
    if (!props.group.members) return [];
    return props.group.members.filter(member => member.role === 'member');
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};



watch(() => props.group, (newGroup) => {
    if (newGroup) {
        hasMorePosts.value = newGroup.has_more_posts === true;
    }
}, { immediate: true });

const groupSlug = ref('');

watch(() => props.group, (newGroup) => {
    if (newGroup && newGroup.slug) {
        groupSlug.value = newGroup.slug;
    }
}, { immediate: true });

</script>
<template>
    <Layout :auth="auth">
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col relative">
            <div class="relative w-full h-64 md:h-80 bg-gradient-to-r from-[#193CB8] to-[#2748c6] overflow-hidden">
                <div v-if="isAdmin" class="absolute bottom-4 right-4 z-11">
                    <label for="bannerUpload"
                        class="bg-white/90 cursor-pointer hover:bg-gray-100 text-[#193CB8] px-3 py-2 rounded-md shadow-md flex items-center gap-2 transition-all">
                        <i class='bx bx-image-add'></i>
                        <span class="text-sm font-medium">Cambiar Banner</span>
                    </label>
                    <input id="bannerUpload" type="file" @change="uploadBanner" class="hidden" accept="image/*">
                </div>
                <img v-if="group.group_banner" :src="group.group_banner || '/storage/images/default-banner.jpg'"
                    :alt="group.name + ' banner'" class="w-full h-full object-cover opacity-80" />
                <img v-else src="/public/images/default-group-banner.jpg"
                    class="w-full h-full object-cover opacity-80" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>

            </div>

            <main class="flex flex-col items-center justify-center -mt-16 z-10 px-4">
                <div class="flex flex-col space-y-6 w-full max-w-5xl">
                    <GroupHeader :group="group" :isAdmin="isAdmin" :isMember="isMember" :activeTab="activeTab"
                        @update:activeTab="activeTab = $event" @request-join="showJoinModal = true"
                        @invite-members="showInviteModal = true" @edit-group="showEditModal = true" />

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
                        <div class="md:col-span-2 space-y-6">
                            <GroupPostTab
                                v-if="activeTab === 'publicaciones'"
                                :auth="auth"
                                :isMember="isMember"
                                :group="group"
                            />

                            <GroupMembersTab
                                v-if="activeTab === 'miembros'"
                                :activeTab="activeTab"
                                :adminMembers="adminMembers"
                                :regularMembers="regularMembers"
                                :isAdmin="isAdmin"
                            />

                            <div
                                v-if="activeTab === 'eventos'"
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
                            >
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Eventos del grupo</h2>
                                    <button
                                        v-if="isAdmin || isMember"
                                        @click="router.get('/eventos/nuevo')"
                                        class="px-3 py-1.5 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors text-sm flex items-center"
                                    >
                                        <i class='bx bx-plus mr-1'></i> Crear evento
                                    </button>
                                </div>

                                <div v-if="group.events && group.events.length > 0" class="space-y-4">
                                    <div
                                        v-for="event in group.events.filter(e => new Date(e.event_date) >= new Date()).slice(0, 3)"
                                        :key="event.id"
                                        @click="router.visit(`/eventos/${event.slug}`)"
                                        class="flex gap-3 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg cursor-pointer transition-colors"
                                    >
                                        <div
                                            class="w-12 h-12 bg-blue-50 dark:bg-blue-900 rounded-lg flex items-center justify-center text-[#193CB8] dark:text-blue-200 flex-shrink-0"
                                        >
                                            <i class='bx bx-calendar-event text-xl'></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800 dark:text-gray-100 line-clamp-1">{{ event.title }}</h4>
                                            <div class="flex items-center text-gray-500 dark:text-gray-400 text-xs">
                                                <i class='bx bx-calendar mr-1'></i>
                                                {{ new Date(event.event_date).toLocaleDateString('es-ES', { day: 'numeric', month: 'short' }) }}
                                                <span class="mx-1">·</span>
                                                <i class='bx bx-time mr-1'></i>
                                                {{ new Date(event.event_date).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) }}
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        v-if="group.events.filter(e => new Date(e.event_date) >= new Date()).length > 3"
                                        class="text-center mt-2"
                                    >
                                        <button
                                            @click="activeTab = 'eventos'"
                                            class="text-sm text-[#193CB8] dark:text-blue-200 hover:underline"
                                        >
                                            Ver todos los eventos
                                        </button>
                                    </div>
                                </div>

                                <div v-else class="text-center py-8">
                                    <i class='bx bx-calendar-event text-5xl text-gray-300 dark:text-gray-600 mb-2'></i>
                                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-1">No hay eventos programados</h3>
                                    <p class="text-gray-500 dark:text-gray-400">Los eventos del grupo aparecerán aquí</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Información</h2>
                                <hr class="border-t border-blue-200 mb-4" />

                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-50 dark:bg-blue-200 p-2 rounded-full">
                                            <i class='bx bx-category-alt text-[#193CB8] text-xl'></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Categoría</p>
                                            <p class="text-gray-700 dark:text-gray-300">{{ group.category }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-50 dark:bg-blue-200 p-2 rounded-full">
                                            <i class='bx bx-calendar text-[#193CB8] text-xl'></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Creado</p>
                                            <p class="text-gray-700 dark:text-gray-300">{{ formatDate(group.created_at) }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-50 dark:bg-blue-200 p-2 rounded-full">
                                            <i class='bx bx-user-check text-[#193CB8] text-xl'></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Miembros</p>
                                            <p class="text-gray-700 dark:text-gray-300">{{ group.members ? group.members.length : 0 }}
                                                {{ group.members && group.members.length === 1 ? 'miembro' : 'miembros'
                                                }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Miembros destacados</h2>
                                <hr class="border-t border-blue-200 mb-4" />

                                <div class="space-y-3">
                                    <div v-for="member in adminMembers.slice(0, 3)" :key="member.id"
                                        class="flex items-center gap-3 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer"
                                        @click="router.get('/perfil/' + member.user.profile.slang)">
                                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                                            <img v-if="member.user.profile.profile_picture"
                                                :src="member.user.profile.profile_picture || '/images/default-avatar.jpg'"
                                                :alt="member.user.name" class="w-full h-full object-cover" />
                                            <div v-else
                                                class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-[#193CB8] shadow-sm border-2  border-white dark:text-blue-400 dark:border-gray-800">
                                                <i class='bx bxs-user text-xl'></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800 dark:text-gray-200">{{ member.user.name }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Administrador</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-center">
                                    <button @click="activeTab = 'miembros'"
                                        class="text-sm text-blue-200 hover:underline cursor-pointer">
                                        Ver todos los miembros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <div v-if="showJoinModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Solicitar unirse al grupo</h3>
                    <button @click="showJoinModal = false" class="text-gray-500 hover:text-gray-700">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                <div class="p-6">
                    <p class="text-gray-600 mb-4">
                        Este es un grupo privado. Tu solicitud será revisada por los administradores del grupo.
                    </p>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Mensaje (opcional)
                        </label>
                        <textarea rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors resize-none"
                            placeholder="Explica por qué quieres unirte a este grupo..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button @click="showJoinModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button @click="joinGroup"
                            class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors">
                            Enviar solicitud
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-show="showInviteModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 dark:text-gray-100">Invitar a nuevos miembros</h3>
                <button @click="showInviteModal = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <i class='bx bx-x text-2xl'></i>
                </button>
            </div>

            <div class="p-6">
                <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Email del invitado
                </label>
                <input v-model="inviteEmail" type="email"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                    placeholder="ejemplo@email.com" />
                </div>

                <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Mensaje (opcional)
                </label>
                <textarea rows="3"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors resize-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                    placeholder="Añade un mensaje personal a la invitación..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                <button @click="showInviteModal = false"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Cancelar
                </button>
                <button @click="inviteMember"
                    class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors">
                    Enviar invitación
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

:deep(.ql-editor) {
    min-height: 100px;
}

:deep(.ql-toolbar) {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
    border-color: #d1d5db;
}

:deep(.ql-container) {
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
    border-color: #d1d5db;
}

:deep(.ql-toolbar .ql-stroke) {
    stroke: #6b7280;
}

:deep(.ql-toolbar .ql-fill) {
    fill: #6b7280;
}

:deep(.ql-toolbar .ql-picker) {
    color: #6b7280;
}

:deep(.ql-toolbar button:hover .ql-stroke) {
    stroke: #193CB8;
}

:deep(.ql-toolbar button.ql-active .ql-stroke) {
    stroke: #193CB8;
}

:deep(.ql-toolbar button:hover .ql-fill) {
    fill: #193CB8;
}

:deep(.ql-toolbar button.ql-active .ql-fill) {
    fill: #193CB8;
}

:deep(.ql-toolbar .ql-picker-label:hover) {
    color: #193CB8;
}

:deep(.ql-toolbar .ql-picker-label.ql-active) {
    color: #193CB8;
}

:deep(.ql-toolbar .ql-picker-item:hover) {
    color: #193CB8;
}

:deep(.ql-toolbar .ql-picker-item.ql-selected) {
    color: #193CB8;
}
</style>