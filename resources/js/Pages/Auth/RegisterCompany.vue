<template>
    <Navbar />
    <div class="max-w-2xl mx-auto p-6 ">
        <div>
            <h1 class="text-4xl font-semibold text-gray-800 mb-4">
                Registro de Empresa
            </h1>
            <p class="text-gray-600">
                Complete todos los campos para confirmar el registro y publicar ofertas
            </p>
        </div>
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
                            placeholder="Introduce tu nombre"
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
                            placeholder="Introduce tu apellido"
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
                            placeholder="Introduce tu segundo apellido"
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
                            placeholder="Introduce tu correo electrónico"
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
                            placeholder="Crea una contraseña"
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
                            placeholder="Repite la contraseña"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg"
                            required
                        />
                    </div>
                </div>

            
                <button
                    type="submit"
                    class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200"
                >
                    Siguiente
                </button>
            </form>
        </div>

       
        <div v-if="currentStep === 2" class="company-info">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Datos de la Empresa
            </h2>
            <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 gap-4 mb-6">
                    <div>
                        
                        <input
                            type="text"
                            id="company_name"
                            v-model="companyInfo.company_name"
                            placeholder="Introduce el nombre de la empresa"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg"
                            required
                        />
                    </div>
                    <div class="flex flex-row w-full justify-between gap-x-3">
                    <div class="w-1/2">
                       
                        <input
                            type="tel"
                            id="company_phone"
                            v-model="companyInfo.company_phone"
                            placeholder="Introduce el teléfono de contacto"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg"
                            required
                        />
                 
                    </div>
                    <div class="w-1/2">
            
                        <input
                            type="text"
                            id="fiscal_id"
                            v-model="companyInfo.fiscal_id"
                            placeholder="Introduce la identificación fiscal"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg"
                            required
                        />
                    
                    </div>
                </div>
                    <div>
                       
                        <textarea
                            id="description"
                            v-model="companyInfo.description"
                            placeholder="Introduce una descripción de la empresa"
                            class="mt-2 p-3 h-full w-full border border-gray-300 rounded-lg"
                        ></textarea>
                    </div>
                </div>

                <!-- Botón de registro -->
                <button
                    type="submit"
                    class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200"
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
