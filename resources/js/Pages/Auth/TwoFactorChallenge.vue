<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({ code: '' });
const codeParts = ref(Array(6).fill(''));

function updateCode(index, event) {
    if (event.inputType === 'deleteContentBackward' && index > 0) {
        document.getElementById(`code-${index - 1}`).focus();
    }

    if (/^\d$/.test(event.data)) {
        codeParts.value[index] = event.data;
        if (index < 5) {
            document.getElementById(`code-${index + 1}`).focus();
        }
    }

    form.code = codeParts.value.join('');
}

function submit() {
    form.post('/two-factor-challenge');
}
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-100">
        <form @submit.prevent="submit" class="bg-white  rounded-lg shadow-xl w-96">
            <h1 class="text-xl font-semibold bg-blue-600 text-white p-2 rounded-t-lg">Un paso más...</h1>

            <div class="px-6 py-4">
                <p class="text-gray-700 mb-4">Introduce el código de 6 dígitos de tu aplicación de autenticación para
                    continuar.</p>

                <div class="flex justify-between space-x-2">
                    <input v-for="(digit, index) in codeParts" :key="index" :id="'code-' + index"
                        v-model="codeParts[index]" @input="updateCode(index, $event)" type="text"
                        class="w-10 h-12 text-center text-xl bg-gray-200 text-black border border-gray-600 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        maxlength="1" />
                </div>

                <button type="submit"
                    class="mt-4 bg-blue-600 text-white p-2 rounded w-full hover:bg-blue-700 transition duration-300 cursor-pointer">
                    Verificar
                </button>

                <p v-if="form.errors.code" class="text-red-500 mt-2">{{ form.errors.code }}</p>
            </div>
        </form>
    </div>
</template>