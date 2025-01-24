<template>
  <div class="w-1/2 flex items-center p-1 px-3 border border-gray-600 rounded-lg relative">
    <!-- Mostrar el icono solo si se pasa un valor en el prop 'icon' -->
    <span v-if="icon" class="material-symbols-outlined text-gray-800 mr-2">
      {{ icon }}
    </span>

    <!-- El campo de entrada -->
    <input
      v-bind="$attrs"
      :value="modelValue"
      @input="handleInput"
      :placeholder="placeholder"
      :type="inputType"
      class="p-2 w-full outline-none"
    />

    <!-- Icono de visibilidad, solo si toggleVisibility es true -->
    <span
      v-if="toggleVisibility"
      @click="toggleVisibilityfn"
      class="material-symbols-outlined text-gray-500 right-2 mt-1 top-2 cursor-pointer"
    >
      {{ inputType === 'password' ? 'visibility_off' : 'visibility' }}
    </span>
  </div>
</template>

<script>
export default {
  props: {
    modelValue: {
      type: String,
      required: true,
    },
    icon: String,
    placeholder: String,
    type: {
      type: String,
      default: "text",  // Por defecto será "password", pero puede ser otro tipo si se pasa
    },
    toggleVisibility: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      inputType: this.type,  // Inicializa inputType con el valor de 'type' recibido
    };
  },
  methods: {
    handleInput(event) {
      this.$emit('update:modelValue', event.target.value);
    },
    toggleVisibilityfn() {
      // Alterna el valor de inputType entre 'password' y 'text'
      this.inputType = this.inputType === 'password' ? 'text' : 'password';
    },
  },
};
</script>
