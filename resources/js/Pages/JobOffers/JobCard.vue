<script setup>
import { defineProps, defineEmits, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
const page = usePage()
const settings = computed(() => page.props.auth.user_settings)

// Obtener rol del usuario autenticado
const userRole = computed(() => page.props.auth.user?.roles?.[0]?.name ?? null)
// Leer áreas de formación asignadas al usuario desde user_settings
const userArea = computed(() => page.props.auth.user?.training_area ?? null)

const jobOfferArea = computed(() => props.jobOffer.category ?? null)

const props = defineProps({
    jobOffer: {
        type: Object,
        required: true
    },
    company: {
        type: Object,
        default: () => ({})
    },
    isLiked: {
        type: Boolean,
        default: false
    },
    isSaved: {
        type: Boolean,
        default: false
    }
});

const MAX_CHARACTERS = 300;

const emit = defineEmits([
    'view',
    'apply',
    'save',
    'like',
    'share'
]);

const truncatedDescription = computed(() => {
  return props.jobOffer.description.length > MAX_CHARACTERS
    ? props.jobOffer.description.substring(0, MAX_CHARACTERS) + '...'
    : props.jobOffer.description;
});

// Formatear rango salarial
const formatSalaryRange = (min, max) => {
    min = parseInt(min, 10);
    max = parseInt(max, 10);
    if (!min && !max) return 'No especificado';
    if (min && max) return `${min.toLocaleString()}€ - ${max.toLocaleString()}€`;
    return min ? `Desde ${min.toLocaleString()}€` : `Hasta ${max.toLocaleString()}€`;
};

// Calcular tiempo desde publicación
const getTimeAgo = (dateString) => {
    if (!dateString) return 'Reciente';

    const posted = new Date(dateString);
    const today = new Date();
    const diffTime = Math.abs(today - posted);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Hoy';
    if (diffDays === 1) return 'Ayer';
    if (diffDays < 7) return `Hace ${diffDays} días`;
    if (diffDays < 30) return `Hace ${Math.floor(diffDays / 7)} semanas`;
    return `Hace ${Math.floor(diffDays / 30)} meses`;
};

// Obtener etiqueta para tipo de trabajo
const getJobTypeLabel = (jobType) => {
    const types = {
        'remote': 'Remoto',
        'onsite': 'Presencial',
        'hybrid': 'Híbrido'
    };
    return types[jobType] || jobType;
};

// Métodos para emitir eventos
const viewJobOffer = (id) => {
    emit('view', id);
};

const applyToJob = (id) => {
    emit('apply', id);
};

const toggleSaveJob = (id) => {
   emit('save', id);
    
};

const toggleLikeJob = (id) => {
    emit('like', id);
};

const shareJob = (id) => {
    emit('share', id);
};

const getIconClass = (category) => {
      switch (category) {
        case 'it':
          return 'bx bx-code-alt';
        case 'automotive':
          return 'bx bx-car';
        case 'marketing':
          return 'bx bx-line-chart';
        default:
          return 'bx bx-category'; 
      }
    };

  
const getCategoryLabel = (category) => {
      switch (category) {
        case 'it':
          return 'Tecnología';
        case 'automotive':
          return 'Automoción';
        case 'marketing':
          return 'Marketing';
        default:
          return 'Categoría desconocida'; 
      }
    }
  ;
  
// Mapeo de áreas de formación a categorías de trabajo
const areaToCategoryMap = {
  'informatica': 'it',
  'marketing': 'marketing',
  'automocion': 'automotive'
};

// Mapeo inverso para mostrar en la interfaz
const categoryToTrainingArea = {
  'it': 'Informatica',
  'automotive': 'Automocion',
  'marketing': 'Marketing'
};

// Función para normalizar texto (quitar acentos, convertir a minúsculas)
const normalizeText = (text) => {
  if (!text) return '';
  return text.toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, ""); // Elimina acentos
};

const isApplyDisabled = computed(() => {
  // Si el usuario es empresa, no puede aplicar
  if (userRole.value === 'empresa') return true;
  
  // Si no hay área de usuario o categoría de trabajo, deshabilitar
  if (!userArea.value || !jobOfferArea.value) return true;
  
  // Normalizar el área de formación del usuario
  const normalizedUserArea = normalizeText(userArea.value);
  
  // Normalizar la categoría del trabajo
  const normalizedJobCategory = normalizeText(jobOfferArea.value);
  
  // Verificar si hay coincidencia directa o a través del mapeo
  return !(
    // Coincidencia directa
    normalizedUserArea === normalizedJobCategory ||
    // O coincidencia a través del mapeo
    normalizeText(areaToCategoryMap[normalizedUserArea]) === normalizedJobCategory ||
    // O coincidencia inversa
    normalizedUserArea === normalizeText(categoryToTrainingArea[normalizedJobCategory])
  );
});
</script>

