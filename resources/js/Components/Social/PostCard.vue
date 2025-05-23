
<template>
  <div
    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
    :data-post-id="post.id"
  >
    <div class="flex items-start gap-3">
      <div
        class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 cursor-pointer"
        @click="router.get(`/perfil/${post.user.profile.slang}`)"
      >
        <img
          v-if="post.user.profile && post.user.profile.profile_picture"
          :src="post.user.profile.profile_picture || '/images/default-avatar.jpg'"
          :alt="post.user.name"
          class="w-full h-full object-cover"
        />
        <div
          v-else
          class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-[#193CB8] dark:text-[#7ea2ff] shadow-sm border-2 border-white dark:border-gray-800"
        >
          <i class="bx bxs-user text-xl"></i>
        </div>
      </div>

      <div class="flex-1 overflow-hidden">
        <div class="flex items-center justify-between">
          <div class="overflow-hidden">
            <h3
              class="font-medium text-gray-800 dark:text-gray-100 hover:underline cursor-pointer truncate"
              @click="router.get(`/perfil/${post.user.profile.slang}`)"
            >
              {{ post.user.name }} {{ post.user.last_name_1 }} {{ post.user.last_name_2 ? post.user.last_name_2 : '' }}
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(post.created_at) }}</p>
          </div>
          <button
            class="p-1 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer flex-shrink-0"
            @click.stop="showPostOptions"
          >
            <div class="relative">
              <i
                v-if="auth?.user?.id === post.user.id"
                class="bx bx-dots-horizontal-rounded"
                ref="optionsButtonRef"
              ></i>
              <div
                v-if="postOptionsModal"
                class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-20"
                ref="optionsMenuRef"
              >
                <ul class="py-1">
                  <li>
                    <button
                      class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-red-600 dark:text-red-400"
                      @click="openDeletePostModal"
                    >
                      Eliminar
                    </button>
                  </li>
                </ul>
              </div>
            </div>
          </button>
        </div>

        <div class="mt-2 overflow-hidden">
          <div
            class="text-gray-700 dark:text-gray-200 post-content break-words"
            v-html="sanitizeHTML(post.content)"
            :style="{ fontSize: user_settings.font_size + 'px' }"
          ></div>

          <div v-if="post.images && post.images.length > 0" class="mt-3 relative">
            <div class="image-carousel overflow-hidden relative w-full h-[300px]">
              <div
                class="flex transition-transform duration-500 h-full"
                :style="{ transform: `translateX(-${currentImageIndex * 100}%)` }"
              >
                <img
                  v-for="(image, index) in post.images"
                  :key="index"
                  :src="image.image_path"
                  alt="Post image"
                  class="flex-shrink-0 object-cover mx-auto min-w-full"
                />
              </div>
            </div>

            <template v-if="post.images.length > 1">
              <button
                @click="prevImage"
                class="nav-arrow absolute cursor-pointer left-2 top-1/2 transform -translate-y-1/2 bg-white/80 dark:bg-gray-800/80 hover:bg-white dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-full w-8 h-8 flex items-center justify-center shadow-md"
              >
                <i class="bx bx-chevron-left text-xl"></i>
              </button>
              <button
                @click="nextImage"
                class="nav-arrow absolute cursor-pointer right-2 top-1/2 transform -translate-y-1/2 bg-white/80 dark:bg-gray-800/80 hover:bg-white dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-full w-8 h-8 flex items-center justify-center shadow-md"
              >
                <i class="bx bx-chevron-right text-xl"></i>
              </button>

              <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 flex space-x-1.5">
                <button
                  v-for="(image, index) in post.images"
                  :key="index"
                  @click="setImage(index)"
                  class="w-2 h-2 rounded-full transition-all duration-200 cursor-pointer"
                  :class="currentImageIndex === index ? 'bg-white dark:bg-gray-200 scale-125' : 'bg-white/50 dark:bg-gray-600/50 hover:bg-white/70 dark:hover:bg-gray-400/70'"
                ></button>
              </div>
            </template>
          </div>
        </div>

        <div class="flex items-center gap-4 mt-4 pt-2 border-t border-gray-100 dark:border-gray-800">
          <button
            @click="likePost"
            class="flex items-center gap-1 cursor-pointer"
            :class="isLiked ? 'text-[#193CB8] dark:text-[#7ea2ff]' : 'text-gray-500 dark:text-gray-400 hover:text-[#193CB8] dark:hover:text-[#7ea2ff]'"
          >
            <i class="bx" :class="isLiked ? 'bxs-like' : 'bx-like'"></i>
            <span class="text-sm">{{ post.likes_count }}</span>
          </button>
          <button
            class="flex items-center gap-1 text-gray-500 dark:text-gray-400 hover:text-[#193CB8] dark:hover:text-[#7ea2ff] cursor-pointer"
            @click="focusCommentInput"
          >
            <i class="bx bx-comment"></i>
            <span class="text-sm">{{ post.comments_count }}</span>
          </button>
          <!-- <button class="flex items-center gap-1 text-gray-500 hover:text-[#193CB8] cursor-pointer">
            <i class="bx bx-share"></i>
            <span class="text-sm">Compartir</span>
          </button> -->
        </div>

        <div
          v-if="post.comments && post.comments.length > 0"
          class="mt-4 pt-2 border-t border-gray-100 dark:border-gray-800 space-y-3 overflow-hidden"
        >
          <PostComment v-for="comment in post.comments" :key="comment.id" :comment="comment" />
        </div>

        <div v-if="isMember" class="mt-3 flex items-center gap-2">
          <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0">
            <img
              :src="auth?.user?.profile?.profile_picture || '/images/default-avatar.jpg'"
              alt="Tu avatar"
              class="w-full h-full object-cover"
            />
          </div>
          <div class="flex-1 relative">
            <input
              type="text"
              placeholder="Escribe un comentario..."
              v-model="commentText"
              @keyup.enter="addComment"
              ref="commentInput"
              class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-700 rounded-full focus:ring-2 focus:ring-[#193CB8] dark:focus:ring-[#7ea2ff] focus:border-[#193CB8] dark:focus:border-[#7ea2ff] outline-none transition-colors pr-10 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
            />
            <button
              class="absolute right-2 top-5 transform -translate-y-1/2 text-[#193CB8] dark:text-[#7ea2ff] cursor-pointer"
              @click="addComment"
            >
              <i class="bx bx-send"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <teleport to="body">
    <div v-if="postDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-sm w-full p-6 relative">
        <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" @click="postDeleteModal = false">
          <i class="bx bx-x text-2xl"></i>
        </button>
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Eliminar publicación</h2>
        <p class="text-gray-600 dark:text-gray-300 mb-4">¿Estás seguro de que deseas eliminar esta publicación? Esta acción no se puede deshacer.</p>
        <div class="flex justify-end gap-2">
          <button class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700" @click="postDeleteModal = false">
            Cancelar
          </button>
          <button
            class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800"
            @click="deletePost"
          >
            Eliminar
          </button>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { ref, nextTick, onMounted, watch, onUnmounted } from 'vue';
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
const optionsButtonRef = ref(null);
const optionsMenuRef = ref(null);

const postDeleteModal = ref(false);

const openDeletePostModal = () => {
    postOptionsModal.value = false;
    postDeleteModal.value = true;
};

const sanitizeHTML = (html) => {
  const config = {
    ADD_ATTR: ['target', 'rel'],
    ADD_TAGS: ['iframe'],
    FORBID_TAGS: ['style', 'script'],
    FORBID_ATTR: ['style', 'onerror', 'onload'],
  };

  let sanitizedHTML = DOMPurify.sanitize(html, config);

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

const deletePost = () => {
  router.delete(`/posts/${props.post.id}`, {
    onSuccess: () => {
      router.visit(window.location.pathname, { 
        preserveScroll: false,
        preserveState: false,
      });
    }
  });
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

// Handle click outside for post options dropdown
const handleClickOutside = (event) => {
  // Check if dropdown is open and if click is outside both the dropdown and the button
  if (
    postOptionsModal.value && 
    optionsMenuRef.value && 
    optionsButtonRef.value && 
    !optionsMenuRef.value.contains(event.target) && 
    !optionsButtonRef.value.contains(event.target)
  ) {
    postOptionsModal.value = false;
    document.removeEventListener('click', handleClickOutside);
  }
};

// Show post options dropdown
const showPostOptions = () => {
  postOptionsModal.value = true;
  // Use nextTick to ensure the DOM is updated before adding the event listener
  nextTick(() => {
    // Add a small delay to prevent the same click event from immediately closing the dropdown
    setTimeout(() => {
      document.addEventListener('click', handleClickOutside);
    }, 10);
  });
};

// Clean up event listeners when component is unmounted
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

watch(() => props.post.id, () => {
  currentImageIndex.value = 0;
});
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

.post-content {
  overflow-wrap: break-word;
  word-wrap: break-word;
  word-break: break-word;
  hyphens: auto;
  max-width: 100%;
}

:deep(.post-content img),
:deep(.post-content iframe),
:deep(.post-content video),
:deep(.post-content embed),
:deep(.post-content object) {
  max-width: 100%;
  height: auto;
}

:deep(.post-content a) {
  word-break: break-all;
  color: #193CB8;
  text-decoration: underline;
}

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