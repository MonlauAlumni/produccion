<template>
  <button
    :class="[ 
      'flex items-center justify-center px-4 py-2 rounded-md transition-colors duration-300 w-full cursor-pointer',
      providerClasses[provider]
    ]"
    @click="redirectToAuthProvider"
  >
    <img v-if="image" :src="providerIcon" alt="provider icon" class="w-5 h-5 mr-3" />
    <span class="font-semibold">{{ text }}</span>
  </button>
</template>

<script setup>
import { computed } from "vue";

// Props
const props = defineProps({
  provider: {
    type: String,
    required: true,
    validator: (value) => ["github", "microsoft", "google"].includes(value),
  },
  text: {
    type: String,
    default: "Sign in",
  },
  image: {
    type: Boolean,
    default: true,
  },
});

// Emit event
const emit = defineEmits(["click"]);

// Computed property for provider icon
const providerIcon = computed(() => {
  const icons = {
    github: "/images/github-mark-white.png",
    microsoft: "/images/microsoft-icon.png",
    google: "/images/logoGoogle.png",
  };
  return icons[props.provider];
});

// Classes for different providers
const providerClasses = {
  github: "bg-gray-900 text-white hover:bg-gray-700",
  microsoft: "bg-blue-500 text-white hover:bg-blue-600",
  google: "bg-white text-gray-700 hover:bg-gray-100 border border-gray-300",
};

// Redirection logic
const redirectToAuthProvider = () => {
  const providerUrls = {
    github: "/github/redirect",
    microsoft: "/redirect",
    google: "/google/redirect",
  };

  const redirectUrl = providerUrls[props.provider];
  if (redirectUrl) {
    window.location.href = redirectUrl;
  } else {
    console.error(`No redirection URL defined for provider: ${props.provider}`);
  }
};
</script>