<template>
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <!-- Status Badge (si existe) -->
        <div v-if="jobOffer.status === 'featured'"
            class="bg-[#4A90E2] text-white text-xs font-bold px-3 py-1 flex items-center">
            <i class='bx bx-star mr-1'></i>
            Oferta destacada
        </div>

        <div v-if="jobOffer.status === 'urgent'"
            class="bg-red-500 text-white text-xs font-bold px-3 py-1 flex items-center">
            <i class='bx bx-time mr-1'></i>
            Contratación urgente
        </div>

        <div class="p-5">
            <!-- Header -->
            <div class="flex items-start gap-4">
                <!-- Company Logo -->
                <div
                    class="w-14 h-14 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center shrink-0 border border-gray-200 dark:border-gray-600">
                    <img v-if="jobOffer.company && jobOffer.company.profile_picture"
                        :src="`${jobOffer.company.profile_picture}`" :alt="jobOffer.company.name || ''"
                        class="w-13 h-13 rounded-lg" />
                    <i v-else class='bx bx-building text-2xl text-gray-400 dark:text-gray-500'></i>
                </div>

                <!-- Job Info -->
                <div class="flex-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="font-bold text-lg text-[#4A90E2] hover:underline cursor-pointer"
                                @click="viewJobOffer(jobOffer.id)">
                                {{ jobOffer.title }}
                            </h2>
                            <div class="text-gray-700 dark:text-gray-300">
                                {{ jobOffer.company ? jobOffer.company.company_name : '' }}
                            </div>
                        </div>

                        <!-- Expires Badge -->
                        <div v-if="jobOffer.deadline"
                            class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300 text-xs font-bold px-3 py-1 w-40 rounded-full flex items-center">
                            <i class='bx bx-calendar-exclamation mr-1'></i>
                            Expira: {{ new Date(jobOffer.deadline).toLocaleDateString() }}
                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm mt-3">
                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                            <i class='bx bx-map-pin mr-1 text-[#4A90E2]'></i>
                            {{ jobOffer.location }}
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                            <i class='bx bx-time-five mr-1 text-[#4A90E2]'></i>
                            {{ getJobTypeLabel(jobOffer.work_mode) }}
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                            <i class='bx bx-money mr-1 text-[#4A90E2]'></i>
                            {{ formatSalaryRange(jobOffer.minimum_salary, jobOffer.maximum_salary) }}
                        </div>
                        <div v-if="jobOffer.category" class="flex items-center text-gray-600 dark:text-gray-400">
                            <!-- Icono dinámico según la categoría -->
                            <i :class="getIconClass(jobOffer.category)" class="mr-1 text-[#4A90E2]"></i>
                            <!-- Etiqueta dinámica según la categoría -->
                            {{ getCategoryLabel(jobOffer.category) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-4 text-gray-700 dark:text-gray-300" v-html="truncatedDescription" :style="{ fontSize: settings.font_size + 'px' }">
              
            </div>

            <!-- Stats & Engagement -->
            <div class="flex flex-wrap items-center justify-between mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center">
                        <i class='bx bx-calendar mr-1'></i>
                        {{ getTimeAgo(jobOffer.created_at) }}
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-2 mt-3 sm:mt-0">

                    <button @click="toggleSaveJob(jobOffer.id)" :class="[
                        'px-2 py-2 rounded-full flex cursor-pointer items-center justify-center transition-colors',
                        isSaved
                            ? 'bg-[#4A90E2]/20 text-[#4A90E2]'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                    ]">
                        <i :class="[isSaved ? 'bx bxs-bookmark' : 'bx bx-bookmark']"></i>
                    </button>
                   
                </div>
            </div>

            <!-- Apply Button -->
            <div class="mt-4 flex">
                <button @click="viewJobOffer(jobOffer.id)"
                    class="flex-1 bg-white dark:bg-gray-800 border cursor-pointer border-[#4A90E2] text-[#4A90E2] hover:bg-blue-100 dark:hover:bg-[#4A90E2]/10 font-medium py-2 rounded-l-lg transition-colors">
                    Ver detalles
                </button>
                <button
                  @click="applyToJob(jobOffer.id)"
                  :disabled="isApplyDisabled"
                  :class="[
                    'flex-1 font-medium py-2 rounded-r-lg transition-colors',
                    isApplyDisabled
                      ? 'bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed'
                      : 'bg-[#4A90E2] text-white hover:bg-[#357ABD] cursor-pointer'
                  ]"
                >
                  Aplicar ahora
                </button>
            </div>
        </div>
    </div>
</template>