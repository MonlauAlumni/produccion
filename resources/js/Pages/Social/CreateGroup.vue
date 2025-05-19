<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Layout from '@/Components/Layout.vue';

const props = defineProps({
    auth: {
        type: Object,
        default: () => ({})
    },
    categories: {
        type: Array,
        default: () => [
            { id: 'emprendimiento', name: 'Emprendimiento' },
            { id: 'tecnologia', name: 'Tecnología' },
            { id: 'marketing', name: 'Marketing' },
            { id: 'finanzas', name: 'Finanzas' },
            { id: 'automocion', name: 'Automoción' },
            { id: 'otros', name: 'Otros' }
        ]
    }
});

const form = ref({
    name: '',
    description: '',
    category: '',
    privacy: 'public',
    tags: '',
    logo: null,
    banner: null
});

const errors = ref({});

const logoPreview = ref(null);
const bannerPreview = ref(null);
const isUploading = ref(false);
const acceptedResponsibility = ref(false); // Nueva variable para el checkbox de responsabilidad

const descriptionLength = computed(() => form.value.description.length);
const maxDescriptionLength = 500;

const handleLogoChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
        errors.value.logo = 'La imagen no debe superar los 2MB';
        return;
    }

    if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
        errors.value.logo = 'El archivo debe ser una imagen (JPG, PNG o GIF)';
        return;
    }

    form.value.logo = file;

    if (logoPreview.value) {
        URL.revokeObjectURL(logoPreview.value);
    }
    logoPreview.value = URL.createObjectURL(file);
    errors.value.logo = null;
};

const handleBannerChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
        errors.value.banner = 'La imagen no debe superar los 2MB';
        return;
    }

    if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
        errors.value.banner = 'El archivo debe ser una imagen (JPG, PNG o GIF)';
        return;
    }

    form.value.banner = file;

    if (bannerPreview.value) {
        URL.revokeObjectURL(bannerPreview.value);
    }

    bannerPreview.value = URL.createObjectURL(file);
    errors.value.banner = null;
};

const removeLogo = () => {
    form.value.logo = null;
    if (logoPreview.value) {
        URL.revokeObjectURL(logoPreview.value);
        logoPreview.value = null;
    }
};

const removeBanner = () => {
    form.value.banner = null;
    if (bannerPreview.value) {
        URL.revokeObjectURL(bannerPreview.value);
        bannerPreview.value = null;
    }
};

const processedTags = computed(() => {
    if (!form.value.tags) return [];
    return form.value.tags.split(',')
        .map(tag => tag.trim())
        .filter(tag => tag.length > 0);
});

const submitForm = () => {
    if (isUploading.value) return;

    errors.value = {};

    if (!form.value.name) {
        errors.value.name = 'El nombre del grupo es obligatorio';
    }

    if (!form.value.description) {
        errors.value.description = 'La descripción del grupo es obligatoria';
    }

    if (!form.value.category) {
        errors.value.category = 'Selecciona una categoría';
    }
    // Nueva validación para responsabilidad
    if (!acceptedResponsibility.value) {
        errors.value.responsibility = 'Debes aceptar los términos de responsabilidad para crear el grupo';
    }

    if (Object.keys(errors.value).length > 0) {
        const firstError = document.querySelector('.error-message');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
    }

    isUploading.value = true;

    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('description', form.value.description);
    formData.append('category', form.value.category);
    formData.append('privacy', form.value.privacy);

    if (processedTags.value.length > 0) {
        formData.append('tags', JSON.stringify(processedTags.value));
    }

    if (form.value.logo) {
        formData.append('logo', form.value.logo);
    }

    if (form.value.banner) {
        formData.append('banner', form.value.banner);
    }

    router.post('/grupos/nuevo', formData, {
        onSuccess: (page) => {

        },
        onError: (newErrors) => {
            isUploading.value = false;
            errors.value = newErrors;
        },
        onFinish: () => {
            isUploading.value = false;
        }
    });
};

const cancelAndReturn = () => {
    router.visit('/connect');
};
</script>

