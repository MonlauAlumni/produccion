<template>
  <Navbar />
  <div class="container mx-auto w-1/2 mt-8">
    <h2 class="text-5xl">Registro de cuenta</h2>
    <p class="mt-4 text-lg text-gray-500">Complete todos los campos para confirmar el registro</p>
    <form @submit.prevent="submit">

      

      <!-- Nombre -->
      <InputField
        v-model="form.first_name"
        placeholder="Introduce tu nombre"
        required
      />

      <!-- Primer apellido -->
      <InputField
        v-model="form.last_name_1"
        placeholder="Introduce tu primer apellido"
        required
      />

      <!-- Correo -->
      <InputField
        v-model="form.email"
        placeholder="Dirección de correo"
        type="email"
        required
      />

      <!-- Rol -->
      <InputField
        v-model="form.role"
        isSelect
        required
      >
        <option value="" disabled>Select Role</option>
        <option value="admin">Admin</option>
        <option value="alumno">Alumno</option>
        <option value="empresa">Empresa</option>
        <option value="exalumno">Exalumno</option>
      </InputField>

      <!-- Botón registro -->
      <ButtonRegister
        :label="'Registrarse'"
        @action="submit"
      />
    </form>
  </div>
</template>


<script>
import Navbar from '../Components/Navbar.vue';
import InputField from '../Components/InputField.vue'; // Importamos el campo de texto
import ButtonRegister from '../Components/ButtonRegister.vue'; // Importamos el botón
import { useForm } from '@inertiajs/vue3';

export default {
  components: {
    Navbar,
    InputField,
    ButtonRegister,
  },
  setup() {
    const form = useForm({
      first_name: '',
      last_name_1: '',
      email: '',
      role: '',
    });

    function submit() {
      form.post('/register');
    }

    return {
      form,
      submit,
    };
  },
};
</script>
