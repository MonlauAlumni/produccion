<template>
  <div class="w-1/2 flex items-center p-1 px-3 border border-gray-600 dark:border-gray-500 rounded-lg relative bg-white dark:bg-gray-800">
    <!-- Mostrar el icono solo si se pasa un valor en el prop 'icon' -->
    <span v-if="icon" class="material-symbols-outlined text-gray-800 dark:text-gray-300 mr-2">
      {{ icon }}
    </span>

    <!-- El campo de entrada -->
    <input
      v-bind="$attrs"
      :value="modelValue"
      @input="handleInput"
      :placeholder="placeholder"
      :type="inputType"
      class="p-2 w-full outline-none bg-transparent text-gray-800 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
    />

    <span
      v-if="toggleVisibility"
      @click="toggleVisibilityfn"
      class="material-symbols-outlined text-gray-500 dark:text-gray-400 right-2 mt-1 top-2 cursor-pointer"
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
      default: "text",
    },
    toggleVisibility: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      inputType: this.type,
    };
  },
  methods: {
    handleInput(event) {
      this.$emit('update:modelValue', event.target.value);
    },
    toggleVisibilityfn() {
      this.inputType = this.inputType === 'password' ? 'text' : 'password';
    },
  },
};
</script>
