<script setup>
import { ref, watch, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AdminLayout from './Layouts/AdminLayout.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';
import Table from '@/Components/Admin/Table.vue';
import BaseModal from '@/Components/Admin/BaseModal.vue';
import axios from 'axios';

const page = usePage();

const filters = ref({
    id: '',
    name: '',
    email: '',
    area: '',
    role: '',
    pagination: '10',
});

let timeout = null;

function searchUsers() {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get('/admin/users', filters.value, {
            preserveState: true,
            preserveScroll: true,
        });
    }, 300);
}

function changePage(pageNumber) {
    router.get('/admin/users', { ...filters.value, page: pageNumber }, {
        preserveState: true,
        preserveScroll: false,
    });
}

function clearFilters() {
    filters.value = {
        id: '',
        name: '',
        email: '',
        area: '',
        role: '',
        pagination: '10',
    };
    searchUsers();
}

function singleUser(id) {
    router.get(`/admin/user/${id}`);
}

const paginationRange = computed(() => {
    const totalPages = page.props.pagination ? page.props.pagination.last_page : 0;
    const currentPage = page.props.pagination ? page.props.pagination.current_page : 1;
    const range = [];
    const rangeWithDots = [];
    const margin = 2;

    for (let i = 1; i <= totalPages; i++) {
        if (
            i === 1 ||
            i === totalPages ||
            (i >= currentPage - margin && i <= currentPage + margin)
        ) {
            range.push(i);
        }
    }

    let l;
    for (let i of range) {
        if (l) {
            if (i - l === 2) {
                rangeWithDots.push(l + 1);
            } else if (i - l !== 1) {
                rangeWithDots.push('...');
            }
        }
        rangeWithDots.push(i);
        l = i;
    }

    return rangeWithDots;
});

// Modals
const modalDeleteUser = ref(false);
const modalBlockUser = ref(false);
const selectedUserId = ref(null);

// Computed: usuario seleccionado
const selectedUser = computed(() => {
    return page.props.users.find(u => u.id === selectedUserId.value) || {};
});

// Function to open delete modal and set the selected user
function confirmDelete(userId) {
    selectedUserId.value = userId;
    modalDeleteUser.value = true;
}

// Function to delete user with axios
function deleteUser() {
    if (!selectedUserId.value) return;
    
    console.log("test");
    axios.delete(`/admin/user/${selectedUserId.value}`)
        .then(response => {
            modalDeleteUser.value = false;
            searchUsers();
        })
        .catch(error => {
            modalDeleteUser.value = false;
        });
}

function confirmBlock(userId) {
    selectedUserId.value = userId;
    modalBlockUser.value = true;
}

function blockUser() {
    if (!selectedUserId.value) return;
    const user = page.props.users.find(u => u.id === selectedUserId.value);
    const isBlocked = user && user.status === 'blocked';
    const newStatus = isBlocked ? 'online' : 'blocked';
    router.put(`/admin/user/${selectedUserId.value}`, { status: newStatus }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            modalBlockUser.value = false;
            searchUsers();
        },
        onError: () => {
            modalBlockUser.value = false;
        }
    });
}
</script>

