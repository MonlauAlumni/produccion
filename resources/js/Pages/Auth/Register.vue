<template>
  <Navbar />
  
  <div class="max-w-2xl mx-auto p-8 mt-5 bg-white">
    <div>
            <h1 class="text-5xl font-semibold text-gray-800 mb-6">
                Registro de cuenta
            </h1>
            <h2 class="text-lg text-gray-500">
              Complete todos los campos para confirmar el registro
            </h2>
        </div>
    
    <form @submit.prevent="submit">

    <div class="grid grid-cols-1 mt-4 gap-2 mb-6">
      <div class="flex flex-row gap-4">
      <InputWithIcon
        v-model="form.first_name"
        icon="person"
        placeholder="Introduce tu nombre"
        id="first_name"
        required
      />
      <InputWithIcon
        v-model="form.last_name_1"
        icon="person"
        placeholder="Introduce tu primer apellido"
        id="last_name_1"
        required />
      </div>  
      <div class="flex flex-row gap-4">
      <InputWithIcon
        v-model="form.last_name_2"
        icon="person"
        placeholder="Introduce tu segundo apellido"
        id="last_name_2" /> 
        <InputWithIcon
        v-model="form.email"
        icon="alternate_email"
        placeholder="Introduce tu correo"
        id="email"
        type="email"
        required />
      </div>
      <div class="flex flex-row gap-4">
        <InputWithIcon 
          v-model="form.password"
          icon="lock"
          placeholder="Crea una contraseña"
          id="password"
          type="password"
          required
          toggleVisibility
          />
        <InputWithIcon
          v-model="form.password_confirmation"
          icon="lock"
          placeholder="Confirma tu contraseña"
          id="password_confirmation"
          type="password"
          required 
          toggleVisibility
          />

          </div>  

      <div class="flex items-center  py-3 px-3 border border-gray-600 rounded-lg relative">
        <select v-model="form.training_area" class="flex-grow w-full border-none outline-none bg-transparent text-gray-600" required>
          <option value="" disabled>Área formativa</option>
          <option value="Informatica">Informática</option>
          <option value="Marketing">Marketing</option>
          <option value="Automocion">Automoción</option>
        </select>
      </div>
      <div class="flex items-center justify-between px-1  mt-2 mb-4">
        <div class="flex items-center gap-x-2">
                    <input
                        type="checkbox"
                        id="privacyPolicy"
                        required
                        class="form-checkbox h-5 w-5 rounded-full text-blue-600"
                    />
                    <label for="privacyPolicy" class="ml-2 text-gray-900">
                        He leído y acepto la
                        <strong class="underline cursor-pointer"
                            >Política de Privacidad</strong
                        >
                    </label>
                  </div>
                
                    <span>
                        ¿Ya tienes cuenta?
                        <a href="/login" class="text-blue-600">Inicia sesión</a>
                    </span>
             
                </div>
     

      <div class="flex justify-between mb-3 gap-x-2">

       <OAuthButton provider="github" @click="registerWithGitHub" text="GitHub" />
       <OAuthButton provider="google" text="Google" />
       <OAuthButton provider="microsoft" text="Microsoft" />
      </div>


      <SubmitButton :disabled="form.processing" label="Registrar" />
      

      <!-- Mostrar errores -->
      <div v-if="form.errors" class="mt-4 text-red-500">
        <ul>
          <li v-for="(message, field) in form.errors" :key="field">{{ message }}</li>
        </ul>
      </div>
      </div>
    </form>
  </div>

</template>

<script>
import ButtonRegister from '../../Components/ButtonRegister.vue';

import Navbar from '../../Components/Navbar.vue';
import { useForm } from '@inertiajs/vue3';
import InputWithIcon from '../../Components/InputWithIcon.vue';
import { ref } from 'vue';
import SubmitButton from '../../Components/SubmitButton.vue';
import OAuthButton from '../../Components/OAuthButton.vue';




export default {
  components: {
    Navbar,
    InputWithIcon,
    SubmitButton,
    OAuthButton,

  },
  setup() {
    const form = useForm({
      first_name: '',
      last_name_1: '',
      last_name_2: '',
      email: '',
      password: '',
      password_confirmation: '',
      training_area: '',
    });
    const showPassword = ref('password');
    const showPasswordConfirm = ref('password');

    function submit() {
      form.post('/register');
    }

    function registerWithGitHub() {
      window.location.href = '/github/redirect';
    }

    function togglePasswordVisibility(field) {
      if (field === 'password') {
        showPassword.value = showPassword.value === 'password' ? 'text' : 'password';
      } else if (field === 'passwordConfirm') {
        showPasswordConfirm.value = showPasswordConfirm.value === 'password' ? 'text' : 'password';
      }
    }

    return {
      form,
      submit,
      registerWithGitHub,
      showPassword,
      showPasswordConfirm,
      togglePasswordVisibility,
    };
  },
};
</script>