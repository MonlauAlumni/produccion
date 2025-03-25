<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from "vue";
import { router, usePage } from '@inertiajs/vue3';
import { QuillEditor, Quill } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import DOMPurify from "dompurify";
import PostCard from "@/Components/Social/PostCard.vue";
import PostCreator from '@/Components/Social/PostCreator.vue';

const props = defineProps({
    auth: Object,
    isMember: Boolean,
    group: Object,
});

const quillEditorRef = ref(null);
const fileInputRef = ref(null);
const loadMoreTrigger = ref(null);
const windowScrollY = ref(0);
const newPostId = ref(null);

const content = ref("");
const images = ref([]);
const imagePreviews = ref([]);
const currentPage = ref(1);
const isLoadingMore = ref(false);
const observer = ref(null);
const hasMorePosts = ref(true);
const groupSlug = ref('');

const updateScroll = () => {
    windowScrollY.value = window.scrollY;
};

const displayedPosts = computed(() => {
    return props.group.posts || [];
});

watch(() => props.group, (newGroup) => {
    if (newGroup) {
        hasMorePosts.value = newGroup.has_more_posts === true;
        if (newGroup.slug) {
            groupSlug.value = newGroup.slug;
        }
    }
}, { immediate: true });

onMounted(() => {
    window.addEventListener('scroll', updateScroll);
    const Link = Quill.import('formats/link');
    class CustomLink extends Link {
        static create(value) {
            const node = super.create(value);
            node.setAttribute('style', 'text-decoration: underline; color: #007bff;');
            node.setAttribute('target', '_blank');
            node.setAttribute('rel', 'noopener noreferrer'); // Security best practice
            return node;
        }
    }
    Quill.register('formats/link', CustomLink, true);

    nextTick(() => {
        setupIntersectionObserver();
    });
});

const editorOptions = {
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            ['link'],
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
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric'
    });
};

const scrollToTop = () => {
    const quillEditorDiv = document.querySelector('.quill-editor-container');
    if (quillEditorDiv) {
        quillEditorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => {
            highlightQuillEditor();
            router.visit(`/grupos/${groupSlug.value}`, {
                preserveState: true,
                preserveScroll: true,
                replace: true
            });
        }, 1000);
    }
};

const goUp = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    router.visit(`/grupos/${groupSlug.value}`, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};

const highlightQuillEditor = () => {
    const quillEditorDiv = document.querySelector('.quill-editor-container');
    if (quillEditorDiv) {
        quillEditorDiv.classList.add('ring-highlight', 'ring-active');
        setTimeout(() => {
            quillEditorDiv.classList.remove('ring-active');
        }, 1650);
    }
};

const highlightNewPost = () => {
    if (!newPostId.value) return;

    const newPostElement = document.querySelector(`[data-post-id="${newPostId.value}"]`);
    if (newPostElement) {
        newPostElement.classList.add('ring-highlight', 'ring-active');
        setTimeout(() => {
            newPostElement.classList.remove('ring-active');
        }, 1650);
    }
};

const handleFileChange = (event) => {
    const files = event.target.files;
    if (!files || files.length === 0) return;

    // Process each image file
    Array.from(files).forEach(file => {
        if (!file.type.startsWith('image/')) return;

        // Add to images array
        images.value.push(file);

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviews.value.push({
                id: Date.now() + Math.random().toString(36).substr(2, 9), // Generate unique ID
                src: e.target.result,
                file: file
            });
        };
        reader.readAsDataURL(file);
    });

    // Reset the input to allow selecting the same file again
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};

const removeImage = (index) => {
    imagePreviews.value.splice(index, 1);
    images.value.splice(index, 1);
};

const submitPost = () => {
    const purifyConfig = {
        ALLOWED_ATTR: ['href', 'target', 'style', 'rel'],
        ADD_ATTR: ['target', 'rel']
    };

    let sanitizedContent = DOMPurify.sanitize(content.value, purifyConfig);

    if (!sanitizedContent.trim() && images.value.length === 0) return;

    const formData = new FormData();
    formData.append('content', sanitizedContent);

    // Add group_id if we're in a group
    if (props.group && props.group.id) {
        formData.append('group_id', props.group.id);
    }

    // Append all images to the form data
    if (images.value.length > 0) {
        images.value.forEach((image, index) => {
            formData.append(`images[]`, image);
        });
    }

    // Use the new route for posts
    const postUrl = props.group && props.group.id
        ? `/posts/group/${props.group.id}`
        : '/posts';

    router.post(postUrl, formData, {
        onSuccess: (page) => {
            // Reset form
            content.value = '';
            images.value = [];
            imagePreviews.value = [];
            if (fileInputRef.value) {
                fileInputRef.value.value = '';
            }
            if (quillEditorRef.value) {
                quillEditorRef.value.setHTML('');
            }

            // Get the ID of the newly created post (assuming it's the first post in the list)
            if (page.props.group && page.props.group.posts && page.props.group.posts.length > 0) {
                newPostId.value = page.props.group.posts[0].id;
            }

            // Wait for the DOM to update with the new post
            nextTick(() => {
                // Scroll to the new post
                const newPostElement = document.querySelector(`[data-post-id="${newPostId.value}"]`);
                if (newPostElement) {
                    newPostElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    // Highlight the new post
                    highlightNewPost();
                }
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

onUnmounted(() => {
    window.removeEventListener('scroll', updateScroll);
    if (observer.value) {
        observer.value.disconnect();
    }
});
</script>

<template>
    <button v-show="windowScrollY > 200" @click="goUp"
        class="fixed cursor-pointer bottom-6 right-6 w-12 h-12 rounded-full bg-[#193CB8] text-white shadow-lg flex items-center justify-center hover:bg-[#142d8c] transition-all duration-300 z-50 animate-fade-in">
        <i class='bx bx-chevron-up text-xl'></i>
    </button>
    <div class="tab-content">

        <PostCreator v-if="isMember"
         :auth="auth" 
         :group="group" 
         @postCreated="handlePostCreated" 
         />

        <div v-if="displayedPosts.length > 0" class="space-y-4 mt-4">
            <PostCard v-for="post in displayedPosts" :key="post.id" :post="post" :formatDate="formatDate"
                :isMember="isMember" :auth="auth" :data-post-id="post.id" />

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

:deep(.ql-editor a) {
    color: #007bff;
    text-decoration: underline;
}

/* Image preview grid */
.image-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 8px;
}

.image-preview-item {
    position: relative;
    height: 120px;
    border-radius: 0.5rem;
    overflow: hidden;
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

.ring-highlight {
    box-shadow: 0 0 0px 0px rgba(25, 60, 184, 0);
    transition: box-shadow 0.4s ease-in-out;
}

.ring-active {
    box-shadow: 0 0 12px 5px rgba(25, 60, 184, 0.6);
}

@keyframes highlight-pulse {
    0% {
        background-color: transparent;
    }

    50% {
        background-color: rgba(255, 255, 255, 0.9);
    }

    100% {
        background-color: transparent;
    }
}

[data-post-id].ring-active {
    animation: highlight-pulse 1.65s ease-in-out;
}
</style>