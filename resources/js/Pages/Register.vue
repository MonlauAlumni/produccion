<template>
  <div>
    <h1>Register</h1>
    <form @submit.prevent="submit">
      <input type="text" v-model="form.first_name" placeholder="First Name" required />
      <input type="text" v-model="form.last_name_1" placeholder="Last Name 1" required />
      <input type="text" v-model="form.last_name_2" placeholder="Last Name 2" />

      <input type="email" v-model="form.email" placeholder="Email" required />
      <input type="password" v-model="form.password" placeholder="Password" required />
      <input type="password" v-model="form.password_confirmation" placeholder="Confirm Password" required />

      <!-- Selector para el rol -->
      <select v-model="form.role" required>
        <option value="" disabled>Select Role</option>
        <option value="admin">Admin</option>
        <option value="teacher">Teacher</option>
        <option value="tutor">Tutor</option>
        <option value="student">Student</option>
        <option value="company">Company</option>
        <option value="guest">Guest</option>
      </select>

      <!-- Selector para el área de formación -->
      <select v-model="form.training_area" required>
        <option value="" disabled>Select Training Area</option>
        <option value="Informatica">Informática</option>
        <option value="Marketing">Marketing</option>
        <option value="Automocion">Automoción</option>
      </select>

      <button type="submit">Register</button>
    </form>
  </div>
</template>

<script>
import { useForm } from '@inertiajs/vue3';

export default {
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

    return {
      form,
      submit,
    };
  },
};
</script>
