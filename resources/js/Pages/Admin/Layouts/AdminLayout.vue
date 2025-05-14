<script setup>
import { defineProps, defineEmits, ref, watch, onMounted, onUnmounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';


import 'boxicons';

const props = defineProps({
    user: Object,
    sidebar: Boolean,
    title: String,
});

const emit = defineEmits(['update:sidebar']);
const userMenuOpen = ref(false);

const toggleUserMenu = () => {
    userMenuOpen.value = !userMenuOpen.value;
};

const sidebarState = ref(props.sidebar);
const overlay = ref(null);
const sidebarRef = ref(null);
const headerRef = ref(null);
const isScrolled = ref(false);

watch(() => props.sidebar, (newValue) => {
    sidebarState.value = newValue;
});

function toggleSidebar() {
    sidebarState.value = !sidebarState.value;
    emit('update:sidebar', sidebarState.value);
}

function closeSidebar(event) {
    if (sidebarRef.value && !sidebarRef.value.contains(event.target)) {
        sidebarState.value = false;
        emit('update:sidebar', false);
    }
}

watch(sidebarState, (newValue) => {
    if (newValue) {
        document.body.classList.add('overflow-hidden');
        document.addEventListener('click', closeSidebar);
    } else {
        document.body.classList.remove('overflow-hidden');
        document.removeEventListener('click', closeSidebar);
    }
});

onMounted(() => {
    overlay.value = document.createElement('div');
    overlay.value.classList.add('fixed', 'inset-0', 'bg-black', 'opacity-0', 'transition-opacity', 'duration-300', 'ease-in-out', 'pointer-events-none', 'z-49');
    document.body.appendChild(overlay.value);

});

onUnmounted(() => {
    if (overlay.value && overlay.value.parentNode) {
        overlay.value.parentNode.removeChild(overlay.value);
    }
    document.removeEventListener('click', closeSidebar);
});

watch(sidebarState, (newValue) => {
    if (newValue) {
        overlay.value.classList.add('opacity-60', 'pointer-events-auto');
        overlay.value.classList.remove('opacity-0', 'pointer-events-none');
    } else {
        overlay.value.classList.remove('opacity-60', 'pointer-events-auto');
        overlay.value.classList.add('opacity-0', 'pointer-events-none');
    }
});

const { auth, appName } = usePage().props;

</script>

<template>
    <nav class="bg-gradient-to-r from-[#193CB8] to-[#2748c6] py-3 px-4 ">
        <div class="container flex  justify-between items-center">
            <!-- Logo y nombre -->
            <div class="flex items-center">
                <a href="/" class="text-white font-bold flex items-center gap-3">

                    <img src="../../../../../public/images/logoMonlau.jpg" alt="Alumni" class="w-8 h-8">

                    <span class="text-xl">Alumni</span>
                </a>
            </div>
            <div class="flex items-center  gap-4">
                <ul>
                    <li v-if="auth?.user" class="relative">
                        <button @click="toggleUserMenu" class="flex items-center gap-2 text-white">
                            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                <i class='bx bxs-user text-white'></i>
                            </div>
                            <span class="hidden sm:inline">{{ auth.user.name }}</span>
                            <i class='bx bx-chevron-down'></i>
                        </button>

                        <div v-if="userMenuOpen"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="/profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class='bx bx-user mr-2'></i> Mi Perfil
                            </a>
                            <a href="/dashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class='bx bx-grid-alt mr-2'></i> Dashboard
                            </a>
                            <a href="/configuracion" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class='bx bx-cog mr-2'></i> Configuración
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="/logout" class="block px-4 py-2 text-red-600 hover:bg-gray-100">
                                <i class='bx bx-log-out mr-2'></i> Cerrar Sesión
                            </a>
                        </div>
                    </li>
                </ul>
                <!-- Menú central - visible solo en pantallas medianas y grandes -->
          <button class="cursor-pointer text-white" @click.stop="toggleSidebar">
       <i class='bx  bx-menu mt-1 text-lg'></i>
          </button>
            </div>

        </div>
    </nav>

    <div class="w-full">
        <transition name="slide">
            <div v-if="sidebarState" ref="sidebarRef"
                class="fixed top-0 right-0 min-h-full bg-white shadow-2xl z-50 transition-transform duration-300 ease-in-out transform overflow-y-auto w-full sm:w-64 flex flex-col"
                :class="{ 'translate-x-0': sidebarState, 'translate-x-full': !sidebarState }" @click.stop>
                <div
                    class="p-4 flex justify-between items-center border-b bg-gradient-to-r from-[#193CB8] to-[#2748c6]">
                    <div class="flex gap-4 items-center">
                        <a href="/admin/dashboard" class="text-white text-xl font-bold flex items-center gap-4">
                            <img src="/public/images/logo.png" alt="Alumni" class="w-8 h-8">
                            Administrador
                        </a>
                    </div>
                    <box-icon name='x' color="#fff" size="md" class="hover:cursor-pointer"
                        @click.stop="toggleSidebar"></box-icon>
                </div>
                <div class="p-4">
                    <h3 class="text-gray-600 text-lg font-semibold">Panel de control</h3>
                    <ul class="space-y-3 mt-3">
                        <li>
                            <a href="/admin/dashboard"
                                class="flex items-center gap-4 text-gray-600 hover:text-blue-600 transition duration-300">
                                <box-icon name='grid-alt' color="#2563EB" size="md"></box-icon>
                                Inicio
                            </a>
                        </li>
                        <li>
                            <a href="/admin/users"
                                class="flex items-center gap-4 text-gray-600 hover:text-blue-600 transition duration-300">
                                <box-icon name='user' color="#2563EB" size="md"></box-icon>
                                Usuarios
                            </a>
                        </li>
                        <li>
                            <a href="/admin/companies"
                                class="flex items-center gap-4 text-gray-600 hover:text-blue-600 transition duration-300">
                                <box-icon name='briefcase' color="#2563EB" size="md"></box-icon>
                                Empresas
                            </a>
                        </li>
                        <li>
                            <a href="/admin/roles"
                                class="flex items-center gap-4 text-gray-600 hover:text-blue-600 transition duration-300">
                                <box-icon name='shield' color="#2563EB" size="md"></box-icon>
                                Roles
                            </a>
                        </li>
                        <li>
                            <a href="/admin/permissions"
                                class="flex items-center gap-4 text-gray-600 hover:text-blue-600 transition duration-300">
                                <box-icon name='key' color="#2563EB" size="md"></box-icon>
                                Permisos
                            </a>
                        </li>
                        <li>
                            <a href="/admin/activities"
                                class="flex items-center gap-4 text-gray-600 hover:text-blue-600 transition duration-300">
                                <box-icon name='calendar' color="#2563EB" size="md"></box-icon>
                                Actividades
                            </a>
                        </li>
                        <li>
                            <a href="/admin/notifications"
                                class="flex items-center gap-4 text-gray-600 hover:text-blue-600 transition duration-300">
                                <box-icon name='bell' color="#2563EB" size="md"></box-icon>
                                Notificaciones
                            </a>
                        </li>



                    </ul>

                    <div class="mt-6 cursor-pointer align-self-end">
                        <a @click="router.get('/home')"
                            class="flex items-center justify-center gap-2 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition duration-300">
                            <box-icon name='left-arrow-alt' color="white" size="md"></box-icon>
                            Volver a Alumni
                        </a>
                    </div>
                </div>
            </div>
        </transition>
        <div class="p-4 min-h-screen bg-gray-50">
            <h1 class="text-2xl text-blue-600 font-bold">{{ props.title }}</h1>
            <slot></slot>
        </div>
    </div>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
    transform: translateX(100%);
}

.slide-enter-to,
.slide-leave-from {
    transform: translateX(0);
}
</style>