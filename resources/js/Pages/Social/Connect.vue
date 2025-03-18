<script setup>
import { ref, defineProps } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Layout from '@/Components/Layout.vue';
import GroupCard from '@/Components/Social/GroupCard.vue';


const searchQuery = ref('');
const activeTab = ref('descubrir');
const showMobileMenu = ref(false);
const user = usePage().props.auth.user;

const trendingTopics = [
    { id: 1, name: 'Eventos de networking', count: 28 },
    { id: 2, name: 'Desarrollo profesional', count: 24 },
    { id: 3, name: 'Emprendimiento', count: 19 },
    { id: 4, name: 'Tecnología', count: 17 },
    { id: 5, name: 'Sostenibilidad', count: 15 }
];

const props = defineProps({
    groups: {
        type: Array,
        user: Object,

    },
    popularGroups: {
        type: Array,
        user: Object,
    },

});

const groups = ref(props.groups);
const popularGroups = ref(props.popularGroups);

const upcomingEvents = [
    {
        id: 1,
        title: 'Encuentro Alumni 2023',
        date: '15 Dic 2023',
        time: '18:00',
        location: 'Campus Central',
        attendees: 87,
        image: '/placeholder.svg?height=120&width=200'
    },
    {
        id: 2,
        title: 'Workshop: Liderazgo Creativo',
        date: '22 Dic 2023',
        time: '10:00',
        location: 'Online',
        attendees: 45,
        image: '/placeholder.svg?height=120&width=200'
    }
];


const alumniProfiles = [
    {
        id: 1,
        name: 'Ana García',
        profile_picture: '/placeholder.svg?height=80&width=80',
        is_online: true,
        headline: 'Creativa digital & Desarrolladora Frontend',
        location: 'Madrid',
        mutual_connections: 12,
        interests: ['Diseño', 'Tecnología', 'Viajes'],
        activity: 'Compartió un artículo sobre diseño UX hace 2 días'
    },
    {
        id: 2,
        name: 'Carlos Rodríguez',
        profile_picture: '/placeholder.svg?height=80&width=80',
        is_online: false,
        headline: 'Fotógrafo & Marketing Digital',
        location: 'Barcelona',
        mutual_connections: 8,
        interests: ['Fotografía', 'Marketing', 'Música'],
        activity: 'Asistirá al evento "Workshop: Fotografía Creativa"'
    },
    {
        id: 3,
        name: 'Laura Martínez',
        profile_picture: '/placeholder.svg?height=80&width=80',
        is_online: true,
        headline: 'Data Scientist & Entusiasta de IA',
        location: 'Valencia',
        mutual_connections: 5,
        interests: ['Inteligencia Artificial', 'Ciencia de Datos', 'Lectura'],
        activity: 'Publicó: "Cómo la IA está transformando nuestra forma de trabajar"'
    },
    {
        id: 4,
        name: 'Miguel Fernández',
        profile_picture: '/placeholder.svg?height=80&width=80',
        is_online: false,
        headline: 'Diseñador UX/UI & Ilustrador',
        location: 'Madrid',
        mutual_connections: 15,
        interests: ['Diseño', 'Arte', 'Cine'],
        activity: 'Compartió su nuevo proyecto de diseño'
    }
];

const featuredStories = [
    {
        id: 1,
        user: {
            name: 'Elena Sánchez',
            profile_picture: '/placeholder.svg?height=40&width=40'
        },
        title: 'Mi experiencia como emprendedora',
        preview: 'Después de graduarme, decidí lanzar mi propio proyecto...',
        likes: 45,
        comments: 12
    },
    {
        id: 2,
        user: {
            name: 'Javier López',
            profile_picture: '/placeholder.svg?height=40&width=40'
        },
        title: 'Trabajando en Silicon Valley',
        preview: 'Mi viaje desde Alumni hasta una empresa tecnológica en EE.UU...',
        likes: 78,
        comments: 23
    }
];

