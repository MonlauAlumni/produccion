<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Layout from '@/Components/Layout.vue';

const props = defineProps({
    auth: {
        type: Object,
        default: () => ({})
    },
    userGroups: {
        type: Array,
        default: () => []
    }
});

const form = ref({
    title: '',
    description: '',
    location: '',
    event_date: '',
    event_time: '',
    is_private: false,
    group_id: '',
    photos: []
});

const errors = ref({});
const photosPreviews = ref([]);
const isUploading = ref(false);
const acceptedResponsibility = ref(false); // Nueva variable para el checkbox de responsabilidad

const descriptionLength = computed(() => form.value.description.length);
const maxDescriptionLength = 500;

const handlePhotosChange = (event) => {
    const files = event.target.files;
    if (!files || files.length === 0) return;

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        if (file.size > 2 * 1024 * 1024) {
            errors.value.photos = 'Las imágenes no deben superar los 2MB';
            return;
        }

        if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
            errors.value.photos = 'Los archivos deben ser imágenes (JPG, PNG o GIF)';
            return;
        }

        form.value.photos.push(file);
        photosPreviews.value.push(URL.createObjectURL(file));
    }
    
    errors.value.photos = null;
};

const removePhoto = (index) => {
    form.value.photos.splice(index, 1);
    URL.revokeObjectURL(photosPreviews.value[index]);
    photosPreviews.value.splice(index, 1);
};

