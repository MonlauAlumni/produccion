<script setup>
  import { ref } from "vue";
  import { defineProps } from "vue";

  defineProps({
    profile: Object,
    isCompany: Boolean,
  });

  </script>
<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">{{ isCompany ? 'Sobre nosotros' : 'Sobre mí' }}</h2>
        <hr class="border-t border-[#193CB8] mb-4" />
        <p class="text-gray-600">
          {{ profile && profile.description ? profile.description : 'Añade una descripción...' }}

        </p>
      </div>
    </div>
  </template>

 