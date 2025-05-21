<script>
import Navbar from '@/Components/Navbar.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import InputWithIcon from '../../Components/InputWithIcon.vue';
import SubmitButton from '../../Components/SubmitButton.vue';
import OAuthButton from '../../Components/OAuthButton.vue';

const loginButton = ref(null);

export default {
    components: {
        SubmitButton,
        InputWithIcon,
        OAuthButton,
        Navbar,


    },
    onMounted() {
        if (loginButton.value) {
            loginButton.value.$el.focus();
        }
    },
    setup() {
        const form = useForm({
            email: '',
            password: '',
        });
        const error = ref('');
        const showPassword = ref(false);

        function login() {
            form.post('/login', {
                onError: (errors) => {
                    if (errors === 'The provided credentials do not match our records.') {
                        error.value = 'Correo o contraseña incorrectos';
                    } else {
                        error.value = `Ha ocurrido un error inesperado.`;

                    }
                }
            });
        }

        function togglePasswordVisibility() {
            showPassword.value = !showPassword.value;
        }

        return {
            form,
            error,
            login,
            showPassword,
            togglePasswordVisibility,
        };
    },
};
</script>
<template>
    <Navbar />

    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-md p-8 bg-white rounded-2xl mb-20 shadow-xl">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">Iniciar sesión</h1>
                <p class="text-gray-500">Ingrese sus credenciales para acceder a <strong class="text-blue-600">Alumni</strong></p>
            </div>

            <form @submit.prevent="login" class="space-y-4">
                <InputWithIcon v-model="form.email" icon="alternate_email" placeholder="Correo electrónico" id="email"
                    type="email" required class="w-full" />

                <InputWithIcon v-model="form.password" icon="lock" placeholder="Contraseña" id="password"
                    type="password" required toggleVisibility class="w-full" />

                <div class="pt-2">
                    <SubmitButton ref="loginButton" type="submit" label="Iniciar sesión" class="w-full" />
                </div>

                <!-- Mensaje de error -->
                <!-- Mensaje de error moderno -->
                <transition name="fade">
                    <div v-if="error"
                        class="mt-4 flex items-start gap-2 text-sm text-red-700 bg-red-100 border border-red-300 rounded-lg p-3 animate-fade-in">
                        <span class="material-symbols-outlined">
                            error
                        </span>
                        <p class="mt-0.5">{{ error }}</p>
                    </div>
                </transition>

            </form>


        </div>
    </div>
</template>
<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>