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
                        error.value = errors.email || errors.password || 'Login failed';
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
    <div class="max-w-2xl mx-auto mt-[5%] p-8 ">
        <div>
            <h1 class="text-5xl font-semibold text-gray-800 mb-6">
                Iniciar Sesión
            </h1>
            <h2 class="text-lg text-gray-500">
                Ingrese sus credenciales para entrar a Alumni
            </h2>
        </div>
        <form @submit.prevent="login">

            <div class="flex flex-col gap-y-3 mb-4 mt-4">
                <InputWithIcon v-model="form.email" icon="alternate_email" placeholder="Dirección de correo" id="email"
                    type="email" class="w-full" required />
                <InputWithIcon v-model="form.password" icon="lock" placeholder="Contraseña" id="password" class="w-full"
                    type="password" required toggleVisibility />
            </div>
          

            <!-- Botón login -->
            <SubmitButton ref="loginButton" type="submit" class="mb-3" label="Iniciar sesión" />
            <div class="flex justify-between gap-x-2">
                <OAuthButton provider="github" text="GitHub" />
                <OAuthButton provider="google" text="Google" />
                <OAuthButton provider="microsoft" text="Microsoft" />
            </div>

            <!-- Mostrar errores -->
            <div v-if="error" class="mt-4 text-red-500">
                {{ error }}
            </div>
        </form>
    </div>
</template>