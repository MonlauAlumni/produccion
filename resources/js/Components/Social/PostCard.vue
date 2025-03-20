<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-start gap-3">
      <!-- Avatar -->
      <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
        <img v-if="post.user.profile.profile_picture"
          :src="post.user.profile.profile_picture || '/images/default-avatar.jpg'"
          :alt="post.user.name" class="w-full h-full object-cover" />
        <div v-else class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-[#193CB8] shadow-sm border-2 border-white">
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
          <img v-if="post.image" :src="post.image" alt="Imagen de la publicación"
            class="mt-3 rounded-lg w-full object-cover max-h-96" />
        </div>

        <!-- Post Actions -->
        <div class="flex items-center gap-4 mt-4 pt-2 border-t border-gray-100">
          <button class="flex items-center gap-1 text-gray-500 hover:text-[#193CB8]">
            <i class="bx bx-like"></i>
            <span class="text-sm">Me gusta</span>
          </button>
          <button class="flex items-center gap-1 text-gray-500 hover:text-[#193CB8]">
            <i class="bx bx-comment"></i>
            <span class="text-sm">Comentar</span>
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
            <img :src="auth?.user?.profile?.profile_picture || '/images/default-avatar.jpg'"
              alt="Tu avatar" class="w-full h-full object-cover" />
          </div>
          <div class="flex-1 relative">
            <input type="text" placeholder="Escribe un comentario..." v-model="comment"
              @keyup.enter="addComment"
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

const comment = ref('');

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
  if (!comment.value.trim()) return;

  const formData = new FormData();
  formData.append('comment', comment.value);

  router.post(`/grupos/${props.post.group_id}/posts/${props.post.id}/comment`, formData, {
    onSuccess: () => {
      comment.value = '';
      nextTick(() => {
        document.querySelector('.post-content')?.scrollIntoView({ behavior: 'smooth' });
      });
    }
  });
};
</script>

