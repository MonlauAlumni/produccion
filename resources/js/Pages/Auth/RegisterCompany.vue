<template>
    <Navbar />
    <div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg">
        <div v-if="currentStep === 1" class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Datos del Usuario
            </h2>
            <form @submit.prevent="nextStep">
                <div class="grid grid-cols-1 gap-6 mb-6">
                    <div>
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700"
                            >Nombre</label
                        >
                        <input
                            type="text"
                            id="name"
                            v-model="form.name"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg"
                            required
                        />
                    </div>

                    <div>
                        <label
                            for="last_name_1"
                            class="block text-sm font-medium text-gray-700"
                            >Primer Apellido</label
                        >
                        <input
                            type="text"
                            id="last_name_1"
                            v-model="form.last_name_1"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg"
                            required
                        />
                    </div>

                    <div>
                        <label
                            for="last_name_2"
                            class="block text-sm font-medium text-gray-700"
                            >Segundo Apellido</label
                        >
                        <input
                            type="text"
                            id="last_name_2"
                            v-model="form.last_name_2"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg"
                        />
                    </div>

                    <div>
                        <label
                            for="email"
                            class="block text-sm font-medium text-gray-700"
                            >Correo Electrónico</label
                        >
                        <input
                            type="email"
                            id="email"
                            v-model="form.email"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg"
                            required
                        />
                    </div>

                    <div>
                        <label
                            for="password"
                            class="block text-sm font-medium text-gray-700"
                            >Contraseña</label
                        >
                        <input
                            type="password"
                            id="password"
                            v-model="form.password"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg"
                            required
                        />
                    </div>

                    <div>
                        <label
                            for="confirmPassword"
                            class="block text-sm font-medium text-gray-700"
                            >Repetir Contraseña</label
                        >
                        <input
                            type="password"
                            id="confirmPassword"
                            v-model="form.confirmPassword"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg"
                            required
                        />
                    </div>
                </div>

                <!-- Botón para pasar a la siguiente sección -->
                <button
                    type="submit"
                    class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200"
                >
                    Siguiente: Datos de la Empresa
                </button>
            </form>
        </div>

        <!-- Sección de Datos de la Empresa -->
        <div v-if="currentStep === 2" class="company-info">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Datos de la Empresa
            </h2>
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 gap-6 mb-6">
                    <div>
                        <label
                            for="company_name"
                            class="block text-sm font-medium text-gray-700"
                            >Nombre de la Empresa</label
                        >
                        <input
                            type="text"
                            id="company_name"
                            v-model="companyInfo.company_name"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg"
                            required
                        />
                    </div>

                    <div>
                        <label
                            for="company_phone"
                            class="block text-sm font-medium text-gray-700"
                            >Teléfono de Contacto</label
                        >
                        <input
                            type="tel"
                            id="company_phone"
                            v-model="companyInfo.company_phone"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg"
                            required
                        />
                    </div>

                    <div>
                        <label
                            for="fiscal_id"
                            class="block text-sm font-medium text-gray-700"
                            >Identificación Fiscal</label
                        >
                        <input
                            type="text"
                            id="fiscal_id"
                            v-model="companyInfo.fiscal_id"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg"
                            required
                        />
                    </div>

                    <div>
                        <label
                            for="description"
                            class="block text-sm font-medium text-gray-700"
                            >Descripción de la Empresa</label
                        >
                        <textarea
                            id="description"
                            v-model="companyInfo.description"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg"
                        ></textarea>
                    </div>
                </div>

                <!-- Botón de registro -->
                <button
                    type="submit"
                    class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200"
                >
                    Registrar Empresa
                </button>
            </form>
        </div>
    </div>
</template>

<script>
import Navbar from "../../Components/Navbar.vue";
import { useForm } from "@inertiajs/vue3";
export default {
    components: {
        Navbar,
    },
    data() {
        return {
            currentStep: 1, // Para controlar la sección actual
            form: {
                name: "", // Cambiado de firstName a name
                last_name_1: "", // Cambiado de lastName a last_name_1
                last_name_2: "", // Cambiado de secondLastName a last_name_2
                email: "",
                password: "",
                confirmPassword: "",
            },
            companyInfo: {
                company_name: "", // Cambiado de companyName a companyName
                company_phone: "", // Cambiado de companyPhone a companyPhone
                fiscal_id: "", // Cambiado de fiscalId a fiscalId
                description: "",
            },
        };
    },
    methods: {
        nextStep() {
            if (this.validateStudentInfo()) {
                this.currentStep = 2; // Cambiar a la sección de empresa
            } else {
                alert("Por favor, complete todos los campos correctamente.");
            }
        },
        validateStudentInfo() {
            return (
                this.form.name &&
                this.form.last_name_1 &&
                this.form.email &&
                this.form.password === this.form.confirmPassword
            );
        },
        submitForm() {
            Inertia.post("/register-company", {
                name: this.form.name, // Para el nombre del usuario
                last_name_1: this.form.last_name_1,
                last_name_2: this.form.last_name_2,
                email: this.form.email,
                password: this.form.password,
                confirmPassword: this.form.confirmPassword,
                company_name: this.companyInfo.company_name, // Usamos company_name
                company_phone: this.companyInfo.company_phone,
                fiscal_id: this.companyInfo.fiscal_id,
                description: this.companyInfo.description,
            });
        },
    },
};
</script>

<style scoped>
.company-register {
    max-width: 600px;
    margin: auto;
}
</style>
