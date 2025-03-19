`
<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from "vue";
import { router, usePage } from '@inertiajs/vue3';
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import DOMPurify from "dompurify";
import PostCard from "@/Components/Social/PostCard.vue";
const props = defineProps({
    auth: Object,
    isMember: Boolean,
    group: Object,
});

const quillEditorRef = ref(null);
const fileInputRef = ref(null);
const loadMoreTrigger = ref(null);

const content = ref("");
const image = ref(null);
const imagePreview = ref(null);
const currentPage = ref(1);
const isLoadingMore = ref(false);
const observer = ref(null);
const hasMorePosts = ref(true);
const groupSlug = ref('');

const displayedPosts = computed(() => {
    return props.group.posts || [];
});

// Watch para actualizar el estado
watch(() => props.group, (newGroup) => {
    if (newGroup) {
        hasMorePosts.value = newGroup.has_more_posts === true;


        if (newGroup.slug) {
            groupSlug.value = newGroup.slug;
        }


    }
}, { immediate: true });

// Editor options
const editorOptions = {
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            [{ 'indent': '-1' }, { 'indent': '+1' }],
            [{ 'align': [] }],
            ['link'],
            ['clean']
        ]
    },
    placeholder: 'Comparte algo con el grupo...',
    theme: 'snow'
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const sanitizeHTML = (html) => {
    return DOMPurify.sanitize(html);
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (!file) {
        image.value = null;
        imagePreview.value = null;
        return;
    }

    image.value = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const removeImage = () => {
    image.value = null;
    imagePreview.value = null;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};

const submitPost = () => {
    const sanitizedContent = DOMPurify.sanitize(content.value);

    if (!sanitizedContent.trim() && !image.value) return;

    const formData = new FormData();
    formData.append('content', sanitizedContent);
    if (image.value) {
        formData.append('image', image.value);
    }

    router.post(`/grupos/${props.group.id}/posts`, formData, {
        onSuccess: () => {
            content.value = '';
            image.value = null;
            imagePreview.value = null;
            if (fileInputRef.value) {
                fileInputRef.value.value = '';
            }
            if (quillEditorRef.value) {
                quillEditorRef.value.setHTML('');
            }
            nextTick(() => {
                window.scrollTo(0, 0);
            });
        }
    });
};


const setupIntersectionObserver = () => {
    if (observer.value) {
        observer.value.disconnect();
    }

    observer.value = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && !isLoadingMore.value && hasMorePosts.value) {
            loadMorePosts();
        }
    }, { threshold: 0.1 });

    if (loadMoreTrigger.value) {
        observer.value.observe(loadMoreTrigger.value);
    }
};

const loadMorePosts = () => {
    if (isLoadingMore.value || !hasMorePosts.value || !groupSlug.value) return;

    isLoadingMore.value = true;
    const nextPage = currentPage.value + 1;

    router.visit(`/grupos/${groupSlug.value}?page=${nextPage}`, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: () => {
            currentPage.value = nextPage;
            isLoadingMore.value = false;


            setTimeout(() => {
                window.history.replaceState({}, '', `/grupos/${groupSlug.value}`);
            }, 0);

            nextTick(() => {
                if (loadMoreTrigger.value && observer.value) {
                    observer.value.observe(loadMoreTrigger.value);
                }
            });
        },
        onError: (errors) => {
            console.error('Error loading more posts:', errors);
            isLoadingMore.value = false;
            setTimeout(() => {
                window.history.replaceState({}, '', `/grupos/${groupSlug.value}`);
            }, 0);
        }


    });
};

onMounted(() => {
    nextTick(() => {
        setupIntersectionObserver();
    });
});

onUnmounted(() => {
    if (observer.value) {
        observer.value.disconnect();
    }
});
</script>

<template>
    <div class="tab-content">
        <div v-if="isMember" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                    <img v-if="auth.user.profile && auth.user.profile.profile_picture"
                        :src="auth.user.profile.profile_picture || '/images/default-avatar.jpg'" alt="Tu avatar"
                        class="w-full h-full object-cover" />
                    <div v-else
                        class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-[#193CB8]">
                        <i class='bx bx-user'></i>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="mb-3">
                        <QuillEditor ref="quillEditorRef" v-model:content="content" :options="editorOptions"
                            contentType="html" />
                    </div>
                    <div v-if="imagePreview" class="mt-3 mb-3 relative">
                        <img :src="imagePreview" alt="Vista previa" class="rounded-lg max-h-48 object-contain" />
                        <button @click="removeImage"
                            class="absolute top-2 right-2 bg-gray-800/70 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-gray-900/70 transition-colors">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>

                    <div class="flex justify-between items-center mt-3">
                        <div class="flex gap-2">
                            <input type="file" @change="handleFileChange" class="hidden" id="fileUpload"
                                ref="fileInputRef" accept="image/*" />
                            <label for="fileUpload"
                                class="p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-colors cursor-pointer">
                                <i class="bx bx-image-alt"></i>
                            </label>
                        </div>
                        <button @click="submitPost" :disabled="!content && !image"
                            :class="{ 'opacity-50 cursor-not-allowed': !content && !image }"
                            class="px-4 py-1.5 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors text-sm">
                            Publicar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="displayedPosts.length > 0" class="space-y-4 mt-4">
            <PostCard v-for="post in displayedPosts" :key="post.id" :post="post" :formatDate="formatDate"
                :sanitizeHTML="sanitizeHTML" :isMember="isMember" :auth="auth" />

            <div v-if="isLoadingMore" class="text-center py-4">
                <div
                    class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#193CB8] border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]">
                </div>
            </div>

            <div ref="loadMoreTrigger" class="h-10"></div>

            <div v-if="!hasMorePosts && displayedPosts.length > 0" class="text-center pb-4">
                <p class="text-gray-500 text-lg font-semibold">
                    No hay más publicaciones.
                    <span @click="scrollToTop" class="text-[#193CB8] underline cursor-pointer">
                        ¡Publica algo!
                    </span>
                </p>
            </div>
        </div>

        <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center mt-4">
            <div class="py-8">
                <i class="bx bx-message-square-detail text-5xl text-gray-300 mb-2"></i>
                <h3 class="text-lg font-medium text-gray-700 mb-1">No hay publicaciones aún</h3>
                <p class="text-gray-500">Sé el primero en compartir algo con el grupo</p>
            </div>
        </div>


    </div>
</template>
<style scoped>
:deep(.ql-editor) {
    min-height: 100px;
}

:deep(.ql-toolbar) {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
    border-color: #d1d5db;
}

:deep(.ql-container) {
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
    border-color: #d1d5db;
}

:deep(.ql-toolbar .ql-stroke) {
    stroke: #6b7280;
}

:deep(.ql-toolbar .ql-fill) {
    fill: #6b7280;
}

:deep(.ql-toolbar .ql-picker) {
    color: #6b7280;
}

:deep(.ql-toolbar button:hover .ql-stroke) {
    stroke: #193CB8;
}

:deep(.ql-toolbar button.ql-active .ql-stroke) {
    stroke: #193CB8;
}

:deep(.ql-toolbar button:hover .ql-fill) {
    fill: #193CB8;
}

:deep(.ql-toolbar button.ql-active .ql-fill) {
    fill: #193CB8;
}

:deep(.ql-toolbar .ql-picker-label:hover) {
    color: #193CB8;
}

:deep(.ql-toolbar .ql-picker-label.ql-active) {
    color: #193CB8;
}

:deep(.ql-toolbar .ql-picker-item:hover) {
    color: #193CB8;
}

:deep(.ql-toolbar .ql-picker-item.ql-selected) {
    color: #193CB8;
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