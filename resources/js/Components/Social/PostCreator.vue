<script setup>
import { ref, onMounted } from "vue";
import { router } from '@inertiajs/vue3';
import { QuillEditor, Quill } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import DOMPurify from "dompurify";
import ResponsabilidadModal from './ResponsabilidadModal.vue';

const props = defineProps({
    auth: Object,
    group: Object,
});

const emit = defineEmits(['postCreated']);

const quillEditorRef = ref(null);
const fileInputRef = ref(null);
const content = ref("");
const images = ref([]);
const imagePreviews = ref([]);
const showConfirmModal = ref(false);
const charCount = ref(0);

onMounted(() => {
    const Link = Quill.import('formats/link');
    class CustomLink extends Link {
        static create(value) {
            const node = super.create(value);
            node.setAttribute('style', 'text-decoration: underline; color: #007bff;');
            node.setAttribute('target', '_blank');
            node.setAttribute('rel', 'noopener noreferrer');
            return node;
        }
    }
    Quill.register('formats/link', CustomLink, true);
    
    // Configurar el comportamiento de desplazamiento del editor
    if (quillEditorRef.value) {
        const editor = quillEditorRef.value.getQuill();
        editor.root.setAttribute('data-gramm', 'false'); // Desactivar Grammarly que puede causar problemas
        
        // Asegurarse de que el editor no crezca indefinidamente
        editor.root.style.overflow = 'auto';
        editor.root.style.maxHeight = '200px'; // Altura máxima antes de mostrar scroll
    }
});

const editorOptions = {
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            ['link'],
        ]
    },
    placeholder: props.group && props.group.id
        ? 'Comparte algo con el grupo..'
        : '¿Qué quieres compartir hoy?',
    theme: 'snow',
    bounds: '.quill-editor-container' // Limitar el editor al contenedor
};

// Limitar el contenido a 1000 caracteres (sin etiquetas HTML)
const handleEditorChange = (newContent) => {
    // Elimina etiquetas HTML para contar solo texto plano
    const plainText = newContent.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
    charCount.value = plainText.length;
    
    if (plainText.length > 2000) {
        // Si supera el límite, recorta el texto y actualiza el contenido
        const truncated = plainText.slice(0, 2000);
        // Actualiza el contenido del editor solo con el texto permitido
        if (quillEditorRef.value) {
            const editor = quillEditorRef.value.getQuill();
            const currentSelection = editor.getSelection();
            editor.setText(truncated);
            
            // Restaurar la selección si es posible
            if (currentSelection) {
                editor.setSelection(Math.min(currentSelection.index, truncated.length));
            }
        }
        content.value = truncated;
        charCount.value = 2000;
    } else {
        content.value = newContent;
    }
};

const handleFileChange = (event) => {
    const files = event.target.files;
    if (!files || files.length === 0) return;
    
    Array.from(files).forEach(file => {
        if (!file.type.startsWith('image/')) return;
        
        images.value.push(file);
        
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviews.value.push({
                id: Date.now() + Math.random().toString(36).substr(2, 9), 
                src: e.target.result,
                file: file
            });
        };
        reader.readAsDataURL(file);
    });
    
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};

const removeImage = (index) => {
    imagePreviews.value.splice(index, 1);
    images.value.splice(index, 1);
};

// Función para abrir el modal de confirmación
const openConfirmModal = () => {
    if (!content.value.trim() && images.value.length === 0) return;
    showConfirmModal.value = true;
};

// Función para cerrar el modal
const closeConfirmModal = () => {
    showConfirmModal.value = false;
};

// Función para manejar cuando el post ha sido enviado desde el modal
const handlePostSubmitted = (data) => {
    // Emitir el evento al componente padre
    emit('postCreated', data);
};

