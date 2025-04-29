<template>
    <div class="flex gap-2 border-2 border-black p-2 rounded-3xl my-3">
      <slot name="icon">
        <!-- Icono por defecto -->
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-500">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
        </svg>
      </slot>
      <component
        :is="isSelect ? 'select' : 'input'"
        :value="modelValue"
        :type="type"
        :placeholder="placeholder"
        :class="inputClasses"
        :required="required"
      >
        <slot v-if="isSelect"></slot>
      </component>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      modelValue: {
        type: [String, Number],
        required: true,
      },
      type: {
        type: String,
        default: 'text',
      },
      placeholder: {
        type: String,
        default: '',
      },
      isSelect: {
        type: Boolean,
        default: false,
      },
      required: {
        type: Boolean,
        default: false,
      },
    },
    emits: ['update:modelValue'],
    computed: {
      inputClasses() {
        return 'flex-grow w-full border-none outline-none bg-transparent placeholder-gray-400';
      },
    },
  };
  </script>
  