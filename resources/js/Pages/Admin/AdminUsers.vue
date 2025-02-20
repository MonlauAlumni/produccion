<script setup>
import { ref, watch, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AdminLayout from './Layouts/AdminLayout.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';
import Table from '@/Components/Admin/Table.vue';
import BaseModal from '@/Components/Admin/BaseModal.vue';

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
    const totalPages = page.props.pagination.last_page;
    const currentPage = page.props.pagination.current_page;
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

//modals
const modalDeleteUser = ref(false);
const modalBlockUser = ref(false);

</script>

<template>
    <AdminLayout title="Gestión de usuarios">
        <p class="mt-4">
            Módulo para la gestión de usuarios de Alumni.
        </p>

        <h2 class="mt-4 text-blue-600 text-xl font-bold">Filtros</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 w-full gap-2 mt-2">
            <InputWithIcon type="number" icon="id_card" placeholder="#" class="w-full" v-model="filters.id"
                @input="searchUsers" />
            <InputWithIcon type="text" icon="search" placeholder="Nombre" class="w-full" v-model="filters.name"
                @input="searchUsers" />
            <InputWithIcon type="email" icon="email" placeholder="Email" class="w-full" v-model="filters.email"
                @input="searchUsers" />

            <div class="w-full relative">
                <i
                    class="bx bx-shield-quarter mr-2 absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-900 text-xl"></i>
                <select v-model="filters.role" @input="searchUsers"
                    class="w-full h-12 pl-10 p-2 border border-gray-600 rounded-lg outline-none">
                    <option value="">Rol</option>
                    <option value="1">Admin</option>
                    <option value="2">Alumno</option>
                    <option value="3">Exalumno</option>
                </select>
            </div>

            <div class="w-full relative">
                <i class="bx bx-book mr-2 absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-900 text-xl"></i>

                <select v-model="filters.area" @input="searchUsers"
                    class="w-full h-12 pl-10 p-2 border border-gray-600 rounded-lg outline-none">
                    <option value="">Área formativa</option>
                    <option value="Informatica">Informática</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Automocion">Automoción</option>
                </select>
            </div>
            <div class="w-full relative">
                <i
                    class="bx bx-list-ul mr-2 absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-900 text-xl"></i>
                <select v-model="filters.pagination" @input="searchUsers"
                    class="w-full h-12 pl-10 p-2 border border-gray-600 rounded-lg outline-none">
                    <option value="">Por página</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <button @click="clearFilters"
                class="w-full bg-blue-600 hover:bg-blue-800 font-bold py-2 px-4 rounded-lg text-white transition duration-300 cursor-pointer h-12">
                Reiniciar Filtros
            </button>
        </div>

        <span class="text-gray-400 mt-2 block text-right">
            Mostrando {{ (page.props.pagination.current_page - 1) * page.props.pagination.per_page + 1 }} - {{
                (page.props.pagination.current_page - 1) * page.props.pagination.per_page + page.props.users.length }} de {{
                page.props.pagination.total }} usuarios
        </span>

        <Table :headers="['#', 'Nombre', 'Email', 'Acciones']">
            <tr v-if="$page.props.users.length === 0">
                <td class="py-2 px-4" colspan="5">
                    <div class="flex items-center space-x-2 justify-center text-gray-500">
                        <box-icon name="error-circle" color="#6b7280"></box-icon>
                        <p>No se encontraron usuarios</p>
                    </div>
                </td>
            </tr>
            <tr v-else v-for="user in $page.props.users" :key="user.id"
                class="border-b border-gray-300 hover:bg-gray-200 transition duration-300">
                <td class="py-2 px-4 text-blue-600 font-bold">{{ user.id }}</td>
                <td class="py-2 px-4">{{ user.name }} {{ user.last_name_1 }} {{ user.last_name_2 ?? '' }}</td>
                <td class="py-2 px-4">{{ user.email }}</td>
                <td class="py-2 px-4 flex items-center space-x-2">
                    <button class="text-blue-600 hover:text-blue-900 cursor-pointer" @click="singleUser(user.id)">
                        <box-icon name='edit' color='#3b82f6' size='sm'></box-icon>
                    </button>
                    <button class="text-red-600 hover:text-red-900 cursor-pointer" @click="modalDeleteUser = true">
                        <box-icon name='trash-alt' color='#ef4444' size='sm'></box-icon>
                    </button>
                    <button class="text-gray-600 hover:text-gray-900 cursor-pointer" @click="modalBlockUser = true">
                        <box-icon name='block' color='#6b7280' size='sm'></box-icon>
                    </button>
                </td>
            </tr>
        </Table>

        <div class="flex flex-wrap mt-4 gap-2 max-w-fit mx-auto">
            <button v-for="pageNum in paginationRange" :key="pageNum"
                @click="pageNum !== '...' ? changePage(pageNum) : null" class="px-3 py-1 border rounded" :class="{
                    'bg-blue-600 text-white': pageNum === page.props.pagination.current_page,
                    'cursor-not-allowed': pageNum === '...',
                    'hover:bg-blue-600 hover:text-white transition duration-300 cursor-pointer': pageNum !== '...'
                }" :disabled="pageNum === '...'">
                {{ pageNum }}
            </button>
        </div>

        <BaseModal :show="modalDeleteUser" title="Eliminar Usuario"
            message="¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer."
            icon="trash-alt" iconColor="#ef4444" iconBgClass="bg-red-100" confirmText="Eliminar"
            confirmButtonClass="bg-red-600 text-white hover:bg-red-700" @close="modalDeleteUser = false"
            @confirm="deleteUser" />

        <BaseModal :show="modalBlockUser" title="Bloquear Usuario"
            message="¿Estás seguro de que deseas bloquear este usuario? Podrá ser desbloqueado más tarde." icon="block"
            iconColor="#6b7280" iconBgClass="bg-gray-300" confirmText="Bloquear"
            confirmButtonClass="bg-gray-600 text-white hover:bg-gray-700" @close="modalBlockUser = false"
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
    -moz-appearance: textfield;
}
</style>
