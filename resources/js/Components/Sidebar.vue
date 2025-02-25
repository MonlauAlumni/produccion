<template>
    <div>
        <!-- Botón de menú en móviles y tablets -->
        <button @click="toggleSidebar" class="md:hidden fixed top-30 left-5 z-50 bg-gray-800 text-white p-2 rounded-md">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M4 5h16v2H4V5zm0 6h16v2H4v-2zm0 6h16v2H4v-2z" clip-rule="evenodd"></path>
            </svg>
        </button>

        <!-- Fondo oscuro al abrir el menú -->
        <div v-if="isOpen" @click="toggleSidebar" class="fixed inset-0 bg-black/60 md:hidden z-40"></div>

        <!-- Sidebar -->
        <aside :class="['h-screen w-64 p-4 flex flex-col fixed top-0 left-0 transition-transform bg-white z-50',
                        isOpen ? 'translate-x-0' : '-translate-x-full', 'md:translate-x-0 md:top-24']">
            <!-- Botón de cerrar en móviles -->
            <button @click="toggleSidebar" class="md:hidden absolute top-4 right-4 bg-red-500 text-white p-1 rounded-full">
                ✕
            </button>

            <!-- Perfil de usuario -->
            <div class="rounded-3xl p-4 flex items-center space-x-3 border-2 border-gray-300">
                <div class="w-10 h-10 bg-gray-400 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold">{{ username }}</p>
                    <p class="text-xs text-gray-600">@{{ usertag }}</p>
                </div>
            </div>

            <div class="mt-6 border-2 border-gray-300 rounded-3xl p-4 flex flex-col space-y-4">
                <!-- Menú de navegación -->
                <nav>
                    <ul class="space-y-4">
                        <li v-for="item in menuItems" :key="item.text">
                            <a href="#" class="block px-4 py-2 text-gray-700 border border-transparent hover:border-gray-300 rounded-3xl transition-all">
                                {{ item.text }}
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Botón Post -->
                <div class="mt-2 flex justify-center">
                    <button class="bg-blue-600 text-white px-6 py-2 shadow-md hover:bg-blue-800 cursor-pointer w-full rounded-3xl transition-all">
                        Post
                    </button>
                </div>
            </div>
        </aside>
    </div>
</template>

<script>
export default {
    props: {
        username: { type: String, required: true },
        usertag: { type: String, required: true }
    },
    data() {
        return {
            isOpen: false,
            menuItems: [
                { text: 'Inicio' },
                { text: 'Social' },
                { text: 'Notificaciones' },
                { text: 'Mis Ofertas' },
                { text: 'Empresas' },
                { text: 'Alumnos' },
                { text: 'Configuración' }
            ]
        };
    },
    methods: {
        toggleSidebar() {
            this.isOpen = !this.isOpen;
        }
    }
};
</script>