// Función para resetear el formulario
const resetForm = () => {
    content.value = '';
    images.value = [];
    imagePreviews.value = [];
    charCount.value = 0;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
    if (quillEditorRef.value) {
        quillEditorRef.value.setHTML('');
    }
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 quill-editor-container">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                <img v-if="auth.user.profile && auth.user.profile.profile_picture"
                    :src="auth.user.profile.profile_picture || '/images/default-avatar.jpg'" alt="Tu avatar"
                    class="w-full h-full object-cover" />
                <div v-else
                    class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-[#193CB8] dark:text-[#8fa2f7]">
                    <i class='bx bx-user'></i>
                </div>
            </div>
            <div class="flex-1 editor-wrapper">
                <div class="mb-3 quill-wrapper">
                    <QuillEditor
                        ref="quillEditorRef"
                        v-model:content="content"
                        :options="editorOptions"
                        contentType="html"
                        @update:content="handleEditorChange"
                        class="quill-fixed-height"
                    />
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-right">
                        {{ charCount }} / 1000 caracteres
                    </div>
                </div>
                
                <div v-if="imagePreviews.length > 0" class="mt-3 mb-3">
                    <div class="image-preview-grid">
                        <div v-for="(preview, index) in imagePreviews" :key="preview.id" class="image-preview-item">
                            <img :src="preview.src" alt="Vista previa" class="rounded-lg object-cover w-full h-full" />
                            <button @click="removeImage(index)"
                                class="absolute top-2 right-2 bg-gray-800/70 dark:bg-gray-900/80 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-gray-900/70 transition-colors">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-3">
                    <div class="flex gap-3">
                        <label
                            class="flex-1 py-1 flex items-center justify-center cursor-pointer text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                            <i class='bx bx-image-alt mr-1 text-blue-500'></i> Foto
                            <input type="file" class="hidden" ref="fileInputRef" @change="handleFileChange"
                                accept="image/*" multiple />
                        </label>
                        <label
                            class="flex-1 py-1 flex items-center justify-center cursor-pointer text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                            <input type="file" class="hidden" @change="handleFileChange" accept="video/*" />
                        </label>
                        <button @click="router.get('/eventos/nuevo')"
                            class="flex-1 py-1 flex items-center justify-center cursor-pointer text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                            <i class='bx bx-calendar-event mr-1 text-orange-500'></i> Evento
                        </button>
                    </div>

                    <button @click="openConfirmModal" :disabled="!content && imagePreviews.length === 0"
                        :class="{ 'opacity-50 cursor-not-allowed': !content && imagePreviews.length === 0 }"
                        class="px-4 py-1.5 bg-[#193CB8] dark:bg-[#2536a7] text-white rounded-lg hover:bg-[#142d8c] dark:hover:bg-[#1a255e] transition-colors text-sm">
                        Publicar
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación -->
        <ResponsabilidadModal 
            v-if="showConfirmModal"
            :auth="auth"
            :group="group"
            :content="content"
            :images="images"
            :imagePreviews="imagePreviews"
            @closeModal="closeConfirmModal"
            @postSubmitted="handlePostSubmitted"
            @resetForm="resetForm"
        />
    </div>
</template>

<style scoped>
/* Contenedor principal del editor */
.editor-wrapper {
    position: relative;
    width: 100%;
}

/* Contenedor del editor Quill */
.quill-wrapper {
    position: relative;
    width: 100%;
}

/* Estilos para el editor Quill */
:deep(.ql-editor) {
    min-height: 100px;
    max-height: 200px;
    overflow-y: auto !important;
    word-break: break-word;
    background-color: #ffffff; /* Light mode background */
    color: #000000; /* Light mode text */
}

.dark :deep(.ql-editor) {
    background-color: #181a20; /* Dark mode background */
    color: #e4e4e7; /* Dark mode text */
}

:deep(.ql-container) {
    overflow: visible;
    display: flex;
    flex-direction: column;
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
    border-color: #d1d5db;
}

:deep(.ql-toolbar) {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
    border-color: #d1d5db;
    z-index: 10;
    border: 1px solid #d1d5db;
    background-color: #f9fafb;
}

.dark :deep(.ql-toolbar) {
    border-color: #4b5563;
    background-color: #2a2f38;
}

/* Evitar que el editor crezca indefinidamente */
.quill-fixed-height {
    display: flex;
    flex-direction: column;
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

.ring-highlight {
    box-shadow: 0 0 0px 0px rgba(25, 60, 184, 0);
    transition: box-shadow 0.4s ease-in-out;
}

.ring-active {
    box-shadow: 0 0 12px 5px rgba(25, 60, 184, 0.6);
}
</style>