<template>
    <Layout :auth="auth">
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col">
            <div class="bg-[#193CB8] dark:bg-[#142d8c] text-white py-6">
                <div class="max-w-4xl mx-auto px-4">
                    <div class="flex items-center">
                        <button @click="cancelAndReturn"
                            class="cursor-pointer flex justify-center mr-4 hover:bg-white/10 p-2 rounded-full transition-colors">
                            <i class='bx bx-arrow-back text-xl'></i>
                        </button>
                        <h1 class="text-2xl font-bold">Crear un nuevo grupo</h1>
                    </div>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="flex-1 py-8">
                <div class="max-w-4xl mx-auto px-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-6">Información del grupo</h2>

                            <form @submit.prevent="submitForm" class="space-y-6">
                                <!-- Nombre del grupo -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Nombre del grupo <span class="text-red-500">*</span>
                                    </label>
                                    <input id="name" v-model="form.name" type="text"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] dark:bg-gray-700 dark:text-gray-100 outline-none transition-colors"
                                        placeholder="Ej: Emprendedores Monlau"
                                        :class="{ 'border-red-500': errors.name }" />
                                    <p v-if="errors.name" class="mt-1 text-sm text-red-500 error-message">{{ errors.name }}</p>
                                </div>

                                <!-- Descripción -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Descripción <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="description" v-model="form.description" rows="4"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] dark:bg-gray-700 dark:text-gray-100 outline-none transition-colors resize-none"
                                        placeholder="Describe el propósito y los objetivos de tu grupo..."
                                        :class="{ 'border-red-500': errors.description }"
                                        :maxlength="maxDescriptionLength"></textarea>
                                    <div class="flex justify-between mt-1">
                                        <p v-if="errors.description" class="text-sm text-red-500 error-message">
                                            {{ errors.description }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ descriptionLength }}/{{ maxDescriptionLength }}</p>
                                    </div>
                                </div>

                                <!-- Categoría -->
                                <div>
                                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Categoría <span class="text-red-500">*</span>
                                    </label>
                                    <select id="category" v-model="form.category"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] dark:bg-gray-700 dark:text-gray-100 outline-none transition-colors"
                                        :class="{ 'border-red-500': errors.category }">
                                        <option value="" disabled selected>Selecciona una categoría</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <p v-if="errors.category" class="mt-1 text-sm text-red-500 error-message">
                                        {{ errors.category }}</p>
                                </div>

                                <!-- Privacidad -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Privacidad</label>
                                    <div class="flex flex-col space-y-3">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio" v-model="form.privacy" value="public"
                                                class="form-radio h-5 w-5 text-[#193CB8]" />
                                            <div class="ml-3">
                                                <span class="text-gray-800 dark:text-gray-200 font-medium">Público</span>
                                                <p class="text-gray-500 dark:text-gray-400 text-sm">Cualquier persona puede ver y unirse al grupo</p>
                                            </div>
                                        </label>
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio" v-model="form.privacy" value="private"
                                                class="form-radio h-5 w-5 text-[#193CB8]" />
                                            <div class="ml-3">
                                                <span class="text-gray-800 dark:text-gray-200 font-medium">Privado</span>
                                                <p class="text-gray-500 dark:text-gray-400 text-sm">Solo miembros invitados pueden unirse al grupo</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Etiquetas -->
                                <div>
                                    <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Etiquetas (separadas por comas)
                                    </label>
                                    <input id="tags" v-model="form.tags" type="text"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] dark:bg-gray-700 dark:text-gray-100 outline-none transition-colors"
                                        placeholder="Ej: networking, innovación, startups" />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Añade etiquetas para ayudar a otros a encontrar tu grupo</p>

                                    <!-- Previsualización de etiquetas -->
                                    <div v-if="processedTags.length > 0" class="flex flex-wrap gap-2 mt-2">
                                        <span v-for="(tag, index) in processedTags" :key="index"
                                            class="px-2 py-1 bg-[#193CB8]/10 dark:bg-blue-300/20 text-[#193CB8] dark:text-blue-200 rounded-full text-xs">
                                            #{{ tag }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Logo del grupo -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Logo del grupo
                                    </label>

                                    <div v-if="!logoPreview"
                                        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class='bx bx-image-add text-4xl text-gray-400 dark:text-gray-500 mb-2'></i>
                                            <p class="text-gray-500 dark:text-gray-400 mb-4">Añade un logo para tu grupo</p>
                                            <label
                                                class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors cursor-pointer">
                                                Seleccionar logo
                                                <input type="file" accept="image/*" class="hidden"
                                                    @change="handleLogoChange" />
                                            </label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">PNG, JPG o GIF (máx. 2MB)</p>
                                        </div>
                                    </div>

                                    <div v-else class="relative inline-block">
                                        <img :src="logoPreview" alt="Logo del grupo"
                                            class="w-32 h-32 object-cover rounded-lg" />
                                        <button type="button" @click="removeLogo"
                                            class="absolute top-2 right-2 bg-white/80 dark:bg-black/50 text-gray-700 dark:text-gray-300 rounded-full p-1 hover:bg-white dark:hover:bg-black transition-colors">
                                            <i class='bx bx-x text-xl'></i>
                                        </button>
                                    </div>

                                    <p v-if="errors.logo" class="mt-1 text-sm text-red-500 error-message">
                                        {{ errors.logo }}</p>
                                </div>

                                <!-- Banner del grupo -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Encabezado del grupo
                                    </label>

                                    <div v-if="!bannerPreview"
                                        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class='bx bx-image-add text-4xl text-gray-400 dark:text-gray-500 mb-2'></i>
                                            <p class="text-gray-500 dark:text-gray-400 mb-4">Añade un encabezado para tu grupo</p>
                                            <label
                                                class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors cursor-pointer">
                                                Seleccionar banner
                                                <input type="file" accept="image/*" class="hidden"
                                                    @change="handleBannerChange" />
                                            </label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">PNG, JPG o GIF (máx. 2MB)</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tamaño recomendado: 1200 x 375 píxeles</p>
                                        </div>
                                    </div>

                                    <div v-else class="relative">
                                        <img :src="bannerPreview" alt="Banner del grupo"
                                            class="w-full h-48 object-cover rounded-lg" />
                                        <button type="button" @click="removeBanner"
                                            class="absolute top-2 right-2 bg-white/80 dark:bg-black/50 text-gray-700 dark:text-gray-300 rounded-full p-1 hover:bg-white dark:hover:bg-black transition-colors">
                                            <i class='bx bx-x text-xl'></i>
                                        </button>
                                    </div>

                                    <p v-if="errors.banner" class="mt-1 text-sm text-red-500 error-message">
                                        {{ errors.banner }}</p>
                                </div>

                                <!-- Vista previa del grupo -->
                                <div v-if="form.name || logoPreview || bannerPreview" class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                                    <div class="bg-gray-50 dark:bg-gray-800 p-4 border-b border-gray-200 dark:border-gray-700">
                                        <h3 class="font-bold text-gray-800 dark:text-gray-200">Vista previa del grupo</h3>
                                    </div>
                                    
                                    <div class="relative">
                                        <!-- Banner -->
                                        <div class="h-48 bg-gradient-to-r from-[#193CB8]/20 to-[#2748c6]/20 dark:from-[#193CB8]/30 dark:to-[#2748c6]/30 relative">
                                            <img 
                                                v-if="bannerPreview" 
                                                :src="bannerPreview" 
                                                alt="Banner del grupo" 
                                                class="w-full h-full object-cover"
                                            />
                                            
                                            <!-- Logo -->
                                            <div class="absolute left-6 -bottom-12">
                                                <div class="w-24 h-24 rounded-xl overflow-hidden border-4 border-white dark:border-gray-900 bg-white dark:bg-gray-900">
                                                    <img 
                                                        v-if="logoPreview" 
                                                        :src="logoPreview" 
                                                        alt="Logo del grupo" 
                                                        class="w-full h-full object-cover"
                                                    />
                                                    <div v-else class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500">
                                                        <i class='bx bx-image text-4xl'></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Información del grupo -->
                                        <div class="pt-16 px-6 pb-6 bg-white dark:bg-gray-900">
                                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ form.name || 'Nombre del grupo' }}</h2>
                                            
                                            <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm mt-2">
                                                <span class="flex items-center mr-4">
                                                    <i class='bx bx-user-circle mr-1'></i> 1 miembro
                                                </span>
                                                <span class="flex items-center">
                                                    <i class='bx bx-lock-open-alt mr-1'></i> {{ form.privacy === 'public' ? 'Grupo público' : 'Grupo privado' }}
                                                </span>
                                            </div>
                                            
                                            <p class="text-gray-600 dark:text-gray-300 mt-4">{{ form.description || 'Descripción del grupo...' }}</p>
                                            
                                            <!-- Etiquetas -->
                                            <div v-if="processedTags.length > 0" class="flex flex-wrap gap-2 mt-4">
                                                <span 
                                                    v-for="(tag, index) in processedTags" 
                                                    :key="index"
                                                    class="px-2 py-1 bg-[#193CB8]/10 dark:bg-blue-300/20 text-[#193CB8] dark:text-blue-200 rounded-full text-xs"
                                                >
                                                    #{{ tag }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cuadro de términos de responsabilidad -->
                                <div class="bg-blue-50 dark:bg-blue-900 border border-blue-100 dark:border-blue-800 rounded-lg p-5 mt-6">
                                    <div class="flex items-start">
                                        <i class='bx bx-info-circle text-[#193CB8] dark:text-blue-200 text-xl mt-0.5 mr-3'></i>
                                        <div>
                                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Términos de responsabilidad</h4>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                                                Al crear este grupo, confirmas que:
                                            </p>
                                            <ul class="list-disc pl-5 mb-4 text-sm text-gray-700 dark:text-gray-300 space-y-1">
                                                <li>Te responsabilizas completamente del contenido y la organización del grupo</li>
                                                <li>La información proporcionada es precisa y veraz</li>
                                                <li>El grupo cumple con las leyes y normativas aplicables</li>
                                                <li>No infringes derechos de terceros (propiedad intelectual, privacidad, etc.)</li>
                                                <li>Asumes la responsabilidad por cualquier incidente que pueda ocurrir en el grupo</li>
                                            </ul>
                                            
                                            <div class="flex items-start">
                                                <div class="flex h-5 items-center">
                                                    <input 
                                                        id="responsibility" 
                                                        v-model="acceptedResponsibility"
                                                        type="checkbox" 
                                                        class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-[#193CB8] focus:ring-[#193CB8] dark:focus:ring-blue-200"
                                                        :class="{ 'border-red-500': errors.responsibility }"
                                                    />
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="responsibility" class="font-medium text-gray-700 dark:text-gray-300">
                                                        He leído y acepto los términos de responsabilidad
                                                    </label>
                                                </div>
                                            </div>
                                            <p v-if="errors.responsibility" class="mt-1 text-sm text-red-500 error-message">
                                                {{ errors.responsibility }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <button type="button" @click="cancelAndReturn"
                                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                        class="px-6 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors flex items-center"
                                        :disabled="isUploading">
                                        <span v-if="isUploading" class="flex items-center">
                                            <i class='bx bx-loader-alt animate-spin mr-2'></i> Creando...
                                        </span>
                                        <span v-else>
                                            <i class='bx bx-plus-circle mr-1'></i> Crear grupo
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Consejos para crear un grupo exitoso -->
                    <div class="bg-[#193CB8]/5 dark:bg-[#193CB8]/10 rounded-xl p-6 mt-6">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                            <i class='bx bx-bulb text-[#193CB8] dark:text-blue-200 mr-2'></i> Consejos para crear un grupo exitoso
                        </h3>
                        <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                            <li class="flex items-start">
                                <i class='bx bx-check-circle text-[#193CB8] dark:text-blue-200 mt-1 mr-2'></i>
                                <span>Define claramente el propósito y los objetivos del grupo</span>
                            </li>
                            <li class="flex items-start">
                                <i class='bx bx-check-circle text-[#193CB8] dark:text-blue-200 mt-1 mr-2'></i>
                                <span>Elige un nombre atractivo y descriptivo</span>
                            </li>
                            <li class="flex items-start">
                                <i class='bx bx-check-circle text-[#193CB8] dark:text-blue-200 mt-1 mr-2'></i>
                                <span>Añade un logo y encabezado representativos para captar la atención</span>
                            </li>
                            <li class="flex items-start">
                                <i class='bx bx-check-circle text-[#193CB8] dark:text-blue-200 mt-1 mr-2'></i>
                                <span>Utiliza etiquetas relevantes para mejorar la visibilidad</span>
                            </li>
                            <li class="flex items-start">
                                <i class='bx bx-check-circle text-[#193CB8] dark:text-blue-200 mt-1 mr-2'></i>
                                <span>Invita a otros alumni para comenzar a construir tu comunidad</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
.form-radio {
    appearance: none;
    border: 1px solid #d1d5db;
    border-radius: 50%;
    outline: none;
    transition: all 0.2s ease-in-out;
}

.form-radio:checked {
    background-color: #193CB8;
    border-color: #193CB8;
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='4'/%3e%3c/svg%3e");
    background-size: 100% 100%;
    background-position: center;
    background-repeat: no-repeat;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>