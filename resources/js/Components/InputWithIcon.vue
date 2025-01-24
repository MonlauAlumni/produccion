<template>
  <div
    class="w-1/2 flex items-center p-1 px-3 border border-gray-600 rounded-lg"
  >
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
      default: "text",  // Por defecto será "text", pero puede ser otro tipo si se pasa
    },
  },
  computed: {
    inputType() {
      return this.type || 'text';  // Usa el valor de 'type' si se pasa, sino usa 'text' por defecto
    },
  },
  methods: {
    handleInput(event) {
      this.$emit('update:modelValue', event.target.value);
    },
  },
};
</script>
