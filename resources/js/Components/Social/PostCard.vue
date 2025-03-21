<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" :data-post-id="post.id">
    <div class="flex items-start gap-3">
      <!-- Avatar -->
      <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
        <img v-if="post.user.profile && post.user.profile.profile_picture"
          :src="post.user.profile.profile_picture || '/images/default-avatar.jpg'" :alt="post.user.name"
          class="w-full h-full object-cover" />
        <div v-else
          class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-[#193CB8] shadow-sm border-2 border-white">
          <i class='bx bxs-user text-xl'></i>
        </div>
      </div>

      <div class="flex-1">
        <!-- Post Header -->
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-medium text-gray-800">
              {{ post.user.name }} {{ post.user.last_name_1 }} {{ post.user.last_name_2 ? post.user.last_name_2 : '' }}
            </h3>
            <p class="text-xs text-gray-500">{{ formatDate(post.created_at) }}</p>
          </div>
          <button class="p-1 text-gray-400 hover:text-gray-600">
            <i class="bx bx-dots-horizontal-rounded"></i>
          </button>
        </div>

        <!-- Post Content -->
        <div class="mt-2">
          <div class="text-gray-700 post-content" v-html="sanitizeHTML(post.content)"></div>
          <div v-if="post.images && post.images.length > 0" class="mt-2">
            <div class="post-images-grid">
              <div v-for="image in post.images" :key="image.id" class="post-image-item">
                <img :src="image.image_path" alt="Post image" class="rounded-lg object-cover w-full h-full" />
              </div>
            </div>
          </div>
        </div>

        <!-- Post Actions -->
        <div class="flex items-center gap-4 mt-4 pt-2 border-t border-gray-100">
          <button @click="likePost" class="flex items-center gap-1"
            :class="isLiked ? 'text-[#193CB8]' : 'text-gray-500 hover:text-[#193CB8]'">
            <i class="bx" :class="isLiked ? 'bxs-like' : 'bx-like'"></i>
            <span class="text-sm">{{ post.likes_count }}</span>
          </button>
          <button class="flex items-center gap-1 text-gray-500 hover:text-[#193CB8]" @click="focusCommentInput">
            <i class="bx bx-comment"></i>
            <span class="text-sm">{{ post.comments_count }}</span>
          </button>
          <button class="flex items-center gap-1 text-gray-500 hover:text-[#193CB8]">
            <i class="bx bx-share"></i>
            <span class="text-sm">Compartir</span>
          </button>
        </div>

        <!-- Comments Section -->
        <div v-if="post.comments && post.comments.length > 0" class="mt-4 pt-2 border-t border-gray-100 space-y-3">
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
import { ref, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import DOMPurify from 'dompurify';
import PostComment from './PostComment.vue';

const props = defineProps({
  post: Object,
  formatDate: Function,
  isMember: Boolean,
  auth: Object
});

const commentText = ref('');
const commentInput = ref(null);
const isLiked = ref(props.post.is_liked);

const sanitizeHTML = (html) => {
  let sanitizedHTML = DOMPurify.sanitize(html);

  sanitizedHTML = sanitizedHTML.replace(/href="([^"]+)"/g, (match, p1) => {
    if (!/^https?:\/\//i.test(p1)) {
      p1 = 'http://' + p1;
    }
    return `href="${p1}"`;
  });

  return sanitizedHTML;
};

const addComment = () => {
  if (!commentText.value.trim()) return;

  const formData = new FormData();
  formData.append('content', commentText.value);

  const commentUrl = props.post.group_id
    ? `/grupos/${props.post.group_id}/posts/${props.post.id}/comment`
    : `/posts/${props.post.id}/comment`;

  router.post(commentUrl, formData, {
    preserveScroll: true,
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

const focusCommentInput = () => {
  if (commentInput.value) {
    commentInput.value.focus();
  }
};
</script>

<style scoped>
.post-images-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 8px;
}

.post-image-item {
  position: relative;
  height: 200px;
  border-radius: 0.5rem;
  overflow: hidden;
}

/* For single image, make it larger */
.post-images-grid:has(.post-image-item:only-child) .post-image-item {
  grid-column: 1 / -1;
  height: 300px;
}

/* For two images, make them side by side */
.post-images-grid:has(.post-image-item:nth-child(2):last-child) {
  grid-template-columns: repeat(2, 1fr);
}
</style>