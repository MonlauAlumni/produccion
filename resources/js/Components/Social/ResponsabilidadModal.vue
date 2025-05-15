<script setup>
import { ref } from "vue";
import { router } from '@inertiajs/vue3';
import DOMPurify from "dompurify";

const props = defineProps({
    auth: Object,
    group: Object,
    content: String,
    images: Array,
    imagePreviews: Array,
});

const emit = defineEmits(['closeModal', 'postSubmitted', 'resetForm']);

const showModal = ref(true);
const isLoading = ref(false);

const closeModal = () => {
    showModal.value = false;
    emit('closeModal');
};

const confirmPost = () => {
    isLoading.value = true;

    const purifyConfig = {
        ALLOWED_ATTR: ['href', 'target', 'style', 'rel'],
        ADD_ATTR: ['target', 'rel']
    };

    let sanitizedContent = DOMPurify.sanitize(props.content, purifyConfig);

    if (!sanitizedContent.trim() && props.images.length === 0) {
        isLoading.value = false;
        closeModal();
        return;
    }

    const formData = new FormData();
    formData.append('content', sanitizedContent);

    if (props.group && props.group.id) {
        formData.append('group_id', props.group.id);
    }

    if (props.images.length > 0) {
        props.images.forEach((image) => {
            formData.append(`images[]`, image);
        });
    }

    const postUrl = props.group && props.group.id
        ? `/posts/group/${props.group.id}`
        : '/posts';

    router.post(postUrl, formData, {
        onSuccess: (page) => {
            isLoading.value = false;
            closeModal();

            let newPostId = null;
            if (page.props.group && page.props.group.posts && page.props.group.posts.length > 0) {
                newPostId = page.props.group.posts[0].id;
            }

            emit('postSubmitted', { postId: newPostId, page });
            emit('resetForm');
        },
        onError: () => {
            isLoading.value = false;
        }
    });
};
</script>

<template>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50" @click="closeModal"></div>

        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 z-10 overflow-hidden transform transition-all">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Confirmación de publicación</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class='bx bx-x text-2xl'></i>
                    </button>
                </div>

                <div class="mb-6">
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-100 mb-4">
                        <p class="text-gray-800 text-sm">
                            Al publicar este contenido, confirmas que:
                        </p>
                        <ul class="list-disc pl-5 mt-2 text-sm text-gray-700 space-y-1">
                            <li>Te responsabilizas completamente del contenido que estás compartiendo</li>
                            <li>No contiene material ofensivo, ilegal o inapropiado</li>
                            <li>
                                Cumple con los 
                                <a href="/terminos-y-condiciones" target="_blank" rel="noopener" class="text-blue-600 underline hover:text-blue-800">
                                    Términos y Condiciones
                                </a>
                                de nuestra plataforma
                            </li>
                        </ul>
                    </div>
                    <p class="text-sm text-gray-600">
                        ¿Estás seguro de que deseas continuar con esta publicación?
                    </p>
                </div>

                <div class="flex justify-end gap-3">
                    <button @click="closeModal"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                        Cancelar
                    </button>
                    <button @click="confirmPost" :disabled="isLoading"
                        class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors text-sm font-medium flex items-center">
                        <span v-if="isLoading" class="mr-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                        Acepto y publico
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>