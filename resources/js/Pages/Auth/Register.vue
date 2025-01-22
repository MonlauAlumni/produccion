<template>
  <Navbar />
  <div class="container mx-auto w-1/2 mt-8">
    <h2 class="text-5xl">Registro de cuenta</h2>
    <p class="mt-4 text-lg text-gray-500">Complete todos los campos para confirmar el registro</p>
    <form @submit.prevent="submit">

      <!-- Nombre -->
      <div class="flex gap-2 border-2 border-black p-2 rounded-3xl my-3">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-500">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
        </svg>
        <input type="text" v-model="form.first_name" placeholder="Introduce tu nombre" class="flex-grow w-full border-none outline-none bg-transparent placeholder-gray-400" required />
      </div>

      <!-- Primer apellido -->
      <div class="flex gap-2 border-2 border-black p-2 rounded-3xl my-3">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-500">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
        </svg>
        <input type="text" v-model="form.last_name_1" placeholder="Introduce tu primer apellido" class="flex-grow w-full border-none outline-none bg-transparent placeholder-gray-400" required />
      </div>

      <!-- Segundo apellido -->
      <div class="flex gap-2 border-2 border-black p-2 rounded-3xl my-3">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-500">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
        </svg>
        <input type="text" v-model="form.last_name_2" placeholder="Introduce tu segundo apellido" class="flex-grow w-full border-none outline-none bg-transparent placeholder-gray-400" />
      </div>

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

      <!-- Confirmación de contraseña -->
      <div class="flex gap-2 border-2 border-black p-2 rounded-3xl my-3">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-500">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
        </svg>
        <input type="password" v-model="form.password_confirmation" placeholder="Confirm Password" class="flex-grow w-full border-none outline-none bg-transparent placeholder-gray-400" required />
      </div>

      <!-- Rol -->
      <div class="flex gap-2 border-2 border-black p-2 rounded-3xl my-3">
        <select v-model="form.role" class="flex-grow w-full border-none outline-none bg-transparent text-gray-600" required>
          <option value="" disabled>Select Role</option>
          <option value="admin">Admin</option>

       
          <option value="alumno">Alumno</option>
          <option value="empresa">Empresa</option>
          <option value="exalumno">Exalumno</option>
         
        </select>
      </div>

      <!-- Área de formación -->
      <div class="flex gap-2 border-2 border-black p-2 rounded-3xl my-3">
        <select v-model="form.training_area" class="flex-grow w-full border-none outline-none bg-transparent text-gray-600" required>
          <option value="" disabled>Select Training Area</option>
          <option value="Informatica">Informática</option>
          <option value="Marketing">Marketing</option>
          <option value="Automocion">Automoción</option>
        </select>
      </div>

      <!-- Botón registro con GitHub -->
      <button type="button" @click="registerWithGitHub" class="flex items-center gap-2 px-5 py-2 bg-gray-800 text-white rounded-3xl my-2 hover:bg-gray-700 transition">
        <img src="/public/images/github-mark-white.png" alt="" class="w-10 h-10">Register with GitHub
      </button>

      <!-- Botón registro -->
      <button type="submit" :disabled="form.processing"
        class="w-full rounded-3xl px-5 py-2 bg-blue-800 text-white text-lg font-semibold hover:bg-blue-600 transition my-2">
        Registrarse
      </button>
      <button type="button" @click="registerWithGoogle" class="border border-gray-300 rounded-md p-2">
        Register with Google
      </button>

      <!-- Mostrar errores -->
      <div v-if="form.errors" class="mt-4 text-red-500">
        <ul>
          <li v-for="(message, field) in form.errors" :key="field">{{ message }}</li>
        </ul>
      </div>
    </form>
  </div>
</template>

<script>
import Navbar from '../../Components/Navbar.vue';
import { useForm } from '@inertiajs/vue3';

export default {
  components: {
    Navbar,
  },
  setup() {
    const form = useForm({
      first_name: '',
      last_name_1: '',
      last_name_2: '',
      email: '',
      password: '',
      password_confirmation: '',
      role: '',
      training_area: '',
    });

    function submit() {
      form.post('/register');
    }

    function registerWithGitHub() {
      window.location.href = '/github/redirect';
    }
    function registerWithGoogle() {
      window.location.href = '/google/redirect';
    }

    return {
      form,
      submit,
      registerWithGitHub,
      registerWithGoogle
    };
  },
};
</script>