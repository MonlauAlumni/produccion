<template>
  <div class="mb-6">
    <button
      @click="toggleExpand"
      class="w-full flex justify-between items-center py-3 px-4 bg-white text-[#193CB8] transition-all duration-300 ease-in-out rounded-lg  hover:bg-[#193CB8] hover:text-white shadow-sm border border-gray-200 cursor-pointer"
    >
      <h2 class="text-xl font-bold">{{ title  }}</h2>
      <i class="bx bx-chevron-down" :class="{ 'transform rotate-180': isExpanded }"></i>

    </button>
    <transition
      enter-active-class="transition-all duration-300 ease-out"
      leave-active-class="transition-all duration-300 ease-in"
      enter-from-class="transform translate-y-[-10px] opacity-0"
      enter-to-class="transform translate-y-0 opacity-100"
      leave-from-class="transform translate-y-0 opacity-100"
      leave-to-class="transform translate-y-[-10px] opacity-0"
    >
      <div v-if="isExpanded" class="mt-4 space-y-4 overflow-hidden">
        <div
          v-for="(item, index) in items"  
          :key="index"
          class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-[#193CB8] hover:shadow-md transition-shadow duration-300 ease-in-out"
        >
          <h3 class="font-semibold text-lg text-[#193CB8]">
            {{ item.position || item.degree }}  <!-- Usa 'position' para experiencias laborales y 'degree' para formaciones académicas -->
          </h3>
          <p class="text-sm text-gray-500">
            {{ item.start_date }} | {{ item.end_date }}
          </p>
          <p class="mt-2 text-gray-600">{{ item.description }}</p>  <!-- 'description' es común entre ambos -->
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  items: Array,  // Aquí se pasan tanto workExperiences como educations
  title: String
});

const isExpanded = ref(false);

const toggleExpand = () => {
  isExpanded.value = !isExpanded.value;
};
</script>
