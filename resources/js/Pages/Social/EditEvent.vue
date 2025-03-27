<script setup>
import { ref, computed } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import Layout from '@/Components/Layout.vue';

const props = defineProps({
    auth: {
        type: Object,
        default: () => ({})
    },
    event: {
        type: Object,
        required: true
    },
    userGroups: {
        type: Array,
        default: () => []
    }
});

const page = usePage();
const auth = computed(() => page.props.auth);

function formatDateForInput(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    
    // Format to YYYY-MM-DDThh:mm
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

const form = useForm({
    title: props.event.title,
    description: props.event.description,
    location: props.event.location,
    event_date: formatDateForInput(props.event.event_date),
    is_private: props.event.is_private,
    group_id: props.event.group_id,
    photos: []
});

const previewImages = ref([]);
const existingPhotos = ref(props.event.photos || []);
const photosToRemove = ref([]);
const isSubmitting = ref(false);

function handleFileUpload(e) {
    const files = e.target.files;
    if (!files.length) return;

    // Reset the photos array to avoid duplicates
    form.photos = [];

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();

        reader.onload = (e) => {
            previewImages.value.push({
                id: Date.now() + i,
                src: e.target.result,
                file: file
            });
            form.photos.push(file);
        };

        reader.readAsDataURL(file);
    }
}

function removePreviewImage(index) {
    previewImages.value.splice(index, 1);
    form.photos.splice(index, 1);
}

function removeExistingPhoto(photoId, index) {
    photosToRemove.value.push(photoId);
    existingPhotos.value.splice(index, 1);
}

async function submitForm() {
    isSubmitting.value = true;

    // Handle photo removal first if needed
    for (const photoId of photosToRemove.value) {
        try {
            await router.post(`/eventos/${props.event.slug}/remove-photo`, {
                photo_id: photoId
            });
        } catch (error) {
            console.error('Error removing photo:', error);
            // Continue even if there's an error
        }
    }

    // Create FormData for file uploads
    if (form.photos.length > 0) {
        const formData = new FormData();

        // Add all the required fields
        formData.append('_method', 'PUT'); // For method spoofing
        formData.append('title', form.title);
        formData.append('description', form.description);
        formData.append('location', form.location);
        formData.append('event_date', form.event_date);
        formData.append('is_private', form.is_private ? '1' : '0');

        if (form.is_private && form.group_id) {
            formData.append('group_id', form.group_id);
        }

        // Add photos
        for (let i = 0; i < form.photos.length; i++) {
            formData.append(`photos[${i}]`, form.photos[i]);
        }

        // Submit with FormData
        router.post(`/eventos/${props.event.slug}`, formData, {
            forceFormData: true,
            onSuccess: () => {
                router.visit(`/eventos/${props.event.slug}`);
            },
            onFinish: () => {
                isSubmitting.value = false;
            }
        });
    } else {
        // No photos, use regular form submission
        form.put(`/eventos/${props.event.slug}`, {
            onSuccess: () => {
                router.visit(`/eventos/${props.event.slug}`);
            },
            onFinish: () => {
                isSubmitting.value = false;
            }
        });
    }
}

function cancelEdit() {
    router.visit(`/eventos/${props.event.slug}`);
}
</script>

