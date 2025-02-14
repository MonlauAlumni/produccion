<script setup>
import { defineProps, defineEmits, ref, watch, onMounted, onUnmounted } from 'vue';
import 'boxicons';

const props = defineProps({
    user: Object,
    sidebar: Boolean,
});

const emit = defineEmits(['update:sidebar']);

const sidebarState = ref(props.sidebar);
const overlay = ref(null);
const sidebarRef = ref(null);

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
    overlay.value.classList.add('fixed', 'inset-0', 'bg-black', 'opacity-0', 'transition-opacity', 'duration-300', 'ease-in-out', 'pointer-events-none');
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
        overlay.value.classList.add('opacity-0', 'pointer-events-none');
        overlay.value.classList.remove('opacity-60', 'pointer-events-auto');
    }
});
</script>

<template>
    <nav class="p-4 flex justify-between items-center border-b border-blue-600">
        <div class="flex gap-4 items-center">
            <a href="/admin/dashboard" class="text-blue-600 text-3xl font-bold flex items-center gap-4">
                <img src="/public/images/logo.png" alt="Alumni" class="w-17 h-16">
                Alumni
            </a>
        </div>
        <box-icon name='menu' color="#2563EB" size="md" class="hover:cursor-pointer"
            @click.stop="toggleSidebar"></box-icon>
    </nav>

    <div class="w-full">
        <transition name="slide">
            <div v-if="sidebarState"
                ref="sidebarRef"
                class="fixed top-0 right-0 h-full w-64 bg-white shadow-lg z-50 transition-transform duration-300 ease-in-out transform overflow-y-auto"
                :class="{ 'translate-x-0': sidebarState, 'translate-x-full': !sidebarState }"
                @click.stop>
                <div class="p-4 flex justify-between items-center border-b border-blue-600">
                    <div class="flex gap-4 items-center">
                        <a href="/admin/dashboard" class="text-blue-600 text-xl font-bold flex items-center gap-4">
                            <img src="/public/images/logo.png" alt="Alumni" class="w-8 h-8">
                            Alumni
                        </a>
                    </div>
                    <box-icon name='x' color="#2563EB" size="md" class="hover:cursor-pointer"
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
                </div>
            </div>
        </transition>

        <slot></slot>
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