<script setup>
import { ref, defineProps, nextTick } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Layout from '@/Components/Layout.vue';
import GroupCard from '@/Components/Social/GroupCard.vue';
import PostCreator from '@/Components/Social/PostCreator.vue';
import PostCard from "@/Components/Social/PostCard.vue";
import EventsTab from '../../Components/Social/EventsTab.vue';

const searchQuery = ref('');
const activeTab = ref('descubrir');
const auth = usePage().props.auth;
const shareInputRef = ref(null);

const scrollToShareInput = async () => {
    await nextTick();
    if (shareInputRef.value) {
        shareInputRef.value.scrollIntoView({ behavior: 'smooth', block: 'center' });
        shareInputRef.value.classList.add('ring-2', 'ring-[#193CB8]', 'transition-all', 'duration-500');
        setTimeout(() => {
            shareInputRef.value.classList.remove('ring-2', 'ring-[#193CB8]');
        }, 2000);
    }
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

const props = defineProps({
    groups: {
        type: Array,
        user: Object,

    },
    popularGroups: {
        type: Array,
        user: Object,
    },
    posts: {
        type: Array,
    },
    featuredStories: {
        type: Array,
    },
    trendingTopics: {
        type: Array,
    },
    hasMorePosts: {
        type: Boolean,
    },
    events: {
        type: Array,
    },
    upcomingEvents: {
        type: Array,
    },
    suggestedConnections: {
        type: Array,
    },

});

const trendingTopics = props.trendingTopics;
const groups = ref(props.groups);
const popularGroups = ref(props.popularGroups);
const events = props.events;
const upcomingEvents = props.upcomingEvents;
const featuredStories = props.featuredStories;
const recentPosts = props.posts;
const suggestedConnections = props.suggestedConnections;

const loading = ref(false);

const loadMorePosts = () => {
    loading.value = true;
    router.get('/connect', { page: 2 }, {
        preserveScroll: true,
        onSuccess: () => {
            loading.value = false;
        }
    });
};

</script>

<template>
    <Layout>
        <div class="min-h-screen bg-gray-50 flex flex-col">
            <div class="bg-[#193CB8] text-white py-10">
                <div class="max-w-6xl mx-auto px-4">
                    <div class="flex flex-col items-center text-center mb-8">
                        <h1 class="text-4xl font-bold mb-3">Alumni Connect</h1>
                        <p class="text-blue-100 text-lg max-w-2xl mx-auto">
                            Conecta, comparte y crece con la comunidad Alumni. Descubre historias inspiradoras, eventos
                            exclusivos y amplía tu red de contactos.
                        </p>
                    </div>

                    <div class="max-w-xl mx-auto relative">
                        <div class="flex bg-white/20 backdrop-blur-sm rounded-full p-1 border border-white/30">
                            <input v-model="searchQuery" type="text" placeholder="Buscar personas, grupos, eventos..."
                                class="w-full px-5 py-3 bg-transparent text-white placeholder-blue-100 focus:outline-none rounded-full" />
                            <button @click="router.get('/connect/search?q=' + searchQuery)"
                                class="bg-white text-[#193CB8] hover:bg-blue-50 px-5 py-3 rounded-full flex items-center justify-center transition-colors">
                                <i class='bx bx-search text-xl'></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-center mt-8">
                        <div class="bg-white/10 backdrop-blur-sm rounded-full p-1 border border-white/30 inline-flex">
                            <button @click="activeTab = 'descubrir'" :class="[
                                'px-5 py-2 rounded-full transition-colors font-medium',
                                activeTab === 'descubrir' ? 'bg-white text-[#193CB8]' : 'text-white hover:bg-white/10'
                            ]">
                                <i class='bx bx-compass mr-1'></i> Descubrir
                            </button>
                            <!-- <button @click="activeTab = 'feed'" :class="[
                                'px-5 py-2 rounded-full transition-colors font-medium',
                                activeTab === 'feed' ? 'bg-white text-[#193CB8]' : 'text-white hover:bg-white/10'
                            ]">
                                <i class='bx bx-news mr-1'></i> Mi Feed
                            </button> -->
                            <button @click="activeTab = 'eventos'" :class="[
                                'px-5 py-2 rounded-full transition-colors font-medium',
                                activeTab === 'eventos' ? 'bg-white text-[#193CB8]' : 'text-white hover:bg-white/10'
                            ]">
                                <i class='bx bx-calendar-event mr-1'></i> Eventos
                            </button>
                            <button @click="activeTab = 'grupos'" :class="[
                                'px-5 py-2 rounded-full transition-colors font-medium',
                                activeTab === 'grupos' ? 'bg-white text-[#193CB8]' : 'text-white hover:bg-white/10'
                            ]">
                                <i class='bx bx-group mr-1'></i> Grupos
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-1 py-8">
                <div class="max-w-6xl mx-auto px-4">
                    <div v-if="activeTab === 'descubrir'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 space-y-6">
                            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                                <div class="p-4 border-b border-gray-100">
                                    <h2 class="text-xl font-bold text-gray-800">Historias Destacadas</h2>
                                </div>
                                <div class="p-4">
                                    <div class="flex overflow-x-auto pb-4 space-x-4">
                                        <div v-for="story in featuredStories" :key="story.id"
                                            class="flex-shrink-0 w-64 bg-gradient-to-br from-[#193CB8]/5 to-[#2748c6]/10 rounded-lg overflow-hidden border border-[#193CB8]/20 cursor-pointer hover:shadow-md transition-shadow">
                                            <div class="p-4">
                                                <div class="flex items-center mb-3 gap-2">
                                                    <img v-if="story.user.profile && story.user.profile.profile_picture"
                                                        :src="story.user.profile.profile_picture || '/images/default-avatar.jpg'"
                                                        :alt="story.user.name" class="w-8 h-8 object-cover" />
                                                    <div v-else
                                                        class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-[#193CB8] shadow-sm border-2 border-white">
                                                        <i class='bx bxs-user text-xl'></i>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-800">{{ story.user.name +
                                                        ' ' + story.user.last_name_1 + ' ' + story.user.last_name_2 ??
                                                        null }}</span>
                                                </div>
                                                <p class="text-gray-600 text-sm mb-3 line-clamp-2"
                                                    v-html="story.content"></p>
                                                <div class="flex items-center text-gray-500 text-xs">
                                                    <span class="flex items-center mr-3">
                                                        <i class='bx bx-heart mr-1'></i> {{ story.likes_count }}
                                                    </span>
                                                    <span class="flex items-center">
                                                        <i class='bx bx-message-rounded mr-1'></i> {{
                                                            story.comments_count }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
<!-- 
                                        <div @click="scrollToShareInput"
                                            class="flex-shrink-0 w-64 bg-gradient-to-br from-[#193CB8]/5 to-[#2748c6]/10 rounded-lg overflow-hidden border border-dashed border-[#193CB8]/30 cursor-pointer hover:shadow-md transition-shadow flex items-center justify-center">
                                            <div class="text-center p-4">
                                                <div
                                                    class="w-12 h-12 bg-[#193CB8]/10 rounded-full flex items-center justify-center mx-auto mb-2">
                                                    <i class='bx bx-plus text-[#193CB8] text-2xl'></i>
                                                </div>
                                                <p class="text-[#193CB8] font-medium">Comparte tu historia</p>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <h2 class="text-xl font-bold text-gray-800 px-2">Publicaciones Recientes</h2>

                                <PostCard v-for="post in recentPosts" :key="post.id" :post="post"
                                    :formatDate="formatDate" :auth="auth" :data-post-id="post.id" :isMember="true" />

                                <div class="text-center pb-4">
                                    <p class="text-gray-500 text-lg font-semibold">
                                        <span v-if="!loading && props.hasMorePosts"
                                            class="text-[#193CB8] underline cursor-pointer" @click="loadMorePosts">
                                            Cargar más publicaciones
                                        </span>
                                    <div v-else-if="loading"
                                        class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#193CB8] border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]">
                                    </div>
                                    <div v-else class="text-[#193CB8] text-lg font-semibold">
                                        ¡Has llegado al final!
                                    </div>
                                    </p>
                                </div>

                                <PostCreator :auth="auth" @postCreated="handlePostCreated" ref="shareInputRef"/>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-white rounded-xl shadow-sm overflow-hidden" v-if="upcomingEvents.length > 0">
                                <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                                    <h2 class="text-lg font-bold text-gray-800">Próximos Eventos</h2>
                                    <a @click="activeTab = 'eventos'"
                                        class="text-[#193CB8] text-sm hover:underline cursor-pointer">Ver todos</a>
                                </div>
                                <div class="p-4 space-y-4">
                                    <div v-for="event in upcomingEvents" :key="event.id"
                                        class="flex gap-3 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                                        <img v-if="event.photos && event.photos[0]" :src="event.photos[0].photo_path"
                                            :alt="event.title"
                                            class="w-16 h-16 rounded-lg object-cover flex-shrink-0" />
                                        <div v-else
                                            class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center text-gray-500">
                                            <i class='bx bx-flag text-xl'></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-800 hover:underline cursor-pointer"
                                                @click="router.get('/eventos/' + event.slug)">{{ event.title }}</h3>
                                            <div class="flex items-center text-gray-500 text-sm mt-1">
                                                <i class='bx bx-calendar mr-1'></i>
                                                <span>{{ formatDate(event.event_date) }}</span>
                                            </div>
                                            <div class="flex items-center text-gray-500 text-sm mt-1">
                                                <i class='bx bx-map mr-1'></i>
                                                <span>{{ event.location }}</span>
                                            </div>
                                            <div class="flex items-center text-gray-500 text-sm mt-1">
                                                <i class='bx bx-user mr-1'></i>
                                                <span>{{ event.attendees_count }} {{ event.attendees_count === 1 ?
                                                    'asistente' : 'asistentes' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                                <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                                    <h2 class="text-lg font-bold text-gray-800">Grupos Populares</h2>
                                    <a @click="activeTab = 'grupos'"
                                        class="text-[#193CB8] text-sm hover:underline cursor-pointer">Ver todos</a>
                                </div>
                                <div class="p-4">
                                    <div v-for="group in popularGroups" :key="group.id"
                                        @click="router.get('/grupos/' + group.slug)"
                                        class="flex items-center gap-3 py-3 border-b border-gray-100 cursor-pointer hover:bg-gray-100 last:border-0 ">
                                        <img v-if="group.group_logo" :src="group.group_logo" :alt="group.name"
                                            class="w-12 h-12 rounded-lg object-cover flex-shrink-0" />

                                        <div v-else
                                            class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center text-gray-500">
                                            <i class='bx bx-group text-xl'></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-800">{{ group.name }}</h3>
                                            <div class="flex items-center justify-between text-gray-500 text-sm mt-1">
                                                <span>{{ group.members_count }} {{ group.members_count == 1 ? 'miembro'
                                                    : 'miembros' }}</span>
                                                <span
                                                    class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs">
                                                    {{ group.category }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 border-t border-gray-100">
                                    <button @click="router.get('/grupos/nuevo')"
                                        class="w-full cursor-pointer py-2 bg-[#193CB8]/10 text-[#193CB8] rounded-lg hover:bg-[#193CB8]/20 transition-colors font-medium">
                                        <i class='bx bx-plus-circle mr-1'></i> Crear un grupo
                                    </button>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                                <div class="p-4 border-b border-gray-100">
                                    <h2 class="text-lg font-bold text-gray-800">Tendencias</h2>
                                </div>
                                <div class="p-4">
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="topic in trendingTopics" :key="topic.word"
                                            @click="router.get('/connect/search?q=' + topic.word)"
                                            class="px-3 py-1.5 bg-[#193CB8]/10 text-[#193CB8] rounded-full text-sm flex items-center cursor-pointer hover:bg-[#193CB8]/20 transition-colors">
                                            #{{ topic.word }}
                                            <span class="ml-1 text-xs bg-[#193CB8] text-white rounded-full px-1.5">
                                                {{ topic.count }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                                <div class="p-4 border-b border-gray-100">
                                    <h2 class="text-lg font-bold text-gray-800">Personas que quizás conozcas</h2>
                                </div>
                                <div class="p-4 space-y-4">
                                    <div v-for="connection in suggestedConnections" :key="connection.id"
                                        class="flex items-center gap-3 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                                        <img :src="connection.profile?.profile_picture" :alt="connection.name"
                                            class="w-12 h-12 rounded-full object-cover flex-shrink-0" />
                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-800 hover:underline cursor-pointer">{{
                                                connection.name + ' ' + connection.last_name_1 + ' ' +
                                                connection.last_name_2 ?? null }}</h3>
                                            <p class="text-gray-500 text-sm">{{ connection.profile?.job_title }}</p>
                                            <p class="text-gray-400 text-xs mt-1">
                                                {{ connection.mutual_connections.length }}
                                                {{ connection.mutual_connections.length === 1 ? 'conexión' :
                                                    'conexiones' }}
                                                en común
                                            </p>
                                        </div>
                                        <button
                                            class="px-3 py-1.5 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors text-sm cursor-pointer">
                                            <i class='bx bx-user-plus mr-1'></i> Conectar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeTab === 'feed'" class="text-center py-12">
                        <div
                            class="w-20 h-20 bg-[#193CB8]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class='bx bx-news text-4xl text-[#193CB8]'></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Tu Feed Personalizado</h2>
                        <p class="text-gray-600 max-w-md mx-auto">
                            Aquí verás publicaciones y actualizaciones de tus conexiones y grupos que sigues.
                        </p>
                    </div>

                    <div v-if="activeTab === 'eventos'">
                        <EventsTab :events="events" />
                    </div>

                    <div v-if="activeTab === 'grupos'" class="text-center py-12">
                        <div v-if="groups.length === 0">
                            <div
                                class="w-20 h-20 bg-[#193CB8]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class='bx bx-group text-4xl text-[#193CB8]'></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Grupos de Interés</h2>
                            <p class="text-gray-600 max-w-md mx-auto">
                                Únete a comunidades de Alumni con intereses similares y amplía tu red profesional.
                            </p>
                        </div>
                        <GroupCard v-for="(group, index) in groups" :key="index" :group="group" class="mb-4" />

                    </div>
                </div>
            </div>

            <button
                class="fixed bottom-6 right-6 w-14 h-14 rounded-full bg-[#193CB8] text-white shadow-lg flex items-center justify-center hover:bg-[#142d8c] transition-all duration-300 z-50">
                <i class='bx bx-plus text-2xl'></i>
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

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>