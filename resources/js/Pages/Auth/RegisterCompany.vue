<template>
    <Navbar />
    <div class="max-w-2xl mx-auto mt-[5%] p-8 bg-white">
        <div>
            <h1 class="text-5xl font-semibold text-gray-800 mb-6">
                Registro de Empresa
            </h1>
            <h2 class="text-lg text-gray-500">
                Complete todos los campos para confirmar el registro y publicar
                ofertas
            </h2>
        </div>
        <div v-if="currentStep === 1" class="mb-8 mt-5">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Datos de acceso a Alumni
            </h2>
            <form @submit.prevent="nextStep">
                <div class="grid grid-cols-1 gap-2 mb-6">
                    <div class="flex flex-row gap-2">
                        <InputWithIcon
                            v-model="form.name"
                            icon="person"
                            placeholder="Introduce tu nombre"
                            id="name"
                            required
                        />
                        <InputWithIcon
                            v-model="form.last_name_1"
                            icon="person"
                            placeholder="Introduce tu primer apellido"
                            id="last_name_1"
                            required
                        />
                    </div>
                    <div class="flex flex-row gap-2">
                        <InputWithIcon
                            v-model="form.last_name_2"
                            icon="person"
                            placeholder="Introduce tu segundo apellido"
                            id="last_name_2"
                        />
                        <InputWithIcon
                            v-model="form.email"
                            icon="alternate_email"
                            placeholder="Introduce tu correo empresarial"
                            id="email"
                            type="email"
                            required
                        />
                    </div>
                    <InputWithIcon
                        v-model="form.password"
                        icon="lock"
                        placeholder="Crea una contraseña"
                        id="password"
                        type="password"
                        required
                        class="w-full"
                        toggleVisibility
                    />
                    <InputWithIcon
                        v-model="form.confirmPassword"
                        icon="lock"
                        placeholder="Confirma tu contraseña"
                        id="confirmPassword"
                        type="password"
                        required
                        class="w-full"
                        toggleVisibility
                    />
                </div>

                <!-- Botón para pasar a la siguiente sección -->
                <SubmitButton label="Siguiente" />
                <div class="flex justify-end mt-4">
                    <span>
                        ¿Ya tienes cuenta?
                        <a href="/login" class="text-blue-600">Inicia sesión</a>
                    </span>
                </div>
            </form>
        </div>

        <!-- Sección de Datos de la Empresa -->
        <div v-if="currentStep === 2" class="mb-6 mt-5">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Datos de la Empresa
            </h2>
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 gap-2 mb-6">
                    <InputWithIcon
                        v-model="form.company_name"
                        icon="domain"
                        placeholder="Nombre de la Empresa"
                        id="company_name"
                        required
                        class="w-full"
                    />
                    <div class="flex flex-row gap-4">
                        <InputWithIcon
                            v-model="form.company_phone"
                            icon="call"
                            placeholder="Teléfono de Contacto"
                            id="company_phone"
                            type="tel"
                            required
                        />
                        <InputWithIcon
                            v-model="form.fiscal_id"
                            icon="badge"
                            placeholder="Identificación Fiscal"
                            id="fiscal_id"
                            required
                        />
                    
                    </div>
                    
                    <div
                        class="flex items-start p-3 border border-gray-600 rounded-lg"
                        style="height: auto"
                    >
                        <span
                            class="material-symbols-outlined text-gray-800 mr-2 mt-1"
                        >
                            description
                        </span>
                        <textarea
                            placeholder="Descripción de la Empresa"
                            v-model="form.description"
                            class="px-2 py-1 w-full outline-none resize-none"
                            style="min-height: 100px"
                        ></textarea>
                    </div>
                </div>
                <div class="flex items-center px-1 mb-4">
                    <input
                        type="checkbox"
                        id="privacyPolicy"
                        v-model="privacyPolicy"
                        class="form-checkbox h-5 w-5 rounded-full text-blue-600"
                    />
                    <label for="privacyPolicy" class="ml-2 text-gray-900">
                        He leído y acepto la
                        <strong class="underline cursor-pointer"
                            >Política de Privacidad</strong
                        >
                    </label>
                </div>

                <SubmitButton label="Registrar" />
            </form>
        </div>
    </div>
</template>

<script>
import Navbar from "../../Components/Navbar.vue";
import SubmitButton from "../../Components/SubmitButton.vue";
import InputWithIcon from "../../Components/InputWithIcon.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

export default {
    components: {
        Navbar,
        SubmitButton,
        InputWithIcon,
    },
    setup() {
        
        const form = useForm({
            name: "",
            last_name_1: "",
            last_name_2: "",
            email: "",
            password: "",
            confirmPassword: "",
            company_name: "",
            company_phone: "",
            fiscal_id: "",
            description: "",
        });

        const privacyPolicy = ref(false);

        const currentStep = ref(1); // Para controlar la sección actual

        const nextStep = () => {
            if (validateStudentInfo()) {
                currentStep.value = 2; // Cambiar a la sección de empresa
            } else {
                alert("Por favor, complete todos los campos correctamente.");
            }
        };

        const validateStudentInfo = () => {
            return (
                form.name &&
                form.last_name_1 &&
                form.email &&
                form.password === form.confirmPassword
            );
        };

        const submitForm = () => {
            if (!privacyPolicy.value) {
                alert(
                    "Debes aceptar la política de privacidad para continuar."
                );
                return;
            }

            form.post("/register-company", {
                name: form.name,
                last_name_1: form.last_name_1,
                last_name_2: form.last_name_2,
                email: form.email,
                password: form.password,
                confirmPassword: form.confirmPassword,
                company_name: form.company_name,
                company_phone: form.company_phone,
                fiscal_id: form.fiscal_id,
                description: form.description,
            });
        };

        return {
            form,
            privacyPolicy,
            currentStep,
            nextStep,
            submitForm,
        };
    },
};
</script>
