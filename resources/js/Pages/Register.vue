<template>
    <div>
      <h1>Register</h1>
      <form @submit.prevent="submit">
        <input type="text" v-model="form.first_name" placeholder="First Name" />
        <input type="text" v-model="form.last_name_1" placeholder="Last Name 1" />
        <input type="text" v-model="form.last_name_2" placeholder="Last Name 2" />
        <input type="email" v-model="form.email" placeholder="Email" />
        <input type="password" v-model="form.password" placeholder="Password" />
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
  