<template>
    <Layout :auth="auth">
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-4xl mx-auto px-4">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">Editar evento</h1>
                    <p class="text-gray-600">Actualiza la información de tu evento</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <form @submit.prevent="submitForm" class="p-6">
                        <div class="space-y-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                    Título del evento *
                                </label>
                                <input id="title" v-model="form.title" type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors"
                                    placeholder="Ej: Networking para profesionales de marketing" required />
                                <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}
                                </p>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Descripción *
                                </label>
                                <textarea id="description" v-model="form.description" rows="6"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors"
                                    placeholder="Describe tu evento, incluye detalles sobre lo que los asistentes pueden esperar"
                                    required></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{
                                    form.errors.description }}</p>
                            </div>

                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                    Ubicación *
                                </label>
                                <input id="location" v-model="form.location" type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors"
                                    placeholder="Ej: Calle Principal 123, Madrid" required />
                                <p v-if="form.errors.location" class="mt-1 text-sm text-red-600">{{ form.errors.location
                                }}</p>
                            </div>

                            <div>
                                <label for="event_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha y hora *
                                </label>
                                <input id="event_date" v-model="form.event_date" type="datetime-local"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors"
                                    required />
                                <p v-if="form.errors.event_date" class="mt-1 text-sm text-red-600">{{
                                    form.errors.event_date }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Privacidad del evento *
                                </label>
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input id="public" v-model="form.is_private" :value="false" type="radio"
                                            class="h-4 w-4 text-[#193CB8] focus:ring-[#193CB8]" />
                                        <label for="public" class="ml-3 block text-gray-700">
                                            <span class="font-medium">Público</span>
                                            <p class="text-sm text-gray-500">Cualquier persona puede ver y asistir a
                                                este evento</p>
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="private" v-model="form.is_private" :value="true" type="radio"
                                            class="h-4 w-4 text-[#193CB8] focus:ring-[#193CB8]" />
                                        <label for="private" class="ml-3 block text-gray-700">
                                            <span class="font-medium">Privado</span>
                                            <p class="text-sm text-gray-500">Solo los miembros del grupo seleccionado
                                                pueden ver y asistir</p>
                                        </label>
                                    </div>
                                </div>
                                <p v-if="form.errors.is_private" class="mt-1 text-sm text-red-600">{{
                                    form.errors.is_private }}</p>
                            </div>

                            <div v-if="form.is_private">
                                <label for="group_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Selecciona un grupo *
                                </label>
                                <select id="group_id" v-model="form.group_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors"
                                    :required="form.is_private">
                                    <option value="" disabled>Selecciona un grupo</option>
                                    <option v-for="group in userGroups" :key="group.id" :value="group.id">
                                        {{ group.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.group_id" class="mt-1 text-sm text-red-600">{{ form.errors.group_id
                                }}</p>
                            </div>

                            <div v-if="existingPhotos.length > 0">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Fotos actuales
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div v-for="(photo, index) in existingPhotos" :key="photo.id" class="relative">
                                        <img :src="photo.photo_path" :alt="props.event.title"
                                            class="w-full h-40 object-cover rounded-lg" />
                                        <button type="button" @click="removeExistingPhoto(photo.id, index)"
                                            class="absolute top-2 right-2 bg-white/80 text-gray-700 rounded-full p-1 hover:bg-white transition-colors">
                                            <i class='bx bx-x text-xl'></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Añadir nuevas fotos
                                </label>
                                <div
                                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                    <div class="space-y-1 text-center">
                                        <i class='bx bx-image-add text-4xl text-gray-400'></i>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="photos"
                                                class="relative cursor-pointer bg-white rounded-md font-medium text-[#193CB8] hover:text-[#142d8c] focus-within:outline-none">
                                                <span>Sube fotos</span>
                                                <input id="photos" name="photos" type="file" multiple accept="image/*"
                                                    class="sr-only" @change="handleFileUpload" />
                                            </label>
                                            <p class="pl-1">o arrastra y suelta</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, GIF hasta 2MB
                                        </p>
                                    </div>
                                </div>
                                <p v-if="form.errors.photos" class="mt-1 text-sm text-red-600">{{ form.errors.photos }}
                                </p>
                            </div>

                            <div v-if="previewImages.length > 0">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Vista previa
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div v-for="(image, index) in previewImages" :key="image.id" class="relative">
                                        <img :src="image.src" alt="Preview"
                                            class="w-full h-40 object-cover rounded-lg" />
                                        <button type="button" @click="removePreviewImage(index)"
                                            class="absolute top-2 right-2 bg-white/80 text-gray-700 rounded-full p-1 hover:bg-white transition-colors">
                                            <i class='bx bx-x text-xl'></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3 pt-4">
                                <button type="submit"
                                    class="flex-1 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors flex items-center justify-center disabled:opacity-70 disabled:cursor-not-allowed"
                                    :disabled="isSubmitting">
                                    <i v-if="isSubmitting" class='bx bx-loader-alt mr-2 animate-spin'></i>
                                    <span>{{ isSubmitting ? 'Guardando...' : 'Guardar cambios' }}</span>
                                </button>
                                <button type="button" @click="cancelEdit"
                                    class="flex-1 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center"
                                    :disabled="isSubmitting">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
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