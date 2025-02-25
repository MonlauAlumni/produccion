<template>
    <div>
      <Navbar />
      <div class="max-w-2xl mx-auto mt-[8%] p-8  bg-white">
        <div>
                <h1 class="text-5xl font-semibold text-gray-800 mb-6">
                    Completa Tu Perfil
                </h1>
                <h2 class="text-lg text-gray-500">
                  Complete todos los campos para confirmar su registro
                </h2>
            </div>
        
        <form @submit.prevent="submitForm">
    
        <div class="grid grid-cols-1 mt-4 gap-2 mb-6">
          <div class="flex flex-row gap-4">
          <InputWithIcon
            v-model="form.name"
            icon="person"
            placeholder="Introduce tu nombre"
            id="name"
            required
            class="w-full"
          />
          
          </div>  
          <div class="flex flex-row gap-4">
            <InputWithIcon
            v-model="form.last_name_1"
            icon="person"
            placeholder="Introduce tu primer apellido"
            id="last_name_1"
            required />
          <InputWithIcon
            v-model="form.last_name_2"
            icon="person"
            placeholder="Introduce tu segundo apellido"
            id="last_name_2" /> 
          </div>
            

          <div class="flex items-center h-12 p-1 px-3 border border-gray-600 rounded-lg relative">
  <span class="material-symbols-outlined text-gray-800 mr-2">
    school
  </span>
  <select
    v-model="form.training_area"
    class="flex-grow h-full border-none outline-none bg-transparent text-gray-600"
    required
  >
    <option value="" disabled>Área formativa</option>
    <option value="Informatica">Informática</option>
    <option value="Marketing">Marketing</option>
    <option value="Automocion">Automoción</option>
  </select>
</div>
          <SubmitButton class="" label="Completar Perfil" />
        </div>
        </form>
      </div>
      
    </div>
  </template>
  
  <script>
  import Navbar from '../../Components/Navbar.vue';
  import { useForm } from '@inertiajs/vue3';
  import InputWithIcon from '../../Components/InputWithIcon.vue';
  import SubmitButton from '../../Components/SubmitButton.vue';

  export default {
    components: {
      Navbar,
      InputWithIcon,
      SubmitButton,
    },
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
  