const submitForm = () => {
    if (isUploading.value) return;

    errors.value = {};

    if (!form.value.title) {
        errors.value.title = 'El título del evento es obligatorio';
    }

    if (!form.value.description) {
        errors.value.description = 'La descripción del evento es obligatoria';
    }

    if (!form.value.location) {
        errors.value.location = 'La ubicación del evento es obligatoria';
    }

    if (!form.value.event_date) {
        errors.value.event_date = 'La fecha del evento es obligatoria';
    }

    if (!form.value.event_time) {
        errors.value.event_time = 'La hora del evento es obligatoria';
    }

    if (form.value.is_private && !form.value.group_id) {
        errors.value.group_id = 'Selecciona un grupo para el evento privado';
    }
    
    // Validación para el checkbox de responsabilidad
    if (!acceptedResponsibility.value) {
        errors.value.responsibility = 'Debes aceptar los términos de responsabilidad para crear el evento';
    }

    if (Object.keys(errors.value).length > 0) {
        const firstError = document.querySelector('.error-message');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
    }

    isUploading.value = true;

    // Combine date and time
    const dateTime = new Date(`${form.value.event_date}T${form.value.event_time}`);
    
    const formData = new FormData();
    formData.append('title', form.value.title);
    formData.append('description', form.value.description);
    formData.append('location', form.value.location);
    formData.append('event_date', dateTime.toISOString());
    formData.append('is_private', form.value.is_private ? 1 : 0);
    
    if (form.value.is_private && form.value.group_id) {
        formData.append('group_id', form.value.group_id);
    }

    if (form.value.photos.length > 0) {
        form.value.photos.forEach((photo, index) => {
            formData.append(`photos[${index}]`, photo);
        });
    }

    router.post('/eventos/nuevo', formData, {
        onSuccess: () => {
            // Success handling is done by redirect in controller
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
        <div class="min-h-screen bg-gray-50 flex flex-col">
            <div class="bg-[#193CB8] text-white py-6">
                <div class="max-w-4xl mx-auto px-4">
                    <div class="flex items-center">
                        <button @click="cancelAndReturn"
                            class="cursor-pointer flex justify-center mr-4 hover:bg-white/10 p-2 rounded-full transition-colors">
                            <i class='bx bx-arrow-back text-xl'></i>
                        </button>
                        <h1 class="text-2xl font-bold">Crear un nuevo evento</h1>
                    </div>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="flex-1 py-8">
                <div class="max-w-4xl mx-auto px-4">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-6">Información del evento</h2>

                            <form @submit.prevent="submitForm" class="space-y-6">
                                <!-- Título del evento -->
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                        Título del evento <span class="text-red-500">*</span>
                                    </label>
                                    <input id="title" v-model="form.title" type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors"
                                        placeholder="Ej: Workshop de Desarrollo Web"
                                        :class="{ 'border-red-500': errors.title }" />
                                    <p v-if="errors.title" class="mt-1 text-sm text-red-500 error-message">{{ errors.title }}</p>
                                </div>

                                <!-- Descripción -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                        Descripción <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="description" v-model="form.description" rows="4"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors resize-none"
                                        placeholder="Describe los detalles del evento, qué pueden esperar los asistentes..."
                                        :class="{ 'border-red-500': errors.description }"
                                        :maxlength="maxDescriptionLength"></textarea>
                                    <div class="flex justify-between mt-1">
                                        <p v-if="errors.description" class="text-sm text-red-500 error-message">
                                            {{ errors.description }}</p>
                                        <p class="text-xs text-gray-500">{{ descriptionLength }}/{{ maxDescriptionLength }}</p>
                                    </div>
                                </div>

                                <!-- Ubicación -->
                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                        Ubicación <span class="text-red-500">*</span>
                                    </label>
                                    <input id="location" v-model="form.location" type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors"
                                        placeholder="Ej: Campus Monlau, Aula 302"
                                        :class="{ 'border-red-500': errors.location }" />
                                    <p v-if="errors.location" class="mt-1 text-sm text-red-500 error-message">{{ errors.location }}</p>
                                </div>

                                <!-- Fecha y Hora -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="event_date" class="block text-sm font-medium text-gray-700 mb-1">
                                            Fecha <span class="text-red-500">*</span>
                                        </label>
                                        <input id="event_date" v-model="form.event_date" type="date"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors"
                                            :class="{ 'border-red-500': errors.event_date }" />
                                        <p v-if="errors.event_date" class="mt-1 text-sm text-red-500 error-message">{{ errors.event_date }}</p>
                                    </div>
                                    <div>
                                        <label for="event_time" class="block text-sm font-medium text-gray-700 mb-1">
                                            Hora <span class="text-red-500">*</span>
                                        </label>
                                        <input id="event_time" v-model="form.event_time" type="time"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors"
                                            :class="{ 'border-red-500': errors.event_time }" />
                                        <p v-if="errors.event_time" class="mt-1 text-sm text-red-500 error-message">{{ errors.event_time }}</p>
                                    </div>
                                </div>

                                <!-- Privacidad -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Privacidad</label>
                                    <div class="flex flex-col space-y-3">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio" v-model="form.is_private" :value="false"
                                                class="form-radio h-5 w-5 text-[#193CB8]" />
                                            <div class="ml-3">
                                                <span class="text-gray-800 font-medium">Público</span>
                                                <p class="text-gray-500 text-sm">Cualquier persona puede ver y asistir al evento</p>
                                            </div>
                                        </label>
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio" v-model="form.is_private" :value="true"
                                                class="form-radio h-5 w-5 text-[#193CB8]" />
                                            <div class="ml-3">
                                                <span class="text-gray-800 font-medium">Privado</span>
                                                <p class="text-gray-500 text-sm">Solo miembros del grupo seleccionado pueden ver y asistir al evento</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Grupo (solo si es privado) -->
                                <div v-if="form.is_private">
                                    <label for="group_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        Grupo <span class="text-red-500">*</span>
                                    </label>
                                    <select id="group_id" v-model="form.group_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors"
                                        :class="{ 'border-red-500': errors.group_id }">
                                        <option value="" disabled selected>Selecciona un grupo</option>
                                        <option v-for="group in userGroups" :key="group.id" :value="group.id">
                                            {{ group.name }}
                                        </option>
                                    </select>
                                    <p v-if="errors.group_id" class="mt-1 text-sm text-red-500 error-message">
                                        {{ errors.group_id }}</p>
                                    <p v-if="userGroups.length === 0" class="mt-1 text-sm text-gray-500">
                                        No tienes grupos creados. <a href="/grupos/nuevo" class="text-[#193CB8] hover:underline">Crea un grupo</a> primero.
                                    </p>
                                </div>

                                <!-- Fotos del evento -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Fotos del evento
                                    </label>

                                    <div v-if="photosPreviews.length === 0"
                                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class='bx bx-image-add text-4xl text-gray-400 mb-2'></i>
                                            <p class="text-gray-500 mb-4">Añade fotos para tu evento</p>
                                            <label
                                                class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors cursor-pointer">
                                                Seleccionar fotos
                                                <input type="file" accept="image/*" multiple class="hidden"
                                                    @change="handlePhotosChange" />
                                            </label>
                                            <p class="text-xs text-gray-500 mt-2">PNG, JPG o GIF (máx. 2MB por imagen)</p>
                                        </div>
                                    </div>

                                    <div v-else class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                                        <div v-for="(preview, index) in photosPreviews" :key="index" class="relative">
                                            <img :src="preview" alt="Vista previa de foto"
                                                class="w-full h-40 object-cover rounded-lg" />
                                            <button type="button" @click="removePhoto(index)"
                                                class="absolute top-2 right-2 bg-white/80 text-gray-700 rounded-full p-1 hover:bg-white transition-colors">
                                                <i class='bx bx-x text-xl'></i>
                                            </button>
                                        </div>
                                        <div
                                            class="border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center h-40">
                                            <label class="cursor-pointer flex flex-col items-center">
                                                <i class='bx bx-plus text-2xl text-gray-400'></i>
                                                <span class="text-sm text-gray-500">Añadir más</span>
                                                <input type="file" accept="image/*" multiple class="hidden"
                                                    @change="handlePhotosChange" />
                                            </label>
                                        </div>
                                    </div>

                                    <p v-if="errors.photos" class="mt-1 text-sm text-red-500 error-message">
                                        {{ errors.photos }}</p>
                                </div>

                                <!-- Vista previa del evento -->
                                <div v-if="form.title || form.description || photosPreviews.length > 0" class="border border-gray-200 rounded-xl overflow-hidden">
                                    <div class="bg-gray-50 p-4 border-b border-gray-200">
                                        <h3 class="font-bold text-gray-800">Vista previa del evento</h3>
                                    </div>
                                    
                                    <div class="relative">
                                        <!-- Banner -->
                                        <div class="h-48 bg-gradient-to-r from-[#193CB8]/20 to-[#2748c6]/20 relative">
                                            <img 
                                                v-if="photosPreviews.length > 0" 
                                                :src="photosPreviews[0]" 
                                                alt="Foto principal del evento" 
                                                class="w-full h-full object-cover"
                                            />
                                        </div>
                                        
                                        <!-- Información del evento -->
                                        <div class="p-6">
                                            <h2 class="text-2xl font-bold text-gray-800">{{ form.title || 'Título del evento' }}</h2>
                                            
                                            <div class="flex flex-wrap items-center text-gray-500 text-sm mt-3 gap-4">
                                                <span v-if="form.event_date" class="flex items-center">
                                                    <i class='bx bx-calendar mr-1'></i> {{ form.event_date }}
                                                    <span v-if="form.event_time" class="ml-1">{{ form.event_time }}</span>
                                                </span>
                                                <span v-if="form.location" class="flex items-center">
                                                    <i class='bx bx-map mr-1'></i> {{ form.location }}
                                                </span>
                                                <span class="flex items-center">
                                                    <i class='bx bx-user-circle mr-1'></i> 1 asistente
                                                </span>
                                                <span class="flex items-center">
                                                    <i class='bx bx-lock-open-alt mr-1' v-if="!form.is_private"></i>
                                                    <i class='bx bx-lock-alt mr-1' v-else></i>
                                                    {{ form.is_private ? 'Evento privado' : 'Evento público' }}
                                                </span>
                                            </div>
                                            
                                            <p class="text-gray-600 mt-4">{{ form.description || 'Descripción del evento...' }}</p>
                                            
                                            <!-- Galería de fotos -->
                                            <div v-if="photosPreviews.length > 1" class="mt-6">
                                                <h4 class="font-medium text-gray-700 mb-2">Galería de fotos</h4>
                                                <div class="grid grid-cols-4 gap-2">
                                                    <img 
                                                        v-for="(preview, index) in photosPreviews.slice(1, 5)" 
                                                        :key="index"
                                                        :src="preview" 
                                                        alt="Foto del evento" 
                                                        class="w-full h-20 object-cover rounded-lg"
                                                    />
                                                </div>
                                                <p v-if="photosPreviews.length > 5" class="text-sm text-gray-500 mt-2">
                                                    +{{ photosPreviews.length - 5 }} fotos más
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Aviso de responsabilidad -->
                                <div class="bg-blue-50 border border-blue-100 rounded-lg p-5">
                                    <div class="flex items-start">
                                        <i class='bx bx-info-circle text-[#193CB8] text-xl mt-0.5 mr-3'></i>
                                        <div>
                                            <h4 class="font-semibold text-gray-800 mb-2">Términos de responsabilidad</h4>
                                            <p class="text-sm text-gray-700 mb-3">
                                                Al crear este evento, confirmas que:
                                            </p>
                                            <ul class="list-disc pl-5 mb-4 text-sm text-gray-700 space-y-1">
                                                <li>Te responsabilizas completamente del contenido y la organización del evento</li>
                                                <li>La información proporcionada es precisa y veraz</li>
                                                <li>El evento cumple con las leyes y normativas aplicables</li>
                                                <li>No infringes derechos de terceros (propiedad intelectual, privacidad, etc.)</li>
                                                <li>Asumes la responsabilidad por cualquier incidente que pueda ocurrir durante el evento</li>
                                            </ul>
                                            
                                            <div class="flex items-start">
                                                <div class="flex h-5 items-center">
                                                    <input 
                                                        id="responsibility" 
                                                        v-model="acceptedResponsibility"
                                                        type="checkbox" 
                                                        class="h-4 w-4 rounded border-gray-300 text-[#193CB8] focus:ring-[#193CB8]"
                                                        :class="{ 'border-red-500': errors.responsibility }"
                                                    />
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="responsibility" class="font-medium text-gray-700">
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
                                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-100">
                                    <button type="button" @click="cancelAndReturn"
                                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                        class="px-6 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors flex items-center"
                                        :disabled="isUploading">
                                        <span v-if="isUploading" class="flex items-center">
                                            <i class='bx bx-loader-alt animate-spin mr-2'></i> Creando...
                                        </span>
                                        <span v-else>
                                            <i class='bx bx-plus-circle mr-1'></i> Crear evento
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Consejos para crear un evento exitoso -->
                    <div class="bg-[#193CB8]/5 rounded-xl p-6 mt-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                            <i class='bx bx-bulb text-[#193CB8] mr-2'></i> Consejos para crear un evento exitoso
                        </h3>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start">
                                <i class='bx bx-check-circle text-[#193CB8] mt-1 mr-2'></i>
                                <span>Define claramente el propósito y los objetivos del evento</span>
                            </li>
                            <li class="flex items-start">
                                <i class='bx bx-check-circle text-[#193CB8] mt-1 mr-2'></i>
                                <span>Elige un título atractivo y descriptivo</span>
                            </li>
                            <li class="flex items-start">
                                <i class='bx bx-check-circle text-[#193CB8] mt-1 mr-2'></i>
                                <span>Añade fotos representativas para captar la atención</span>
                            </li>
                            <li class="flex items-start">
                                <i class='bx bx-check-circle text-[#193CB8] mt-1 mr-2'></i>
                                <span>Proporciona información detallada sobre la ubicación y cómo llegar</span>
                            </li>
                            <li class="flex items-start">
                                <i class='bx bx-check-circle text-[#193CB8] mt-1 mr-2'></i>
                                <span>Invita a otros alumni para comenzar a construir tu lista de asistentes</span>
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