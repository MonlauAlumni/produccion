<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" :data-post-id="post.id">
    <div class="flex items-start gap-3">
      <!-- Avatar -->
      <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 cursor-pointer"
        @click="router.get(`/perfil/${post.user.profile.slang}`)">
        <img v-if="post.user.profile && post.user.profile.profile_picture"
          :src="post.user.profile.profile_picture || '/images/default-avatar.jpg'" :alt="post.user.name"
          class="w-full h-full object-cover" />
        <div v-else
          class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-[#193CB8] shadow-sm border-2 border-white">
          <i class='bx bxs-user text-xl'></i>
        </div>
      </div>

      <div class="flex-1 overflow-hidden"> <!-- Añadido overflow-hidden aquí -->
        <!-- Post Header -->
        <div class="flex items-center justify-between">
          <div class="overflow-hidden"> <!-- Añadido overflow-hidden aquí -->
            <h3 class="font-medium text-gray-800 hover:underline cursor-pointer truncate" <!-- Añadido truncate -->
              @click="router.get(`/perfil/${post.user.profile.slang}`)">
              {{ post.user.name }} {{ post.user.last_name_1 }} {{ post.user.last_name_2 ? post.user.last_name_2 : '' }}
            </h3>
            <p class="text-xs text-gray-500">{{ formatDate(post.created_at) }}</p>
          </div>
          <button class="p-1 text-gray-400 hover:text-gray-600 cursor-pointer flex-shrink-0"> <!-- Añadido flex-shrink-0 -->
            <i class="bx bx-dots-horizontal-rounded"></i>
          </button>
        </div>

        <!-- Post Content -->
        <div class="mt-2 overflow-hidden"> <!-- Añadido overflow-hidden aquí -->
          <div class="text-gray-700 post-content break-words" v-html="sanitizeHTML(post.content)"
            :style="{ fontSize: user_settings.font_size + 'px' }"></div>

          <!-- Image Carousel -->
          <div v-if="post.images && post.images.length > 0" class="mt-3 relative">
            <div class="image-carousel overflow-hidden relative w-full h-[300px]">
              <div class="flex transition-transform duration-500 h-full"
                :style="{ transform: `translateX(-${currentImageIndex * 100}%)` }">
                <img v-for="(image, index) in post.images" :key="index" :src="image.image_path" alt="Post image"
                  class="flex-shrink-0 object-cover mx-auto min-w-full" />
              </div>
            </div>

            <!-- Flechas de navegación -->
            <template v-if="post.images.length > 1">
              <button @click="prevImage"
                class="nav-arrow absolute cursor-pointer left-2 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full w-8 h-8 flex items-center justify-center shadow-md">
                <i class="bx bx-chevron-left text-xl"></i>
              </button>
              <button @click="nextImage"
                class="nav-arrow absolute cursor-pointer right-2 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full w-8 h-8 flex items-center justify-center shadow-md">
                <i class="bx bx-chevron-right text-xl"></i>
              </button>

              <!-- Indicadores (dots) -->
              <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 flex space-x-1.5">
                <button v-for="(image, index) in post.images" :key="index" @click="setImage(index)"
                  class="w-2 h-2 rounded-full transition-all duration-200 cursor-pointer"
                  :class="currentImageIndex === index ? 'bg-white scale-125' : 'bg-white/50 hover:bg-white/70'"></button>
              </div>
            </template>
          </div>
        </div>

        <!-- Post Actions -->
        <div class="flex items-center gap-4 mt-4 pt-2 border-t border-gray-100">
          <button @click="likePost" class="flex items-center gap-1 cursor-pointer"
            :class="isLiked ? 'text-[#193CB8]' : 'text-gray-500 hover:text-[#193CB8]'">
            <i class="bx" :class="isLiked ? 'bxs-like' : 'bx-like'"></i>
            <span class="text-sm">{{ post.likes_count }}</span>
          </button>
          <button class="flex items-center gap-1 text-gray-500 hover:text-[#193CB8] cursor-pointer"
            @click="focusCommentInput">
            <i class="bx bx-comment"></i>
            <span class="text-sm">{{ post.comments_count }}</span>
          </button>
          <!-- <button class="flex items-center gap-1 text-gray-500 hover:text-[#193CB8] cursor-pointer">
            <i class="bx bx-share"></i>
            <span class="text-sm">Compartir</span>
          </button> -->
        </div>

        <!-- Comments Section -->
        <div v-if="post.comments && post.comments.length > 0" class="mt-4 pt-2 border-t border-gray-100 space-y-3 overflow-hidden"> <!-- Añadido overflow-hidden -->
          <PostComment v-for="comment in post.comments" :key="comment.id" :comment="comment" />
        </div>

        <!-- Add Comment (Only if Member) -->
        <div v-if="isMember" class="mt-3 flex items-center gap-2">
          <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0">
            <img :src="auth?.user?.profile?.profile_picture || '/images/default-avatar.jpg'" alt="Tu avatar"
              class="w-full h-full object-cover" />
          </div>
          <div class="flex-1 relative">
            <input type="text" placeholder="Escribe un comentario..." v-model="commentText" @keyup.enter="addComment"
              ref="commentInput"
              class="w-full px-3 py-1.5 border border-gray-300 rounded-full focus:ring-2 focus:ring-[#193CB8] focus:border-[#193CB8] outline-none transition-colors pr-10" />
            <button class="absolute right-2 top-5 transform -translate-y-1/2 text-[#193CB8] cursor-pointer"
              @click="addComment">
              <i class="bx bx-send"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, onMounted, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import DOMPurify from 'dompurify';
import PostComment from './PostComment.vue';

const props = defineProps({
  post: Object,
  formatDate: Function,
  isMember: Boolean,
  auth: Object
});

const page = usePage();

const user_settings = page.props.auth.user_settings;

const commentText = ref('');
const commentInput = ref(null);
const isLiked = ref(props.post.is_liked);
const currentImageIndex = ref(0);
const slideDirection = ref('slide-right');
const postOptionsModal = ref(false);

// Reset current image index when post changes
watch(() => props.post.id, () => {
  currentImageIndex.value = 0;
});

const sanitizeHTML = (html) => {
  // Configuración para asegurar que los enlaces largos no rompan el diseño
  const config = {
    ADD_ATTR: ['target', 'rel'],
    ADD_TAGS: ['iframe'],
    FORBID_TAGS: ['style', 'script'],
    FORBID_ATTR: ['style', 'onerror', 'onload'],
  };
  
  let sanitizedHTML = DOMPurify.sanitize(html, config);

  // Asegurar que los enlaces tengan http/https
  sanitizedHTML = sanitizedHTML.replace(/href="([^"]+)"/g, (match, p1) => {
    if (!/^https?:\/\//i.test(p1)) {
      p1 = 'http://' + p1;
    }
    return `href="${p1}" target="_blank" rel="noopener noreferrer"`;
  });

  return sanitizedHTML;
};

