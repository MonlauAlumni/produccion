<template>
    <Navbar />
    <div class="container mx-auto w-1/2 mt-8">
        <h2 class="text-5xl">Iniciar sesión</h2>
        <p class="mt-4 text-lg text-gray-500">Ingrese sus credenciales para iniciar sesión</p>
        <form @submit.prevent="login">

            <!-- Correo -->
            <div class="flex gap-2 border-2 border-black p-2 rounded-3xl my-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 1 0-2.636 6.364M16.5 12V8.25" />
                </svg>
                <input type="email" v-model="form.email" placeholder="Dirección de correo" class="flex-grow w-full border-none outline-none bg-transparent placeholder-gray-400" required />
            </div>

            <!-- Contraseña -->
            <div class="flex gap-2 border-2 border-black p-2 rounded-3xl my-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                <input type="password" v-model="form.password" placeholder="Password" class="flex-grow w-full border-none outline-none bg-transparent placeholder-gray-400" required />
            </div>

            <!-- Botón login -->
            <button type="submit" :disabled="form.processing"
                class="w-full rounded-3xl px-5 py-2 bg-blue-800 text-white text-lg font-semibold hover:bg-blue-600 transition my-2">
                Iniciar sesión
            </button>

            <!-- Mostrar errores -->
            <div v-if="error" class="mt-4 text-red-500">
                {{ error }}
            </div>
        </form>
    </div>
</template>

<script>
import Navbar from '@/Components/Navbar.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
export default {
    components: {
        Navbar,
    },
    setup() {
        const form = useForm({
            email: '',
            password: '',
        });
        const error = ref('');
        function login() {
            form.post('/login', {
                onError: (errors) => {
                    error.value = errors.email || errors.password || 'Login failed';
                }
            });
        }

        return {
            form,
            error,
            login,
        };
    },
};
</script>
