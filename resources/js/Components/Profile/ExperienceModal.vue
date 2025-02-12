<script setup>
import { ref, defineProps, defineEmits } from "vue";
import { router } from '@inertiajs/vue3'

const props = defineProps({ isOpen: Boolean, slang: String });
const emit = defineEmits(["close", "save"]);

const experience = ref({
  user_id: props.userId || null,
  company_name: "",
  position: "",
  start_date: "",
  end_date: "",
  description: "",
});

const closeModal = () => {
  emit("close");
};

const submitExperience =  () => {
  if (!experience.value.company_name || !experience.value.position) {
    alert("Por favor, completa todos los campos obligatorios.");
    return;
  }

  if (!props.slang) {
    console.error("Error: slang aún no está disponible");
    alert("Error al obtener la información necesaria. Intenta de nuevo.");
    return;
  }

  // Emitir la experiencia para guardarla en el componente padre
  emit("save", experience.value);
  closeModal();

  // Enviar los datos al backend si estás utilizando Inertia.js o un método como axios/fetch.
  try {
     router.post(`/perfil/${props.slang}/experiencia`, experience.value);
     router.visit(`/perfil/${props.slang}`)
  } catch (error) {
    console.error("Error al guardar la experiencia", error);
  }
};

</script>


<template>
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-[rgba(0,0,0,0.5)] bg-opacity-50">
      <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-bold mb-4">Añadir Experiencia</h2>
  
        <form @submit.prevent="submitExperience">
          <div class="mb-3">
            <label class="block text-sm font-semibold">Nombre de la empresa</label>
            <input v-model="experience.company_name" type="text" class="w-full border rounded px-2 py-1">
          </div>
  
          <div class="mb-3">
            <label class="block text-sm font-semibold">Puesto</label>
            <input v-model="experience.position" type="text" class="w-full border rounded px-2 py-1">
          </div>
  
          <div class="mb-3">
            <label class="block text-sm font-semibold">Fecha de inicio</label>
            <input v-model="experience.start_date" type="date" class="w-full border rounded px-2 py-1">
          </div>
  
          <div class="mb-3">
            <label class="block text-sm font-semibold">Fecha de fin</label>
            <input v-model="experience.end_date" type="date" class="w-full border rounded px-2 py-1">
          </div>
  
          <div class="mb-3">
            <label class="block text-sm font-semibold">Descripción</label>
            <textarea v-model="experience.description" rows="3" class="w-full border rounded px-2 py-1"></textarea>
          </div>
  
          <div class="flex justify-between mt-4">
            <button type="button" @click="closeModal" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
            <button type="submit"  class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
