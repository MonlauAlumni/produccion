<script setup>
import { ref, defineProps, defineEmits } from "vue";
import { router } from '@inertiajs/vue3'

const props = defineProps({ 
  isOpen: Boolean,
  slang: String,
  type: String,
});
const emit = defineEmits(["close", "save"]);

const experience = ref({
  user_id: null, // Se asignará según el usuario
  company_name: "", // Usado para la experiencia laboral
  position: "", // Usado para la experiencia laboral
  start_date: "",
  end_date: "",
  description: "",
  institution: "", // Usado para la educación
  degree: "", // Usado para la educación
});

const closeModal = () => {
  emit("close");
};

const submitExperience = async () => {
  // Validar campos obligatorios
  if ((props.type === 'work' && (!experience.value.company_name || !experience.value.position)) || 
      (props.type === 'education' && (!experience.value.degree || !experience.value.institution))) {
    alert("Por favor, completa todos los campos obligatorios.");
    return;
  }

  if (!props.slang) {
    console.error("Error: slang aún no está disponible");
    alert("Error al obtener la información necesaria. Intenta de nuevo.");
    return;
  }

  // Preparar los datos según el tipo
  const data = {
    user_id: experience.value.user_id,
    start_date: experience.value.start_date,
    end_date: experience.value.end_date,
    description: experience.value.description,
  };

  // Si es experiencia laboral
  if (props.type === 'work') {
    data.company_name = experience.value.company_name;
    data.position = experience.value.position;
  }

  // Si es formación académica
  if (props.type === 'education') {
    data.degree = experience.value.degree;
    data.institution = experience.value.institution;
  }

  // Emitir los datos para guardarlos en el componente padre
  emit("save", data);
  closeModal();

  // Enviar los datos al backend (para educación o experiencia laboral)
  try {
    if (props.type === 'work') {
      await router.post(`/perfil/${props.slang}/experiencia`, data);
    } else if (props.type === 'education') {
      await router.post(`/perfil/${props.slang}/educacion`, data);
    }

    // Redirigir o hacer cualquier otra acción después de guardar
    router.visit(`/perfil/${props.slang}`);
  } catch (error) {
    console.error("Error al guardar la experiencia o educación", error);
  }
};
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-[rgba(0,0,0,0.5)] bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
      <h2 class="text-xl font-bold mb-4">Añadir {{ props.type === 'work' ? 'Experiencia Laboral' : 'Formación Académica' }}</h2>

      <form @submit.prevent="submitExperience">
        <div class="mb-3" v-if="props.type === 'work'">
          <label class="block text-sm font-semibold">Nombre de la empresa</label>
          <input v-model="experience.company_name" type="text" class="w-full border rounded px-2 py-1">
        </div>

        <div class="mb-3" v-if="props.type === 'work'">
          <label class="block text-sm font-semibold">Puesto</label>
          <input v-model="experience.position" type="text" class="w-full border rounded px-2 py-1">
        </div>

        <div class="mb-3" v-if="props.type === 'education'">
          <label class="block text-sm font-semibold">Título Académico</label>
          <input v-model="experience.degree" type="text" class="w-full border rounded px-2 py-1">
        </div>

        <div class="mb-3" v-if="props.type === 'education'">
          <label class="block text-sm font-semibold">Institución</label>
          <input v-model="experience.institution" type="text" class="w-full border rounded px-2 py-1">
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
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</template>