<template>
    <AdminLayout>
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 rounded-xl shadow-sm p-8 mb-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Gestión de Usuarios</h1>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        Módulo para la gestión de usuarios de Alumni.
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-5 pb-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filtros de búsqueda
                </h2>
                <button @click="clearFilters"
                    class="bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center border border-gray-200 dark:border-gray-600 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reiniciar Filtros
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-gray-500 ml-1">ID</label>
                    <InputWithIcon type="number" icon="id_card" placeholder="#" class="w-full" v-model="filters.id"
                        @input="searchUsers" />
                </div>
                
                <div class="space-y-1">
                    <label class="text-xs font-medium text-gray-500 ml-1">Nombre</label>
                    <InputWithIcon type="text" icon="search" placeholder="Nombre completo" class="w-full" v-model="filters.name"
                        @input="searchUsers" />
                </div>
                
                <div class="space-y-1">
                    <label class="text-xs font-medium text-gray-500 ml-1">Email</label>
                    <InputWithIcon type="email" icon="email" placeholder="Correo electrónico" class="w-full" v-model="filters.email"
                        @input="searchUsers" />
                </div>
                
                <div class="space-y-1">
                    <label class="text-xs font-medium text-gray-500 ml-1">Rol</label>
                    <div class="w-full relative">
                        <i class="bx bx-shield-quarter mr-2 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl"></i>
                        <select v-model="filters.role" @input="searchUsers"
                            class="w-full h-12 pl-10 p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-300 transition duration-200 bg-white appearance-none">
                            <option value="">Seleccionar</option>
                            <option value="1">Admin</option>
                            <option value="2">Alumno</option>
                            <option value="3">Exalumno</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-1">
                    <label class="text-xs font-medium text-gray-500 ml-1">Área formativa</label>
                    <div class="w-full relative">
                        <i class="bx bx-book mr-2 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl"></i>
                        <select v-model="filters.area" @input="searchUsers"
                            class="w-full h-12 pl-10 p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-300 transition duration-200 bg-white appearance-none">
                            <option value="">Seleccionar</option>
                            <option value="Informatica">Informática</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Automocion">Automoción</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-gray-500 ml-1">Resultados por página</label>
                    <div class="w-full relative">
                        <i class="bx bx-list-ul mr-2 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl"></i>
                        <select v-model="filters.pagination" @input="searchUsers"
                            class="w-full h-12 pl-10 p-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-300 transition duration-200 bg-white appearance-none">
                            <option value="">Seleccionar</option>
                            <option value="5">5 registros</option>
                            <option value="10">10 registros</option>
                            <option value="15">15 registros</option>
                            <option value="25">25 registros</option>
                            <option value="50">50 registros</option>
                            <option value="100">100 registros</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6 border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-5 pb-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Listado de Usuarios
                </h2>
                <div class="flex items-center">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                        Mostrando {{ (page.props.pagination?.current_page || 1 - 1) * (page.props.pagination?.per_page || 10) + 1 }} - {{
                            (page.props.pagination?.current_page || 1 - 1) * (page.props.pagination?.per_page || 10) + (page.props.users?.length || 0) }}
                        de {{ page.props.pagination?.total || 0 }} usuarios
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <Table :headers="['#', 'Nombre', 'Email', 'Área formativa','Acciones']">
                    <tr v-if="!page.props.users || page.props.users.length === 0">
                        <td class="py-12 px-4" colspan="4">
                            <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-full mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-lg font-medium text-gray-700 dark:text-gray-200">No se encontraron usuarios</p>
                                <p class="text-sm mt-1 max-w-sm text-center text-gray-500 dark:text-gray-400">No hay resultados que coincidan con los criterios de búsqueda. Intente con otros filtros o reinicie la búsqueda.</p>
                                <button @click="clearFilters" class="mt-4 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition duration-200">
                                    Reiniciar filtros
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-else v-for="user in page.props.users" :key="user.id"
                        class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors duration-150">
                        <td class="py-4 px-4">
                            <span class="font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 py-1 px-2.5 rounded-md">{{ user.id }}</span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-200 font-medium mr-2">
                                    {{ user.name?.charAt(0) || '?' }}{{ user.last_name_1?.charAt(0) || '' }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ user.name }} {{ user.last_name_1 }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ user.last_name_2 }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 dark:text-gray-400 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm truncate max-w-[150px] text-gray-700 dark:text-gray-200">{{ user.email }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-sm text-gray-700 dark:text-gray-200">{{ user.training_area || '-' }}</span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-1">
                                <button class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150" @click="singleUser(user.id)" title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900 transition-colors duration-150" @click="confirmDelete(user.id)" title="Eliminar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <button
                                    v-if="String(user.status).trim().toLowerCase() === 'blocked'"
                                    class="p-1.5 rounded-lg hover:bg-green-50 dark:hover:bg-green-900 transition-colors duration-150"
                                    @click="confirmBlock(user.id)"
                                    title="Desbloquear"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4 -4" />
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" fill="none" />
                                    </svg>
                                </button>
                                <button
                                    v-else
                                    class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150"
                                    @click="confirmBlock(user.id)"
                                    title="Bloquear"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </Table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center">
                <nav class="inline-flex items-center rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button 
                        @click="changePage(page.props.pagination?.current_page - 1)" 
                        :disabled="!page.props.pagination || page.props.pagination.current_page === 1"
                        class="px-3 py-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed border-r border-gray-200 dark:border-gray-700 transition-colors duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div class="flex">
                        <button v-for="pageNum in paginationRange" :key="pageNum"
                            @click="pageNum !== '...' ? changePage(pageNum) : null" 
                            class="px-4 py-2 text-sm font-medium transition-colors duration-150" 
                            :class="{
                                'bg-gray-800 dark:bg-gray-700 text-white': pageNum === (page.props.pagination ? page.props.pagination.current_page : null),
                                'text-gray-500 dark:text-gray-400 cursor-not-allowed': pageNum === '...',
                                'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border-r border-gray-200 dark:border-gray-700': pageNum !== '...' && pageNum !== (page.props.pagination ? page.props.pagination.current_page : null)
                            }" 
                            :disabled="pageNum === '...'">
                            {{ pageNum }}
                        </button>
                    </div>
                    <button 
                        @click="changePage(page.props.pagination?.current_page + 1)" 
                        :disabled="!page.props.pagination || page.props.pagination.current_page === page.props.pagination.last_page"
                        class="px-3 py-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Modals -->
        <BaseModal :show="modalDeleteUser" title="Eliminar Usuario"
            message="¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer."
            icon="trash-alt" iconColor="#ef4444" iconBgClass="bg-red-100 dark:bg-red-800" confirmText="Eliminar"
            confirmButtonClass="bg-red-600 text-white hover:bg-red-700" @close="modalDeleteUser = false"
            @confirm="deleteUser" />

        <BaseModal v-if="String(selectedUser.status).trim().toLowerCase() !== 'blocked'" :show="modalBlockUser" title="Bloquear Usuario"
            message="¿Estás seguro de que deseas bloquear este usuario? Podrá ser desbloqueado más tarde." icon="block"
            iconColor="#6b7280" iconBgClass="bg-gray-300 dark:bg-gray-700" confirmText="Bloquear"
            confirmButtonClass="bg-gray-600 text-white hover:bg-gray-700" @close="modalBlockUser = false"
            @confirm="blockUser" />
        <BaseModal v-else :show="modalBlockUser" title="Desbloquear Usuario"
            message="¿Estás seguro de que deseas desbloquear este usuario? Podrá ser bloqueado más tarde." icon="check"
            iconColor="#4ade80" iconBgClass="bg-green-100 dark:bg-green-800" confirmText="Desbloquear"
            confirmButtonClass="bg-green-600 text-white hover:bg-green-700" @close="modalBlockUser = false"
            @confirm="blockUser" />
    </AdminLayout>
</template>

<style scoped>
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    appearance: textfield;
    -moz-appearance: textfield;
}
</style>