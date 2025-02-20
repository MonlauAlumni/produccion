<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from './Layouts/AdminLayout.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';

import { defineProps } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const formUser = ref({
    name: props.user.name,
    last_name_1: props.user.last_name_1,
    last_name_2: props.user.last_name_2,
    email: props.user.email,
    training_area: props.user.training_area
});

function updateUser(event) {
    event.preventDefault();
    router.put(`/admin/user/${props.user.id}`, formUser.value);
}
</script>
<template>
    <AdminLayout title="Editar usuario">
        <div class="h-full p-6">
            <form @submit="updateUser" class="bg-white p-6 rounded shadow space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-1" for="name">Nombre</label>
                        <InputWithIcon
                            v-model="formUser.name"
                            type="text"
                            icon="person"
                            placeholder="Nombre"
                            class="w-full"
                        />
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1" for="last_name_1">Apellido Paterno</label>
                        <InputWithIcon
                            v-model="formUser.last_name_1"
                            type="text"
                            icon="badge"
                            placeholder="Apellido Paterno"
                            class="w-full"
                        />
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1" for="last_name_2">Apellido Materno</label>
                        <InputWithIcon
                            v-model="formUser.last_name_2"
                            type="text"
                            icon="badge"
                            placeholder="Apellido Materno"
                            class="w-full"
                        />
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1" for="email">Email</label>
                        <InputWithIcon
                            v-model="formUser.email"
                            type="email"
                            icon="email"
                            placeholder="Email"
                            class="w-full"
                        />
                    </div>
                    <div class="md:col-span-2 relative">
                        <label class="block text-gray-700 font-bold mb-1" for="training_area">Área Formativa</label>
                        <i class="bx bx-book absolute left-3 top-10 text-gray-800 text-xl"></i>
                        <select
                            id="training_area"
                            v-model="formUser.training_area"
                            class="w-full h-12 pl-10 p-2 border border-gray-600 rounded-lg outline-none"
                        >
                            <option value="">Área formativa</option>
                            <option value="Informatica">Informática</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Automocion">Automoción</option>
                        </select>
                    </div>
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
<style scoped>

</style>