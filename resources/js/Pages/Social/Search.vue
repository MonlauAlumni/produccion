<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Layout from '@/Components/Layout.vue';
import GroupCard from '@/Components/Social/GroupCard.vue';
import EventCard from '@/Components/Social/EventCard.vue';
import PostCard from '@/Components/Social/PostCard.vue';

const props = defineProps({
    query: {
        type: String,
        default: ''
    },
    results: {
        type: Object,
        default: () => ({
            users: [],
            groups: [],
            events: [],
            posts: []
        })
    }
});

const searchQuery = ref(props.query || '');
const activeTab = ref('all');
const isLoading = ref(false);
const page = usePage();
const auth = computed(() => page.props.auth);

const filteredUsers = computed(() => props.results.users || []);
const filteredGroups = computed(() => props.results.groups || []);
const filteredEvents = computed(() => props.results.events || []);
const filteredPosts = computed(() => props.results.posts || []);

const totalResults = computed(() => {
    return (filteredUsers.value?.length || 0) +
        (filteredGroups.value?.length || 0) +
        (filteredEvents.value?.length || 0) +
        (filteredPosts.value?.length || 0);
});

const handleSearch = () => {
    if (!searchQuery.value.trim()) return;

    clearTimeout(handleSearch.timeout);
    handleSearch.timeout = setTimeout(() => {
        isLoading.value = true;
        router.get(`/connect/search`, { q: searchQuery.value }, {
            preserveState: true,
            onSuccess: () => {
                isLoading.value = false;
            },
            onError: () => {
                isLoading.value = false;
            }
        });
    }, 500);
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

watch(searchQuery, (newValue, oldValue) => {
    if (newValue !== oldValue && !isLoading.value) {
        const timeoutId = setTimeout(() => {
            handleSearch();
        }, 500);

        return () => clearTimeout(timeoutId);
    }
});

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const queryParam = urlParams.get('q');
    if (queryParam) {
        searchQuery.value = queryParam;
    }
});
</script>