const recentPosts = [
    {
        id: 1,
        user: {
            id: 1,
            name: 'María Gómez',
            profile_picture: '/placeholder.svg?height=50&width=50',
            headline: 'Marketing Manager'
        },
        content: 'Acabo de publicar un artículo sobre las tendencias de marketing para 2024. ¡Me encantaría conocer vuestras opiniones! #Marketing #Tendencias2024',
        image: '/placeholder.svg?height=300&width=500',
        likes: 34,
        comments: 8,
        time: 'hace 3 horas'
    },
    {
        id: 2,
        user: {
            id: 2,
            name: 'Pablo Ruiz',
            profile_picture: '/placeholder.svg?height=50&width=50',
            headline: 'Desarrollador Full Stack'
        },
        content: '¡Emocionado de anunciar que me uno al equipo de desarrollo de @TechInnovation! Nuevos retos y proyectos apasionantes por delante. #NuevoEmpleo #Desarrollo',
        image: null,
        likes: 56,
        comments: 15,
        time: 'hace 1 día'
    }
];

const suggestedConnections = [
    {
        id: 1,
        name: 'Sara Navarro',
        profile_picture: '/placeholder.svg?height=60&width=60',
        headline: 'Diseñadora Gráfica',
        mutual_connections: 7
    },
    {
        id: 2,
        name: 'Daniel Torres',
        profile_picture: '/placeholder.svg?height=60&width=60',
        headline: 'Ingeniero de Software',
        mutual_connections: 5
    },
    {
        id: 3,
        name: 'Lucía Moreno',
        profile_picture: '/placeholder.svg?height=60&width=60',
        headline: 'Consultora de Marketing',
        mutual_connections: 3
    }
];
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        console.log('File selected:', file.name);
    }
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
                            <button
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
                            <button @click="activeTab = 'feed'" :class="[
                                'px-5 py-2 rounded-full transition-colors font-medium',
                                activeTab === 'feed' ? 'bg-white text-[#193CB8]' : 'text-white hover:bg-white/10'
                            ]">
                                <i class='bx bx-news mr-1'></i> Mi Feed
                            </button>
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
                                                <div class="flex items-center mb-3">
                                                    <img :src="story.user.profile_picture" :alt="story.user.name"
                                                        class="w-8 h-8 rounded-full object-cover mr-2" />
                                                    <span class="text-sm font-medium text-gray-800">{{ story.user.name
                                                    }}</span>
                                                </div>
                                                <h3 class="font-bold text-gray-800 mb-2">{{ story.title }}</h3>
                                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ story.preview }}
                                                </p>
                                                <div class="flex items-center text-gray-500 text-xs">
                                                    <span class="flex items-center mr-3">
                                                        <i class='bx bx-heart mr-1'></i> {{ story.likes }}
                                                    </span>
                                                    <span class="flex items-center">
                                                        <i class='bx bx-message-rounded mr-1'></i> {{ story.comments }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="flex-shrink-0 w-64 bg-gradient-to-br from-[#193CB8]/5 to-[#2748c6]/10 rounded-lg overflow-hidden border border-dashed border-[#193CB8]/30 cursor-pointer hover:shadow-md transition-shadow flex items-center justify-center">
                                            <div class="text-center p-4">
                                                <div
                                                    class="w-12 h-12 bg-[#193CB8]/10 rounded-full flex items-center justify-center mx-auto mb-2">
                                                    <i class='bx bx-plus text-[#193CB8] text-2xl'></i>
                                                </div>
                                                <p class="text-[#193CB8] font-medium">Comparte tu historia</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <h2 class="text-xl font-bold text-gray-800 px-2">Publicaciones Recientes</h2>

                                <div v-for="post in recentPosts" :key="post.id"
                                    class="bg-white rounded-xl shadow-sm overflow-hidden">
                                    <div class="p-4">
                                        <div class="flex items-start mb-3">
                                            <img :src="post.user.profile_picture" :alt="post.user.name"
                                                class="w-10 h-10 rounded-full object-cover mr-3" />
                                            <div>
                                                <h3 class="font-bold text-gray-800">{{ post.user.name }}</h3>
                                                <p class="text-gray-500 text-sm">{{ post.user.headline }}</p>
                                                <p class="text-gray-400 text-xs">{{ post.time }}</p>
                                            </div>
                                        </div>

                                        <!-- Contenido de la publicación -->
                                        <p class="text-gray-700 mb-4">{{ post.content }}</p>

                                        <img v-if="post.image" :src="post.image" alt="Imagen de la publicación"
                                            class="w-full h-auto rounded-lg mb-4" />

                                        <div
                                            class="flex items-center justify-between text-gray-500 text-sm border-t border-gray-100 pt-3">
                                            <div class="flex items-center">
                                                <span class="flex items-center mr-4">
                                                    <i class='bx bx-heart mr-1'></i> {{ post.likes }}
                                                </span>
                                                <span class="flex items-center">
                                                    <i class='bx bx-message-rounded mr-1'></i> {{ post.comments }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="flex items-center">
                                                    <i class='bx bx-share-alt mr-1'></i> Compartir
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex border-t border-gray-100">
                                        <button
                                            class="flex-1 py-3 flex items-center justify-center text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class='bx bx-heart mr-1'></i> Me gusta
                                        </button>
                                        <button
                                            class="flex-1 py-3 flex items-center justify-center text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class='bx bx-message-rounded mr-1'></i> Comentar
                                        </button>
                                        <button
                                            class="flex-1 py-3 flex items-center justify-center text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class='bx bx-share-alt mr-1'></i> Compartir
                                        </button>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                                    <div class="p-4">
                                        <div class="flex items-center">
                                            <img :src="user.profile.profile_picture" alt="Tu perfil"
                                                class="w-10 h-10 rounded-full object-cover mr-3" />
                                            <input v-model="searchQuery" type="text"
                                                placeholder="¿Qué quieres compartir hoy?"
                                                class="flex-1 bg-gray-100 rounded-full px-4 py-2 text-gray-500 focus:outline-none hover:bg-gray-200 transition-colors" />
                                        </div>
                                        <div class="flex mt-3 border-t border-gray-100 pt-3">
                                            <label
                                                class="flex-1 py-1 flex items-center justify-center cursor-pointer text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                                <i class='bx bx-image-alt mr-1 text-blue-500'></i> Foto
                                                <input type="file" class="hidden" @change="handleFileUpload" />
                                            </label>
                                            <label
                                                class="flex-1 py-1 flex items-center justify-center cursor-pointer text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                                <i class='bx bx-video mr-1 text-green-500'></i> Video
                                                <input type="file" class="hidden" @change="handleFileUpload" />
                                            </label>
                                            <button
                                                class="flex-1 py-1 flex items-center justify-center cursor-pointer text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                                <i class='bx bx-calendar-event mr-1 text-orange-500'></i> Evento
                                                <input type="text" class="hidden" @change="handleEventInput" />
                                            </button>
                                            <button
                                                class="flex-1 py-1 flex items-center justify-center cursor-pointer text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                                <i class='bx bx-news mr-1 text-purple-500'></i> Artículo
                                                <input type="text" class="hidden" @change="handleArticleInput" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                                <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                                    <h2 class="text-lg font-bold text-gray-800">Próximos Eventos</h2>
                                    <a href="#" class="text-[#193CB8] text-sm hover:underline">Ver todos</a>
                                </div>
                                <div class="p-4 space-y-4">
                                    <div v-for="event in upcomingEvents" :key="event.id"
                                        class="flex gap-3 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                                        <img :src="event.image" :alt="event.title"
                                            class="w-16 h-16 rounded-lg object-cover flex-shrink-0" />
                                        <div>
                                            <h3 class="font-bold text-gray-800">{{ event.title }}</h3>
                                            <div class="flex items-center text-gray-500 text-sm mt-1">
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
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                                <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                                    <h2 class="text-lg font-bold text-gray-800">Grupos Populares</h2>
                                    <a @click="activeTab = 'grupos'"
                                        class="text-[#193CB8] text-sm hover:underline cursor-ponter">Ver todos</a>
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
                                        <span v-for="topic in trendingTopics" :key="topic.id"
                                            class="px-3 py-1.5 bg-[#193CB8]/10 text-[#193CB8] rounded-full text-sm flex items-center cursor-pointer hover:bg-[#193CB8]/20 transition-colors">
                                            #{{ topic.name }}
                                            <span class="ml-1 text-xs bg-[#193CB8] text-white rounded-full px-1.5">{{
                                                topic.count }}</span>
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
                                        <img :src="connection.profile_picture" :alt="connection.name"
                                            class="w-12 h-12 rounded-full object-cover flex-shrink-0" />
                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-800">{{ connection.name }}</h3>
                                            <p class="text-gray-500 text-sm">{{ connection.headline }}</p>
                                            <p class="text-gray-400 text-xs mt-1">{{ connection.mutual_connections }}
                                                conexiones en común</p>
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

                    <div v-if="activeTab === 'descubrir'" class="mt-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Descubre Nuevas Personas</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div v-for="profile in alumniProfiles" :key="profile.id"
                                class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow flex flex-col justify-between">
                                <div class="h-24 bg-gradient-to-r from-[#193CB8] to-[#2748c6] relative">
                                    <div class="absolute left-1/2 transform -translate-x-1/2 -bottom-10">
                                        <div class="relative">
                                            <img :src="profile.profile_picture" :alt="profile.name"
                                                class="w-20 h-20 rounded-full object-cover border-4 border-white cursor-pointer" />
                                            <div class="absolute bottom-1 right-1 w-4 h-4 rounded-full border-2 border-white"
                                                :class="profile.is_online ? 'bg-green-500' : 'bg-gray-300'"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-12 p-4 text-center">
                                    <h3 class="text-xl font-bold text-gray-800">{{ profile.name }}</h3>
                                    <p class="text-gray-600 mt-1">{{ profile.headline }}</p>

                                    <div class="flex items-center justify-center text-gray-500 text-sm mt-2">
                                        <i class='bx bx-map-pin mr-1'></i>
                                        <span>{{ profile.location }}</span>
                                    </div>

                                    <div class="flex items-center justify-center text-gray-500 text-sm mt-1">
                                        <i class='bx bx-link mr-1'></i>
                                        <span>{{ profile.mutual_connections }} conexiones en común</span>
                                    </div>

                                    <div class="flex flex-wrap justify-center gap-1 mt-3">
                                        <span v-for="(interest, index) in profile.interests" :key="index"
                                            class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-full text-xs">
                                            {{ interest }}
                                        </span>
                                    </div>

                                    <div class="mt-4 pt-4 border-t border-gray-100 text-sm text-gray-600">
                                        <p class="line-clamp-2">{{ profile.activity }}</p>
                                    </div>


                                </div>
                                <div class="flex mt-4 gap-2 justify-end p-4">
                                    <button
                                        class="flex-1 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors text-sm cursor-pointer">
                                        <i class='bx bx-user-plus mr-1'></i> Conectar
                                    </button>
                                    <button
                                        class="flex-1 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm cursor-pointer">
                                        <i class='bx bx-envelope mr-1'></i> Mensaje
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-center mt-8">
                            <button
                                class="px-6 py-3 bg-[#193CB8]/10 text-[#193CB8] rounded-full hover:bg-[#193CB8]/20 transition-colors font-medium cursor-pointer">
                                Descubrir más Alumni
                            </button>
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

                    <div v-if="activeTab === 'eventos'" class="text-center py-12">
                        <div
                            class="w-20 h-20 bg-[#193CB8]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class='bx bx-calendar-event text-4xl text-[#193CB8]'></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Eventos para Alumni</h2>
                        <p class="text-gray-600 max-w-md mx-auto">
                            Descubre eventos exclusivos, networking y oportunidades de desarrollo profesional.
                        </p>
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
                        <GroupCard v-for="(group, index) in groups" :key="index" :group="group" />

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