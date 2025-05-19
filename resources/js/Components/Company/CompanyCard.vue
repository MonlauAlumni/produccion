<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
        <div class="h-40 bg-gray-200 dark:bg-gray-700 relative">
            <!-- Banner de la empresa -->
            <img
                v-if="company.banner_url"
                :src="company.banner_url"
                alt="Banner de la empresa"
                class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full bg-gradient-to-r from-[#193CB8]/80 to-[#193CB8]/60"></div>

            <!-- Logo de la empresa -->
            <div class="absolute -bottom-10 left-6">
                <div class="w-20 h-20 rounded-lg bg-white dark:bg-gray-900 shadow-md flex items-center justify-center overflow-hidden">
                    <img
                        v-if="company.profile_picture"
                        :src="company.profile_picture"
                        alt="Logo de la empresa"
                        class="w-full h-full object-contain p-1"
                    />
                    <div v-else class="w-full h-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-[#193CB8] dark:text-blue-200">
                        <i class='bx bx-building text-4xl'></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 pt-12">
            <!-- Nombre y sector -->
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-1">{{ company.company_name }}</h3>
            <div class="flex gap-x-5">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                    <i class='bx bx-category-alt mr-1'></i>
                    {{ company.sector ?? 'Sin sector' }}
                </p>

                <!-- Ubicación -->
                <p v-if="company.location" class="text-sm text-gray-600 dark:text-gray-400 mb-3 flex items-center">
                    <i class='bx bx-map mr-1'></i>
                    {{ company.location }}
                </p>
            </div>
            <!-- Descripción corta -->
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                {{ company.description ?? 'Sin descripción disponible' }}
            </p>

            <!-- Botón para ver perfil -->
            <div class="mt-4">
                <a
                    @click="viewCompanyProfile(company.slang)"
                    class="w-full block text-center px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors cursor-pointer"
                >
                    Ver perfil
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
 
  
  defineProps({
    company: {
      type: Object,
      required: true
    }
  });

  const viewCompanyProfile = (companySlang) => {
    window.location.href = `/empresa/${companySlang}`;
  };

  </script>
  

  <style scoped>
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  </style>