<script>
export default {
  props: {
    show: Boolean,
    title: String,
    message: String,
    icon: String,
    iconColor: String,
    iconBgClass: String,
    confirmText: String,
    confirmButtonClass: String,
  },
  emits: ["close", "confirm"],
  watch: {
    show(newVal) {
      if (newVal) {
        document.body.style.overflow = "hidden";
      } else {
        document.body.style.overflow = "";
      }
    },
  },
  methods: {
    closeModal() {
      this.$emit("close");
    },
    confirmAction() {
      this.$emit("confirm");
    },
  },
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center z-50">
      <div class="bg-black/60 bg-opacity-50 absolute inset-0" @click="closeModal"></div>
      <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div
              class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full"
              :class="iconBgClass"
            >
              <box-icon :name="icon" :color="iconColor"></box-icon>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ title }}
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">{{ message }}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <div class="grid grid-cols-2 gap-4">
            <button
              @click="confirmAction"
              type="button"
              class="transition duration-300 cursor-pointer inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2"
              :class="confirmButtonClass"
            >
              {{ confirmText }}
            </button>
            <button
              @click="closeModal"
              type="button"
              class="cursor-pointer inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