const nextImage = () => {
  if (!props.post.images || props.post.images.length <= 1) return;
  slideDirection.value = 'slide-left';

  if (currentImageIndex.value === props.post.images.length - 1) {
    slideDirection.value = 'slide-right';
  }
  currentImageIndex.value = (currentImageIndex.value + 1) % props.post.images.length;
};

const prevImage = () => {
  if (!props.post.images || props.post.images.length <= 1) return;
  slideDirection.value = 'slide-right';

  if (currentImageIndex.value === 0) {
    slideDirection.value = 'slide-left';
  }
  currentImageIndex.value = (currentImageIndex.value - 1 + props.post.images.length) % props.post.images.length;
};


const addComment = () => {
  if (!commentText.value.trim()) return;

  const sanitizedContent = DOMPurify.sanitize(commentText.value, {
    ALLOWED_TAGS: [],
    ALLOWED_ATTR: []
  });

  const formData = new FormData();
  formData.append('content', sanitizedContent);

  const commentUrl = props.post.group_id
    ? `/grupos/${props.post.group_id}/posts/${props.post.id}/comment`
    : `/posts/${props.post.id}/comment`;

  router.post(commentUrl, formData, {
    preserveScroll: true,
    preserveState: false,
    onSuccess: () => {
      commentText.value = '';
      nextTick(() => {
        document.querySelector('.post-content')?.scrollIntoView({ behavior: 'smooth' });
      });
    }
  });
};

const likePost = () => {
  props.post.likes_count += isLiked.value ? -1 : 1;
  isLiked.value = !isLiked.value;
  router.post(`/posts/${props.post.id}/like`, {}, {
    preserveScroll: true,
    onSuccess: () => {
    }
  });
};

const setImage = (index) => {
  currentImageIndex.value = index;
};

const focusCommentInput = () => {
  if (commentInput.value) {
    commentInput.value.focus();
  }
};

const showPostOptions = () => {
  postOptionsModal.value = true;
};
</script>

<style scoped>
.image-carousel {
  border-radius: 0.5rem;
}

.nav-arrow {
  opacity: 0.8;
  transition: opacity 0.2s ease, background-color 0.2s ease;
  z-index: 10;
}

.nav-arrow:hover {
  opacity: 1;
}

/* Estilos para asegurar que el contenido del post no se desborde */
.post-content {
  overflow-wrap: break-word;
  word-wrap: break-word;
  word-break: break-word;
  hyphens: auto;
  max-width: 100%;
}

/* Asegurar que las imágenes y otros elementos embebidos no se desborden */
:deep(.post-content img),
:deep(.post-content iframe),
:deep(.post-content video),
:deep(.post-content embed),
:deep(.post-content object) {
  max-width: 100%;
  height: auto;
}

/* Asegurar que los enlaces largos no rompan el diseño */
:deep(.post-content a) {
  word-break: break-all;
  color: #193CB8;
  text-decoration: underline;
}

/* Estilos para los bloques de código o texto preformateado */
:deep(.post-content pre),
:deep(.post-content code) {
  white-space: pre-wrap;
  word-wrap: break-word;
  overflow-x: auto;
  max-width: 100%;
  display: block;
  padding: 0.5rem;
  background-color: #f8f9fa;
  border-radius: 0.25rem;
}
</style>