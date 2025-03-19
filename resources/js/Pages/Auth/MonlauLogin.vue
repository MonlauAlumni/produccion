<template>
    <div class="bg-gray-200 h-full min-h-screen flex items-center justify-center">
        <div class="bg-white w-full max-w-md shadow-xl rounded-lg p-8 mx-4 sm:mx-0">
            <div>
                <div class="flex justify-center">
                    <img src="../../../../public/images/logoMonlau.jpg" class="w-40" />
                </div>
                <h1 class="text-2xl font-semibold text-gray-800 mb-6 mt-4 text-center">
                    Acceso para miembros de Monlau </h1>
                    <div class="flex justify-center max-w-45 mx-auto">
                        <OAuthButton :image="false" :provider="'microsoft'" text="INICIAR SESIÓN" class="text-white bg-linear-65 from-blue-600 to-green-600"/>
                    </div>

                <h2 class="text-md text-gray-500 text-center mt-2">
                    Si no eres miembro, puedes visitar nuestra <a href="https://www.monlau.com/"
                        class="text-red-700 underline">web</a> </h2>
            </div>
        </div>
    </div>
</template>

<script>
import Navbar from '@/Components/Navbar.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputWithIcon from '../../Components/InputWithIcon.vue';
import SubmitButton from '../../Components/SubmitButton.vue';
import OAuthButton from '../../Components/OAuthButton.vue';

export default {
    components: {
        SubmitButton,
        InputWithIcon,
        OAuthButton,
        Navbar,


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
