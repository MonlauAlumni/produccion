<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Layout from '@/Components/Layout.vue';
import GroupCard from '@/Components/Social/GroupCard.vue';

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
            events: []
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

const totalResults = computed(() => {
    return (filteredUsers.value?.length || 0) +
        (filteredGroups.value?.length || 0) +
        (filteredEvents.value?.length || 0);
});

// Handle search submission
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
        <div class="min-h-screen bg-gray-50 flex flex-col">
            <div class="bg-[#193CB8] text-white py-8">
                <div class="max-w-6xl mx-auto px-4">
                    <div class="flex flex-col items-center text-center mb-6">
                        <h1 class="text-3xl font-bold mb-2">Resultados de búsqueda</h1>
                        <p class="text-blue-100 text-lg max-w-2xl mx-auto">
                            Encuentra personas, grupos y eventos en la comunidad Alumni
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
                    <div class="mb-6 border-b border-gray-200">
                        <div class="flex overflow-x-auto hide-scrollbar">
                            <button @click="activeTab = 'all'" :class="[
                                'px-4 py-2 font-medium whitespace-nowrap',
                                activeTab === 'all' ? 'text-[#193CB8] border-b-2 border-[#193CB8]' : 'text-gray-500 hover:text-gray-700 cursor-pointer'
                            ]">
                                Todos ({{ totalResults }})
                            </button>
                            <button @click="activeTab = 'people'" :class="[
                                'px-4 py-2 font-medium whitespace-nowrap',
                                activeTab === 'people' ? 'text-[#193CB8] border-b-2 border-[#193CB8]' : 'text-gray-500 hover:text-gray-700 cursor-pointer'
                            ]">
                                <i class='bx bx-user mr-1'></i> Personas ({{ filteredUsers.length }})
                            </button>
                            <button @click="activeTab = 'groups'" :class="[
                                'px-4 py-2 font-medium whitespace-nowrap',
                                activeTab === 'groups' ? 'text-[#193CB8] border-b-2 border-[#193CB8]' : 'text-gray-500 hover:text-gray-700 cursor-pointer'
                            ]">
                                <i class='bx bx-group mr-1'></i> Grupos ({{ filteredGroups.length }})
                            </button>
                            <button @click="activeTab = 'events'" :class="[
                                'px-4 py-2 font-medium whitespace-nowrap',
                                activeTab === 'events' ? 'text-[#193CB8] border-b-2 border-[#193CB8]' : 'text-gray-500 hover:text-gray-700 cursor-pointer'
                            ]">
                                <i class='bx bx-calendar-event mr-1'></i> Eventos ({{ filteredEvents.length }})
                            </button>
                        </div>
                    </div>

                    <div v-if="isLoading" class="flex justify-center items-center py-12">
                        <div
                            class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#193CB8] border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]">
                        </div>
                        <span class="ml-3 text-gray-600">Buscando...</span>
                    </div>

                    <div v-else-if="totalResults === 0" class="text-center py-12">
                        <div
                            class="w-20 h-20 bg-[#193CB8]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class='bx bx-search-alt text-4xl text-[#193CB8]'></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">No se encontraron resultados</h2>
                        <p class="text-gray-600 max-w-md mx-auto">
                            No hemos encontrado resultados para "{{ searchQuery }}". Intenta con otros términos o revisa
                            la ortografía.
                        </p>
                    </div>

                    <div v-else>
                        <div v-if="activeTab === 'all' || activeTab === 'people'" class="mb-8">
                            <h2 class="text-xl font-bold text-gray-800 mb-4"
                                v-if="activeTab === 'all' && filteredUsers.length > 0">
                                Personas ({{ filteredUsers.length }})
                            </h2>

                            <div v-if="filteredUsers.length > 0"
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div v-for="user in filteredUsers" :key="user.id"
                                    class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                                    <div class="p-4 flex items-start gap-4">
                                        <div class="relative">
                                            <img :src="user.profile_picture || '/images/default-avatar.jpg'"
                                                :alt="user.name" class="w-16 h-16 rounded-full object-cover" />
                                            <div class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white"
                                                :class="user.is_online ? 'bg-green-500' : 'bg-gray-300'"></div>
                                        </div>

                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-800">{{ user.name }}</h3>
                                            <p class="text-gray-600 text-sm">{{ user.headline }}</p>

                                            <div class="flex items-center text-gray-500 text-sm mt-1">
                                                <i class='bx bx-map-pin mr-1'></i>
                                                <span>{{ user.profile.location }}</span>
                                            </div>

                                            <div class="flex items-center text-gray-500 text-sm mt-1">
                                                <i class='bx bx-link mr-1'></i>
                                                <span>{{ user.mutual_connections }} conexiones en común</span>
                                            </div>

                                            <div class="mt-3 flex gap-2">
                                                <button
                                                    class="px-3 py-1.5 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors text-sm">
                                                    <i class='bx bx-user-plus mr-1'></i> Conectar
                                                </button>
                                                <button @click="router.get('/perfil/' + user.profile.slang)"
                                                    class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                                    <i class='bx bx-user mr-1'></i> Ver Perfil
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else-if="activeTab === 'people'"
                                class="text-center py-8 bg-white rounded-xl shadow-sm">
                                <div
                                    class="w-16 h-16 bg-[#193CB8]/10 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class='bx bx-user text-3xl text-[#193CB8]'></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-1">No se encontraron personas</h3>
                                <p class="text-gray-600">Intenta con otros términos de búsqueda</p>
                            </div>

                            <div v-if="activeTab === 'all' && filteredUsers.length > 3" class="text-center mt-4">
                                <button @click="activeTab = 'people'"
                                    class="text-[#193CB8] hover:underline font-medium">
                                    Ver todas las personas
                                </button>
                            </div>
                        </div>

                        <div v-if="activeTab === 'all' || activeTab === 'groups'" class="mb-8">
                            <h2 class="text-xl font-bold text-gray-800 mb-4"
                                v-if="activeTab === 'all' && filteredGroups.length > 0">
                                Grupos ({{ filteredGroups.length }})
                            </h2>

                            <div v-if="filteredGroups.length > 0" class="space-y-4">
                                <GroupCard v-for="group in filteredGroups" :group="group" />
                            </div>

                            <div v-else-if="activeTab === 'groups'"
                                class="text-center py-8 bg-white rounded-xl shadow-sm">
                                <div
                                    class="w-16 h-16 bg-[#193CB8]/10 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class='bx bx-group text-3xl text-[#193CB8]'></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-1">No se encontraron grupos</h3>
                                <p class="text-gray-600">Intenta con otros términos de búsqueda</p>
                            </div>

                            <div v-if="activeTab === 'all' && filteredGroups.length > 3" class="text-center mt-4">
                                <button @click="activeTab = 'groups'"
                                    class="text-[#193CB8] hover:underline font-medium">
                                    Ver todos los grupos
                                </button>
                            </div>
                        </div>

                        <div v-if="activeTab === 'all' || activeTab === 'events'">
                            <h2 class="text-xl font-bold text-gray-800 mb-4"
                                v-if="activeTab === 'all' && filteredEvents.length > 0">
                                Eventos ({{ filteredEvents.length }})
                            </h2>

                            <div v-if="filteredEvents.length > 0"
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div v-for="event in filteredEvents" :key="event.id"
                                    class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                                    <div class="h-40 overflow-hidden">
                                        <img :src="event.image" :alt="event.title" class="w-full h-full object-cover" />
                                    </div>

                                    <div class="p-4">
                                        <h3 class="font-bold text-gray-800 text-lg">{{ event.title }}</h3>

                                        <div class="flex items-center text-gray-500 text-sm mt-2">
                                            <i class='bx bx-calendar mr-1'></i>
                                            <span>{{ event.date }} · {{ event.time }}</span>
                                        </div>

                                        <div class="flex items-center text-gray-500 text-sm mt-1">
                                            <i class='bx bx-map mr-1'></i>
                                            <span>{{ event.location }}</span>
                                        </div>

                                        <div class="flex items-center text-gray-500 text-sm mt-1">
                                            <i class='bx bx-user mr-1'></i>
                                            <span>{{ event.attendees }} asistentes</span>
                                        </div>

                                        <div class="mt-4 flex gap-2">
                                            <button
                                                class="flex-1 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors text-sm">
                                                <i class='bx bx-calendar-check mr-1'></i> Asistir
                                            </button>
                                            <button
                                                class="flex-1 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                                <i class='bx bx-info-circle mr-1'></i> Detalles
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else-if="activeTab === 'events'"
                                class="text-center py-8 bg-white rounded-xl shadow-sm">
                                <div
                                    class="w-16 h-16 bg-[#193CB8]/10 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class='bx bx-calendar-event text-3xl text-[#193CB8]'></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-1">No se encontraron eventos</h3>
                                <p class="text-gray-600">Intenta con otros términos de búsqueda</p>
                            </div>

                            <div v-if="activeTab === 'all' && filteredEvents.length > 3" class="text-center mt-4">
                                <button @click="activeTab = 'events'"
                                    class="text-[#193CB8] hover:underline font-medium">
                                    Ver todos los eventos
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