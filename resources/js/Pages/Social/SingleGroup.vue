<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Layout from '@/Components/Layout.vue';

const props = defineProps({
    group: Object,
    isAdmin: Boolean,
    isMember: Boolean,
    auth: Object,
});

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

const uploadLogo = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('logo', file);

    isUploading.value = true;

    router.post(`/grupos/${props.group.slug}/update-logo`, formData, {
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
</script>

<template>
    <Layout :auth="auth">
        <div class="min-h-screen bg-gray-50 flex flex-col">
            <div class="relative w-full h-64 md:h-80 bg-gradient-to-r from-[#193CB8] to-[#2748c6] overflow-hidden">
                <img :src="group.group_banner || '/images/default-banner.jpg'" :alt="group.name + ' banner'"
                    class="w-full h-full object-cover opacity-80" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>

                <div v-if="isAdmin" class="absolute bottom-4 right-4">
                    <label
                        class="cursor-pointer bg-white/90 hover:bg-white text-[#193CB8] px-3 py-2 rounded-md shadow-md flex items-center gap-2 transition-all">
                        <i class='bx bx-image-add'></i>
                        <span class="text-sm font-medium">Cambiar Banner</span>
                        <input type="file" @change="uploadBanner" class="hidden" accept="image/*">
                    </label>
                </div>
            </div>

            <main class="flex flex-col items-center justify-center -mt-16 relative z-10 px-4">
                <div class="flex flex-col space-y-6 w-full max-w-5xl">
                    <!-- Cabecera del grupo -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 pt-20 relative">
     
                            <div class="absolute left-6 -top-12">
                                <div class="relative">
                                    <div
                                        class="w-24 h-24 rounded-xl overflow-hidden border-4 border-white bg-white shadow-md">
                                        <img :src="group.group_logo || '/images/default-logo.jpg'"
                                            :alt="group.group_name + ' logo'" class="w-full h-full object-cover" />
                                    </div>

                                    <!-- Logo upload button para administradores -->
                                    <label v-if="isAdmin"
                                        class="absolute -bottom-2 -right-2 cursor-pointer bg-white hover:bg-gray-50 text-[#193CB8] p-1.5 rounded-full shadow-md transition-all">
                                        <i class='bx bx-camera text-lg'></i>
                                        <input type="file" @change="uploadLogo" class="hidden" accept="image/*">
                                    </label>
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-800">{{ group.name }}</h1>

                                    <div class="flex items-center text-gray-500 text-sm mt-2">
                                        <span class="flex items-center mr-4">
                                            <i class='bx bx-user-circle mr-1'></i> {{ group.members ?
                                            group.members.length : 0 }} miembros
                                        </span>
                                        <span class="flex items-center">
                                            <i class='bx bx-lock-open-alt mr-1' v-if="group.privacy === 'public'"></i>
                                            <i class='bx bx-lock-alt mr-1' v-else></i>
                                            {{ group.privacy === 'public' ? 'Grupo público' : 'Grupo privado' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4 md:mt-0 flex items-center gap-3">
                                    <!-- Botón para unirse al grupo (si no es miembro) -->
                                    <button v-if="!isMember && group.privacy === 'public'" @click="joinGroup"
                                        class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors flex items-center">
                                        <i class='bx bx-user-plus mr-1'></i> Unirse al grupo
                                    </button>

                                    <!-- Botón para solicitar unirse (si es privado) -->
                                    <button v-if="!isMember && group.privacy === 'private'"
                                        @click="showJoinModal = true"
                                        class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors flex items-center">
                                        <i class='bx bx-envelope mr-1'></i> Solicitar unirse
                                    </button>

                                    <!-- Botón para invitar (si es miembro) -->
                                    <button v-if="isMember" @click="showInviteModal = true"
                                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                                        <i class='bx bx-user-plus mr-1'></i> Invitar
                                    </button>

                                    <!-- Botón para editar (si es admin) -->
                                    <button v-if="isAdmin" @click="showEditModal = true"
                                        class="p-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                        <i class='bx bx-edit-alt'></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Descripción del grupo -->
                            <p class="text-gray-600 mt-4">{{ group.description }}</p>

                            <!-- Etiquetas -->
                            <div v-if="group.tags && group.tags.length > 0" class="flex flex-wrap gap-2 mt-4">
                                <span v-for="(tag, index) in group.tags" :key="index"
                                    class="px-2 py-1 bg-[#193CB8]/10 text-[#193CB8] rounded-full text-xs">
                                    #{{ tag }}
                                </span>
                            </div>

                            <!-- Tabs de navegación -->
                            <div class="flex border-b border-gray-200 mt-6">
                                <button @click="activeTab = 'publicaciones'"
                                    class="px-4 py-2 font-medium text-sm transition-colors"
                                    :class="activeTab === 'publicaciones' ? 'text-[#193CB8] border-b-2 border-[#193CB8]' : 'text-gray-500 hover:text-gray-700'">
                                    Publicaciones
                                </button>
                                <button @click="activeTab = 'miembros'"
                                    class="px-4 py-2 font-medium text-sm transition-colors"
                                    :class="activeTab === 'miembros' ? 'text-[#193CB8] border-b-2 border-[#193CB8]' : 'text-gray-500 hover:text-gray-700'">
                                    Miembros
                                </button>
                                <button @click="activeTab = 'eventos'"
                                    class="px-4 py-2 font-medium text-sm transition-colors"
                                    :class="activeTab === 'eventos' ? 'text-[#193CB8] border-b-2 border-[#193CB8]' : 'text-gray-500 hover:text-gray-700'">
                                    Eventos
                                </button>
                                <button @click="activeTab = 'archivos'"
                                    class="px-4 py-2 font-medium text-sm transition-colors"
                                    :class="activeTab === 'archivos' ? 'text-[#193CB8] border-b-2 border-[#193CB8]' : 'text-gray-500 hover:text-gray-700'">
                                    Archivos
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido según la tab activa -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Columna principal -->
                        <div class="md:col-span-2 space-y-6">
                            <!-- Tab de Publicaciones -->
                            <div v-if="activeTab === 'publicaciones'">
                                <!-- Formulario para crear publicación (si es miembro) -->
                                <div v-if="isMember" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                                            <img :src="auth.user.profile_photo_url || '/images/default-avatar.jpg'"
                                                alt="Tu avatar" class="w-full h-full object-cover" />
                                        </div>
                                        <div class="flex-1">
                                            <textarea placeholder="Comparte algo con el grupo..."
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors resize-none"
                                                rows="3"></textarea>

                                            <div class="flex justify-between items-center mt-3">
                                                <div class="flex gap-2">
                                                    <button
                                                        class="p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-colors">
                                                        <i class='bx bx-image-alt'></i>
                                                    </button>
                                                    <button
                                                        class="p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-colors">
                                                        <i class='bx bx-link-alt'></i>
                                                    </button>
                                                    <button
                                                        class="p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-colors">
                                                        <i class='bx bx-file'></i>
                                                    </button>
                                                </div>

                                                <button
                                                    class="px-4 py-1.5 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors text-sm">
                                                    Publicar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lista de publicaciones -->
                                <div v-if="group.posts && group.posts.length > 0" class="space-y-4">
                                    <div v-for="post in group.posts" :key="post.id"
                                        class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                                                <img :src="post.user.profile_photo_url || '/images/default-avatar.jpg'"
                                                    :alt="post.user.name" class="w-full h-full object-cover" />
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <h3 class="font-medium text-gray-800">{{ post.user.name }}</h3>
                                                        <p class="text-xs text-gray-500">{{ formatDate(post.created_at)
                                                            }}</p>
                                                    </div>

                                                    <button class="p-1 text-gray-400 hover:text-gray-600">
                                                        <i class='bx bx-dots-horizontal-rounded'></i>
                                                    </button>
                                                </div>

                                                <div class="mt-2">
                                                    <p class="text-gray-700">{{ post.content }}</p>

                                                    <!-- Imagen de la publicación si existe -->
                                                    <img v-if="post.image_url" :src="post.image_url"
                                                        alt="Imagen de la publicación"
                                                        class="mt-3 rounded-lg w-full object-cover max-h-96" />
                                                </div>

                                                <!-- Acciones de la publicación -->
                                                <div class="flex items-center gap-4 mt-4 pt-2 border-t border-gray-100">
                                                    <button
                                                        class="flex items-center gap-1 text-gray-500 hover:text-[#193CB8]">
                                                        <i class='bx bx-like'></i>
                                                        <span class="text-sm">Me gusta</span>
                                                    </button>
                                                    <button
                                                        class="flex items-center gap-1 text-gray-500 hover:text-[#193CB8]">
                                                        <i class='bx bx-comment'></i>
                                                        <span class="text-sm">Comentar</span>
                                                    </button>
                                                    <button
                                                        class="flex items-center gap-1 text-gray-500 hover:text-[#193CB8]">
                                                        <i class='bx bx-share'></i>
                                                        <span class="text-sm">Compartir</span>
                                                    </button>
                                                </div>

                                                <!-- Comentarios -->
                                                <div v-if="post.comments && post.comments.length > 0"
                                                    class="mt-4 pt-2 border-t border-gray-100 space-y-3">
                                                    <div v-for="comment in post.comments" :key="comment.id"
                                                        class="flex items-start gap-2">
                                                        <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0">
                                                            <img :src="comment.user.profile_photo_url || '/images/default-avatar.jpg'"
                                                                :alt="comment.user.name"
                                                                class="w-full h-full object-cover" />
                                                        </div>
                                                        <div class="flex-1 bg-gray-50 p-2 rounded-lg">
                                                            <div class="flex items-center justify-between">
                                                                <h4 class="font-medium text-sm text-gray-800">{{
                                                                    comment.user.name }}</h4>
                                                                <p class="text-xs text-gray-500">{{
                                                                    formatDate(comment.created_at) }}</p>
                                                            </div>
                                                            <p class="text-sm text-gray-700">{{ comment.content }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Formulario para comentar -->
                                                <div v-if="isMember" class="mt-3 flex items-center gap-2">
                                                    <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0">
                                                        <img :src="auth.user.profile_photo_url || '/images/default-avatar.jpg'"
                                                            alt="Tu avatar" class="w-full h-full object-cover" />
                                                    </div>
                                                    <div class="flex-1 relative">
                                                        <input type="text" placeholder="Escribe un comentario..."
                                                            class="w-full px-3 py-1.5 border border-gray-300 rounded-full focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors pr-10" />
                                                        <button
                                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-[#193CB8]">
                                                            <i class='bx bx-send'></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mensaje si no hay publicaciones -->
                                <div v-else
                                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                                    <div class="py-8">
                                        <i class='bx bx-message-square-detail text-5xl text-gray-300 mb-2'></i>
                                        <h3 class="text-lg font-medium text-gray-700 mb-1">No hay publicaciones aún</h3>
                                        <p class="text-gray-500">Sé el primero en compartir algo con el grupo</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab de Miembros -->
                            <div v-if="activeTab === 'miembros'"
                                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <h2 class="text-xl font-semibold mb-4">Miembros del grupo</h2>

                                <!-- Administradores -->
                                <div class="mb-6">
                                    <h3 class="text-sm font-medium text-gray-500 mb-3">Administradores</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div v-for="member in adminMembers" :key="member.id"
                                            class="flex items-center gap-3 p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors">
                                            <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0">
                                                <img :src="member.user.profile_photo_url || '/images/default-avatar.jpg'"
                                                    :alt="member.user.name" class="w-full h-full object-cover" />
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-800">{{ member.user.name }}</h4>
                                                <p class="text-xs text-gray-500">Administrador</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Miembros regulares -->
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-3">Miembros</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div v-for="member in regularMembers" :key="member.id"
                                            class="flex items-center gap-3 p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors">
                                            <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0">
                                                <img :src="member.user.profile_photo_url || '/images/default-avatar.jpg'"
                                                    :alt="member.user.name" class="w-full h-full object-cover" />
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-800">{{ member.user.name }}</h4>
                                                <p class="text-xs text-gray-500">Miembro desde {{
                                                    formatDate(member.created_at) }}</p>
                                            </div>

                                            <!-- Opciones para administradores -->
                                            <div v-if="isAdmin" class="flex-shrink-0">
                                                <button class="p-1 text-gray-400 hover:text-gray-600">
                                                    <i class='bx bx-dots-vertical-rounded'></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab de Eventos -->
                            <div v-if="activeTab === 'eventos'"
                                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-xl font-semibold">Eventos del grupo</h2>

                                    <button v-if="isAdmin || isMember"
                                        class="px-3 py-1.5 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors text-sm flex items-center">
                                        <i class='bx bx-plus mr-1'></i> Crear evento
                                    </button>
                                </div>

                                <!-- Lista de eventos -->
                                <div v-if="group.events && group.events.length > 0" class="space-y-4">
                                    <!-- Aquí irían los eventos -->
                                </div>

                                <!-- Mensaje si no hay eventos -->
                                <div v-else class="text-center py-8">
                                    <i class='bx bx-calendar-event text-5xl text-gray-300 mb-2'></i>
                                    <h3 class="text-lg font-medium text-gray-700 mb-1">No hay eventos programados</h3>
                                    <p class="text-gray-500">Los eventos del grupo aparecerán aquí</p>
                                </div>
                            </div>

                            <!-- Tab de Archivos -->
                            <div v-if="activeTab === 'archivos'"
                                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-xl font-semibold">Archivos compartidos</h2>

                                    <button v-if="isAdmin || isMember"
                                        class="px-3 py-1.5 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors text-sm flex items-center">
                                        <i class='bx bx-upload mr-1'></i> Subir archivo
                                    </button>
                                </div>

                                <!-- Lista de archivos -->
                                <div v-if="group.files && group.files.length > 0" class="space-y-4">
                                    <!-- Aquí irían los archivos -->
                                </div>

                                <!-- Mensaje si no hay archivos -->
                                <div v-else class="text-center py-8">
                                    <i class='bx bx-file text-5xl text-gray-300 mb-2'></i>
                                    <h3 class="text-lg font-medium text-gray-700 mb-1">No hay archivos compartidos</h3>
                                    <p class="text-gray-500">Los archivos compartidos en el grupo aparecerán aquí</p>
                                </div>
                            </div>
                        </div>

                        <!-- Columna lateral -->
                        <div class="space-y-6">
                            <!-- Información del grupo -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                <h2 class="text-xl font-semibold mb-4">Información</h2>
                                <hr class="border-t border-[#193CB8] mb-4" />

                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-50 p-2 rounded-full">
                                            <i class='bx bx-category-alt text-[#193CB8] text-xl'></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Categoría</p>
                                            <p class="text-gray-700">{{ group.category }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-50 p-2 rounded-full">
                                            <i class='bx bx-calendar text-[#193CB8] text-xl'></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Creado</p>
                                            <p class="text-gray-700">{{ formatDate(group.created_at) }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-50 p-2 rounded-full">
                                            <i class='bx bx-user-check text-[#193CB8] text-xl'></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Miembros</p>
                                            <p class="text-gray-700">{{ group.members ? group.members.length : 0 }}
                                                miembros</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Próximos eventos -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                <h2 class="text-xl font-semibold mb-4">Próximos eventos</h2>
                                <hr class="border-t border-[#193CB8] mb-4" />

                                <div v-if="group.events && group.events.length > 0" class="space-y-4">
                                    <!-- Aquí irían los eventos próximos -->
                                </div>

                                <div v-else class="text-center py-4">
                                    <p class="text-gray-500">No hay eventos programados</p>
                                </div>
                            </div>

                            <!-- Miembros destacados -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                <h2 class="text-xl font-semibold mb-4">Miembros destacados</h2>
                                <hr class="border-t border-[#193CB8] mb-4" />

                                <div class="space-y-3">
                                    <div v-for="member in adminMembers.slice(0, 3)" :key="member.id"
                                        class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                                            <img :src="member.user.profile_photo_url || '/images/default-avatar.jpg'"
                                                :alt="member.user.name" class="w-full h-full object-cover" />
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800">{{ member.user.name }}</h4>
                                            <p class="text-xs text-gray-500">Administrador</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-center">
                                    <button @click="activeTab = 'miembros'"
                                        class="text-sm text-[#193CB8] hover:underline">
                                        Ver todos los miembros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Modal para solicitar unirse -->
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

        <!-- Modal para invitar miembros -->
        <div v-if="showInviteModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Invitar a nuevos miembros</h3>
                    <button @click="showInviteModal = false" class="text-gray-500 hover:text-gray-700">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                <div class="p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email del invitado
                        </label>
                        <input v-model="inviteEmail" type="email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors"
                            placeholder="ejemplo@email.com" />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Mensaje (opcional)
                        </label>
                        <textarea rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors resize-none"
                            placeholder="Añade un mensaje personal a la invitación..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button @click="showInviteModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
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
</style>