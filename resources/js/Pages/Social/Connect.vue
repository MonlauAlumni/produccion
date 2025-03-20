<script setup>
import { ref, defineProps } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Layout from '@/Components/Layout.vue';
import GroupCard from '@/Components/Social/GroupCard.vue';


const searchQuery = ref('');
const activeTab = ref('descubrir');
const showMobileMenu = ref(false);
const user = usePage().props.auth.user;

const scrollToShareInput = () => {
    const shareInput = document.getElementById('shareInput');
    shareInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
    shareInput.classList.add('ring-2', 'ring-[#193CB8]', 'transition-all', 'duration-500');
    setTimeout(() => {
        shareInput.classList.remove('ring-2', 'ring-[#193CB8]');
    }, 2000);
};

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
        title: 'Conferencia Monlau 2025',
        date: '15 Abr 2025',
        time: '18:00',
        location: 'La Sagrera, Barcelona',
        attendees: 87,
        image: 'data:image/png;base64,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'
    },
    {
        id: 2,
        title: 'Workshop: Liderazgo Creativo',
        date: '22 Mar 2025',
        time: '10:00',
        location: 'Pl. Espanya, Barcelona',
        attendees: 45,
        image: 'https://www.creativefabrica.com/wp-content/uploads/2021/08/21/workshop-logo-Graphics-16131907-1.jpg'
    }
];


const alumniProfiles = [
    {
        id: 1,
        name: 'Ana García',
        profile_picture: 'https://media.istockphoto.com/id/1432226243/es/foto/feliz-joven-de-color-sonriendo-a-la-c%C3%A1mara-en-un-estudio.jpg?s=612x612&w=0&k=20&c=WZ13cFaGQF7Y062lW5RqoKSp47kdqmvb3EJvy5NZhV8=',
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
        profile_picture: 'data:image/jpeg;base64,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',
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
        profile_picture: 'https://img.freepik.com/foto-gratis/mujer-sonriente-mirando-camara-sentada-frente-superficie-amarilla_23-2148204630.jpg',
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
        profile_picture: 'https://caravacasonrie.com/wp-content/uploads/2021/10/Hombre-guapo-sonriendo-1-1.jpg',
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
            profile_picture: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSMOAIGPabXqkJdwsTMZz28liHzBlFpioGmsg&s'
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
            profile_picture: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRUIq63KhI4KOEIyTpz6AmUyVeWAgGALLpoZ9edOyO_AkqWsg_AME-GpTmJn7jR4Oars1A&usqp=CAU'
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
            profile_picture: 'https://i.pinimg.com/736x/f9/a2/b9/f9a2b906b0164ad5bee5d1df391648bf.jpg',
            headline: 'Marketing Manager'
        },
        content: 'Acabo de publicar un artículo sobre las tendencias de marketing para 2025. ¡Me encantaría conocer vuestras opiniones! #Marketing #Tendencias2025',
        image: 'https://incae.edu/wp-content/uploads/2023/10/tendencias-de-marketing-para-2022.jpg',
        likes: 34,
        comments: 8,
        time: 'hace 3 horas'
    },
    {
        id: 2,
        user: {
            id: 2,
            name: 'Pablo Ruiz',
            profile_picture: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQLXJQDzz8-mfF6J2E_32clcMDMzKcRK1xxxQ&s',
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
        profile_picture: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRDGE8DADk8KW_A9xqtLkKQcqBmflJtJGN8Jw&s',
        headline: 'Diseñadora Gráfica',
        mutual_connections: 7
    },
    {
        id: 2,
        name: 'Daniel Torres',
        profile_picture: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTQBz5QgsYhzNkfnWhr10roi33vNXtF8gm3FQ&s',
        headline: 'Ingeniero de Software',
        mutual_connections: 5
    },
    {
        id: 3,
        name: 'Lucía Moreno',
        profile_picture: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRyPRXs4YhCJEqwOCyl9Vlt6pA4Zqfsu1aROA&s',
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

                                        <div @click="scrollToShareInput"
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

                                <div class="bg-white rounded-xl shadow-sm overflow-hidden" id="shareInput">
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
                                        <i class='bx bx-user mr-1'></i> Ver Perfil
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-center mt-8">
                            <button
                                class="px-6 py-3 bg-[#193CB8]/10 text-[#193CB8] rounded-full hover:bg-[#193CB8]/20 transition-colors font-medium cursor-pointer">
                                Descubrir más personas
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