<template>
    <Layout :auth="auth">
        <div class="  flex flex-col">
            <div class="bg-[#193CB8] dark:bg-[#142d8c] text-white py-8">
                <div class="max-w-6xl mx-auto px-4">
                    <div class="flex flex-col items-center text-center mb-6">
                        <h1 class="text-3xl font-bold mb-2">Resultados de búsqueda</h1>
                        <p class="text-blue-100 text-lg max-w-2xl mx-auto">
                            Encuentra personas, grupos, eventos y publicaciones en la comunidad Alumni
                        </p>
                    </div>

                    <div class="max-w-xl mx-auto relative">
                        <div class="flex bg-white/20 backdrop-blur-sm rounded-full p-1 border border-white/30">
                            <input v-model="searchQuery" type="text" placeholder="Buscar personas, grupos, eventos..."
                                class="w-full px-5 py-3 bg-transparent text-white placeholder-blue-100 focus:outline-none rounded-full"
                                @keyup.enter="handleSearch" />
                            <button @click="handleSearch"
                                class="bg-white text-[#193CB8] hover:bg-blue-50 px-5 py-3 rounded-full flex items-center justify-center transition-colors">
                                <i class='bx bx-search text-xl'></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-1 py-8">
                <div class="max-w-6xl mx-auto px-4">
                    <div class="mb-6 border-b border-gray-200 dark:border-blue-900/40">
                        <div class="flex overflow-x-auto hide-scrollbar">
                            <button @click="activeTab = 'all'" :class="[
                                'px-4 py-2 font-medium whitespace-nowrap',
                                activeTab === 'all' 
                                    ? 'text-[#193CB8] dark:text-blue-200 border-b-2 border-[#193CB8] dark:border-blue-200' 
                                    : 'text-gray-500 dark:text-blue-300 hover:text-gray-700 dark:hover:text-blue-100 cursor-pointer'
                            ]">
                                Todos ({{ totalResults }})
                            </button>
                            <button @click="activeTab = 'people'" :class="[
                                'px-4 py-2 font-medium whitespace-nowrap',
                                activeTab === 'people' 
                                    ? 'text-[#193CB8] dark:text-blue-200 border-b-2 border-[#193CB8] dark:border-blue-200' 
                                    : 'text-gray-500 dark:text-blue-300 hover:text-gray-700 dark:hover:text-blue-100 cursor-pointer'
                            ]">
                                <i class='bx bx-user mr-1'></i> Personas ({{ filteredUsers.length }})
                            </button>
                            <button @click="activeTab = 'groups'" :class="[
                                'px-4 py-2 font-medium whitespace-nowrap',
                                activeTab === 'groups' 
                                    ? 'text-[#193CB8] dark:text-blue-200 border-b-2 border-[#193CB8] dark:border-blue-200' 
                                    : 'text-gray-500 dark:text-blue-300 hover:text-gray-700 dark:hover:text-blue-100 cursor-pointer'
                            ]">
                                <i class='bx bx-group mr-1'></i> Grupos ({{ filteredGroups.length }})
                            </button>
                            <button @click="activeTab = 'events'" :class="[
                                'px-4 py-2 font-medium whitespace-nowrap',
                                activeTab === 'events' 
                                    ? 'text-[#193CB8] dark:text-blue-200 border-b-2 border-[#193CB8] dark:border-blue-200' 
                                    : 'text-gray-500 dark:text-blue-300 hover:text-gray-700 dark:hover:text-blue-100 cursor-pointer'
                            ]">
                                <i class='bx bx-calendar-event mr-1'></i> Eventos ({{ filteredEvents.length }})
                            </button>
                            <button @click="activeTab = 'posts'" :class="[
                                'px-4 py-2 font-medium whitespace-nowrap',
                                activeTab === 'posts' 
                                    ? 'text-[#193CB8] dark:text-blue-200 border-b-2 border-[#193CB8] dark:border-blue-200' 
                                    : 'text-gray-500 dark:text-blue-300 hover:text-gray-700 dark:hover:text-blue-100 cursor-pointer'
                            ]">
                                <i class='bx bx-news mr-1'></i> Publicaciones ({{ filteredPosts.length }})
                            </button>
                        </div>
                    </div>

                    <div v-if="isLoading" class="flex justify-center items-center py-12">
                        <div
                            class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#193CB8] dark:border-blue-200 border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]">
                        </div>
                        <span class="ml-3 text-gray-600 dark:text-blue-100">Buscando...</span>
                    </div>

                    <div v-else-if="totalResults === 0" class="text-center py-12 bg-white dark:bg-[#1a2332] rounded-xl shadow-sm">
                        <div
                            class="w-20 h-20 bg-[#193CB8]/10 dark:bg-[#193CB8]/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class='bx bx-search-alt text-4xl text-[#193CB8] dark:text-blue-200'></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-blue-100 mb-2">No se encontraron resultados</h2>
                        <p class="text-gray-600 dark:text-blue-200 max-w-md mx-auto">
                            No hemos encontrado resultados para "{{ searchQuery }}". Intenta con otros términos o revisa
                            la ortografía.
                        </p>
                    </div>

                    <div v-else>
                        <div v-if="activeTab === 'all' || activeTab === 'people'" class="mb-8">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-blue-100 mb-4"
                                v-if="activeTab === 'all' && filteredUsers.length > 0">
                                Personas ({{ filteredUsers.length }})
                            </h2>

                            <div v-if="filteredUsers.length > 0"
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div v-for="user in filteredUsers" :key="user.id"
                                    class="bg-white dark:bg-[#1a2332] rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                                    <div class="p-4 flex items-start gap-4">
                                        <div class="relative">
                                            <img v-if="user.profile && user.profile.profile_picture"
                                                :src="user.profile.profile_picture" :alt="user.name"
                                                class="w-16 h-16 rounded-full object-cover" />
                                            <div v-else
                                                class="w-16 h-16 bg-gray-200 dark:bg-blue-900/40 rounded-full flex items-center justify-center text-[#193CB8] dark:text-blue-200">
                                                <i class='bx bxs-user text-xl'></i>
                                            </div>
                                            <div class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white dark:border-[#1a2332]"
                                                :class="user.is_online ? 'bg-green-500' : 'bg-gray-300 dark:bg-blue-900/40'"></div>
                                        </div>

                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-800 dark:text-blue-100">{{ user.name }} {{ user.last_name_1 }}
                                                {{ user.last_name_2 }}</h3>
                                            <p v-if="user.profile && user.profile.job_title"
                                                class="text-gray-600 dark:text-blue-200 text-sm">{{ user.profile.job_title }}</p>

                                            <div v-if="user.profile && user.profile.location"
                                                class="flex items-center text-gray-500 dark:text-blue-300 text-sm mt-1">
                                                <i class='bx bx-map-pin mr-1'></i>
                                                <span>{{ user.profile.location }}</span>
                                            </div>

                                            <div class="mt-3 flex gap-2">
                                                <button
                                                    class="px-3 py-1.5 bg-[#193CB8] dark:bg-blue-700 text-white dark:text-blue-100 rounded-lg hover:bg-[#142d8c] dark:hover:bg-blue-900 transition-colors text-sm">
                                                    <i class='bx bx-user-plus mr-1'></i> Conectar
                                                </button>
                                                <button
                                                    @click="router.get('/perfil/' + (user.profile ? user.profile.slang : user.id))"
                                                    class="px-3 py-1.5 bg-gray-100 dark:bg-blue-900/40 text-gray-700 dark:text-blue-100 rounded-lg hover:bg-gray-200 dark:hover:bg-blue-800 transition-colors text-sm">
                                                    <i class='bx bx-user mr-1'></i> Ver Perfil
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else-if="activeTab === 'people'"
                                class="text-center py-8 bg-white dark:bg-[#1a2332] rounded-xl shadow-sm">
                                <div
                                    class="w-16 h-16 bg-[#193CB8]/10 dark:bg-[#193CB8]/20 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class='bx bx-user text-3xl text-[#193CB8] dark:text-blue-200'></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-blue-100 mb-1">No se encontraron personas</h3>
                                <p class="text-gray-600 dark:text-blue-200">Intenta con otros términos de búsqueda</p>
                            </div>

                            <div v-if="activeTab === 'all' && filteredUsers.length > 3" class="text-center mt-4">
                                <button @click="activeTab = 'people'"
                                    class="text-[#193CB8] dark:text-blue-200 hover:underline font-medium">
                                    Ver todas las personas
                                </button>
                            </div>
                        </div>

                        <div v-if="activeTab === 'all' || activeTab === 'groups'" class="mb-8">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-blue-100 mb-4"
                                v-if="activeTab === 'all' && filteredGroups.length > 0">
                                Grupos ({{ filteredGroups.length }})
                            </h2>

                            <div v-if="filteredGroups.length > 0" class="space-y-4">
                                <GroupCard v-for="group in filteredGroups" :key="group.id" :group="group" />
                            </div>

                            <div v-else-if="activeTab === 'groups'"
                                class="text-center py-8 bg-white dark:bg-[#1a2332] rounded-xl shadow-sm">
                                <div
                                    class="w-16 h-16 bg-[#193CB8]/10 dark:bg-[#193CB8]/20 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class='bx bx-group text-3xl text-[#193CB8] dark:text-blue-200'></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-blue-100 mb-1">No se encontraron grupos</h3>
                                <p class="text-gray-600 dark:text-blue-200">Intenta con otros términos de búsqueda</p>
                            </div>

                            <div v-if="activeTab === 'all' && filteredGroups.length > 3" class="text-center mt-4">
                                <button @click="activeTab = 'groups'"
                                    class="text-[#193CB8] dark:text-blue-200 hover:underline font-medium">
                                    Ver todos los grupos
                                </button>
                            </div>
                        </div>

                        <div v-if="activeTab === 'all' || activeTab === 'events'" class="mb-8">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-blue-100 mb-4"
                                v-if="activeTab === 'all' && filteredEvents.length > 0">
                                Eventos ({{ filteredEvents.length }})
                            </h2>

                            <div v-if="filteredEvents.length > 0"
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <EventCard v-for="event in filteredEvents" :key="event.id" :event="event" />
                            </div>

                            <div v-else-if="activeTab === 'events'"
                                class="text-center py-8 bg-white dark:bg-[#1a2332] rounded-xl shadow-sm">
                                <div
                                    class="w-16 h-16 bg-[#193CB8]/10 dark:bg-[#193CB8]/20 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class='bx bx-calendar-event text-3xl text-[#193CB8] dark:text-blue-200'></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-blue-100 mb-1">No se encontraron eventos</h3>
                                <p class="text-gray-600 dark:text-blue-200">Intenta con otros términos de búsqueda</p>
                            </div>

                            <div v-if="activeTab === 'all' && filteredEvents.length > 3" class="text-center mt-4">
                                <button @click="activeTab = 'events'"
                                    class="text-[#193CB8] dark:text-blue-200 hover:underline font-medium">
                                    Ver todos los eventos
                                </button>
                            </div>
                        </div>

                        <div v-if="activeTab === 'all' || activeTab === 'posts'">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-blue-100 mb-4"
                                v-if="activeTab === 'all' && filteredPosts.length > 0">
                                Publicaciones ({{ filteredPosts.length }})
                            </h2>

                            <div v-if="filteredPosts.length > 0" class="space-y-4">
                                <PostCard v-for="post in filteredPosts" :key="post.id" :post="post"
                                    :formatDate="formatDate" :auth="auth" :isMember="true" />
                            </div>

                            <div v-else-if="activeTab === 'posts'"
                                class="text-center py-8 bg-white dark:bg-[#1a2332] rounded-xl shadow-sm">
                                <div
                                    class="w-16 h-16 bg-[#193CB8]/10 dark:bg-[#193CB8]/20 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class='bx bx-news text-3xl text-[#193CB8] dark:text-blue-200'></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-blue-100 mb-1">No se encontraron publicaciones</h3>
                                <p class="text-gray-600 dark:text-blue-200">Intenta con otros términos de búsqueda</p>
                            </div>

                            <div v-if="activeTab === 'all' && filteredPosts.length > 3" class="text-center mt-4">
                                <button @click="activeTab = 'posts'" class="text-[#193CB8] dark:text-blue-200 hover:underline font-medium">
                                    Ver todas las publicaciones
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.hide-scrollbar::-webkit-scrollbar {
    display: none;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

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
