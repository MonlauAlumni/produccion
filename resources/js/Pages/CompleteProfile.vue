<template>
    <div>
      <h1>Complete Your Profile</h1>
      <form @submit.prevent="submitForm">
        <div>
          <label for="name">Name:</label>
          <input type="text" v-model="form.name" required />
        </div>
        <div>
          <label for="last_name_1">First Last Name:</label>
          <input type="text" v-model="form.last_name_1" required />
        </div>
        <div>
          <label for="last_name_2">Second Last Name:</label>
          <input type="text" v-model="form.last_name_2"  />
        </div>
        <div>
          <label for="training_area">Training Area:</label>
          <select v-model="form.training_area" required>
            <option value="">Select your area</option>
            <option value="Informatica">Informática</option>
            <option value="Marketing">Marketing</option>
            <option value="Automocion">Automoción</option>
          </select>
        </div>
        <button type="submit">Complete Profile</button>
      </form>
    </div>
  </template>
  
  <script>
  import { useForm } from '@inertiajs/vue3';
  
  export default {
    setup() {
      const form = useForm({
        name: '',
        last_name_1: '',
        last_name_2: '',
        training_area: '',
      });
  
      const submitForm = () => {
        form.post('/complete-profile');
      };
  
      return { form, submitForm };
    },
  };
  </script>
  