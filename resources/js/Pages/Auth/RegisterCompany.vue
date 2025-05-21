<template>
  <Navbar />

  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-2xl p-8 bg-white rounded-2xl shadow-xl mb-20">
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">
          Registro de Empresa
        </h1>
        <p class="text-gray-500">
          Complete todos los campos para confirmar el registro y publicar
          ofertas en <strong class="text-blue-600">Alumni</strong>
        </p>
      </div>

      <!-- Paso 1: Datos de acceso -->
      <form v-if="currentStep === 1" @submit.prevent="nextStep" class="space-y-4">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">
          Datos de acceso
        </h2>

        <div class="grid grid-cols-1 gap-2">
          <div class="flex flex-row gap-2">
            <InputWithIcon v-model="form.name" icon="person" placeholder="Nombre" id="name" required />
            <InputWithIcon v-model="form.last_name_1" icon="person" placeholder="Primer apellido" id="last_name_1" required />
          </div>
          <div class="flex flex-row gap-2">
            <InputWithIcon v-model="form.last_name_2" icon="person" placeholder="Segundo apellido" id="last_name_2" />
            <InputWithIcon v-model="form.email" icon="alternate_email" placeholder="Correo empresarial" id="email" type="email" required />
          </div>
          <InputWithIcon v-model="form.password" icon="lock" placeholder="Contraseña" id="password" type="password" required toggleVisibility class="w-full" />
          <InputWithIcon v-model="form.confirmPassword" icon="lock" placeholder="Confirmar contraseña" id="confirmPassword" type="password" required toggleVisibility class="w-full" />
        </div>

        <div class="pt-2">
          <SubmitButton label="Siguiente" class="w-full" />
        </div>

        <div class="text-right mt-4">
          <span>¿Ya tienes cuenta? <a href="/login" class="text-blue-600">Inicia sesión</a></span>
        </div>
      </form>

      <!-- Paso 2: Datos de la empresa -->
      <form v-if="currentStep === 2" @submit.prevent="submitForm" class="space-y-4">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Datos de la Empresa</h2>

        <div class="grid grid-cols-1 gap-2">
          <InputWithIcon v-model="form.company_name" icon="domain" placeholder="Nombre de la Empresa" id="company_name" required class="w-full"/>
          <div class="flex flex-row gap-2">
            <InputWithIcon v-model="form.company_phone" icon="call" placeholder="Teléfono de Contacto" id="company_phone" type="tel" required />
            <InputWithIcon v-model="form.fiscal_id" icon="badge" placeholder="Identificación Fiscal" id="fiscal_id" required />
          </div>
          <div class="flex items-start p-3 border border-gray-300 rounded-lg">
            <span class="material-symbols-outlined text-gray-800 mr-2 mt-1">description</span>
            <textarea
              placeholder="Descripción de la Empresa"
              v-model="form.description"
              class="px-2 py-1 w-full outline-none resize-none"
              style="min-height: 100px"
            ></textarea>
          </div>
        </div>

        <!-- Política de privacidad -->
        <div class="flex items-center mb-4">
          <input type="checkbox" id="privacyPolicy" v-model="privacyPolicy" class="form-checkbox h-5 w-5 text-blue-600" />
          <label for="privacyPolicy" class="ml-2 text-gray-700">
            He leído y acepto la <strong class="underline cursor-pointer">Política de Privacidad</strong>
          </label>
        </div>

        <div class="flex justify-between gap-4">
          <button type="button" @click="goBack" class="px-4 cursor-pointer py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
            Volver
          </button>
          <SubmitButton label="Registrar" />
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Navbar from '@/Components/Navbar.vue'
import InputWithIcon from '@/Components/InputWithIcon.vue'
import SubmitButton from '@/Components/SubmitButton.vue'

const form = useForm({
  name: '',
  last_name_1: '',
  last_name_2: '',
  email: '',
  password: '',
  confirmPassword: '',
  company_name: '',
  company_phone: '',
  fiscal_id: '',
  description: ''
})

const currentStep = ref(1)
const privacyPolicy = ref(false)
const error = ref(null)
const goBack = () => {
      currentStep.value = 1;
    };
const nextStep = () => {
  if (
    form.name &&
    form.last_name_1 &&
    form.email &&
    form.password &&
    form.password === form.confirmPassword
  ) {
    error.value = null
    currentStep.value = 2
  } else {
    error.value = 'Verifica que todos los campos estén completos y las contraseñas coincidan.'
  }
}

const submitForm = () => {
  if (!privacyPolicy.value) {
    error.value = 'Debes aceptar la política de privacidad para continuar.'
    return
  }

  error.value = null

  form.post('/register-company', {
    onError: (errors) => {
      error.value = 'Revisa los campos e intenta nuevamente.'
    }
  })
}
</script>

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
