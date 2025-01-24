<template>
    <Navbar />
    <div class="max-w-2xl mx-auto p-8 mt-5 bg-white">
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
                    <div class="flex flex-row gap-4">
                        <div
                            class="w-1/2 flex items-center p-1 px-3 border border-gray-600 rounded-lg"
                        >
                            <span
                                class="material-symbols-outlined text-gray-800 mr-2"
                            >
                                person
                            </span>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                placeholder="Introduce tu nombre"
                                class="p-2 w-full outline-none"
                                required
                            />
                        </div>

                        <div
                            class="w-1/2 flex items-center p-1 px-3 border border-gray-600 rounded-lg"
                        >
                            <span
                                class="material-symbols-outlined text-gray-800 mr-2"
                            >
                                person
                            </span>
                            <input
                                type="text"
                                id="last_name_1"
                                v-model="form.last_name_1"
                                placeholder="Introduce tu primer apellido"
                                class="p-2 w-full outline-none"
                                required
                            />
                        </div>
                    </div>
                    <div class="flex flex-row gap-4">
                        <div
                            class="w-1/2 flex items-center p-1 px-3 border border-gray-600 rounded-lg"
                        >
                            <span
                                class="material-symbols-outlined text-gray-800 mr-2"
                            >
                                person
                            </span>
                            <input
                                type="text"
                                id="last_name_2"
                                v-model="form.last_name_2"
                                placeholder="Introduce tu segundo apellido"
                                class="p-2 w-full outline-none"
                            />
                        </div>

                        <div
                            class="w-1/2 flex items-center p-1 px-3 border border-gray-600 rounded-lg"
                        >
                            <span
                                class="material-symbols-outlined text-gray-800 mr-2"
                            >
                                <span class="material-symbols-outlined">
                                    alternate_email
                                </span>
                            </span>
                            <input
                                type="email"
                                id="email"
                                v-model="form.email"
                                placeholder="Introduce tu correo electrónico"
                                class="p-2 w-full outline-none"
                                required
                            />
                        </div>
                    </div>
                    <div
                        class="flex items-center p-1 px-3 border border-gray-600 rounded-lg"
                    >
                        <span
                            class="material-symbols-outlined text-gray-800 mr-2"
                        >
                            lock
                        </span>
                        <input
                            type="password"
                            id="password"
                            v-model="form.password"
                            placeholder="Crea una contraseña"
                            class="p-2 w-full outline-none"
                            required
                        />
                    </div>

                    <div
                        class="flex items-center p-1 px-3 border border-gray-600 rounded-lg"
                    >
                        <span
                            class="material-symbols-outlined text-gray-800 mr-2"
                        >
                            lock
                        </span>
                        <input
                            type="password"
                            id="confirmPassword"
                            v-model="form.confirmPassword"
                            placeholder="Confirma tu contraseña"
                            class="p-2 w-full outline-none"
                            required
                        />
                    </div>
                </div>

                <!-- Botón para pasar a la siguiente sección -->
                <button
                    type="submit"
                    class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200"
                >
                    Siguiente
                </button>
            </form>
        </div>

        <!-- Sección de Datos de la Empresa -->
        <div v-if="currentStep === 2" class="mb-6 mt-5">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Datos de la Empresa
            </h2>
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 gap-2 mb-6">
                    <div class="flex flex-row gap-4">
                        <div
                            class="w-full flex items-center p-1 px-3 border border-gray-600 rounded-lg"
                        >
                            <span
                                class="material-symbols-outlined text-gray-800 mr-2"
                            >
                                domain
                            </span>
                            <input
                                type="text"
                                id="company_name"
                                placeholder="Nombre de la Empresa"
                                v-model="form.company_name"
                                class="p-2 w-full outline-none"
                                required
                            />
                        </div>
                    </div>
                    <div class="flex flex-row gap-4">
                        <div
                            class="w-1/2 flex items-center p-1 px-3 border border-gray-600 rounded-lg"
                        >
                            <span
                                class="material-symbols-outlined text-gray-800 mr-2"
                            >
                                call
                            </span>
                            <input
                                type="tel"
                                placeholder="Teléfono de Contacto"
                                v-model="form.company_phone"
                                class="p-2 w-full outline-none"
                                required
                            />
                        </div>
                        <div
                            class="w-1/2 flex items-center p-1 px-3 border border-gray-600 rounded-lg"
                        >
                            <span
                                class="material-symbols-outlined text-gray-800 mr-2"
                            >
                                badge
                            </span>
                            <input
                                type="text"
                                placeholder="Identificación Fiscal"
                                v-model="form.fiscal_id"
                                class="p-2 w-full outline-none"
                                required
                            />
                        </div>
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
                <button
                    type="submit"
                    class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200"
                >
                    Registrar
                </button>
            </form>
        </div>
    </div>
</template>

<script>
import Navbar from "../../Components/Navbar.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

export default {
    components: {
        Navbar,
    },
    setup() {
        // Inicializa el formulario usando useForm
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
