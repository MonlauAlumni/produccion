<script setup>
import NavBar from "@/Components/Navbar.vue";
import ProfileHeader from "@/Components/Profile/ProfileHeader.vue";
import AboutSection from "@/Components/Profile/AboutSection.vue";
import ExperienceSection from "@/Components/Profile/ExperienceSection.vue";
import EditModal from "@/Components/Profile/EditModal.vue";

import { ref } from "vue";


const props = defineProps({
  user: Object,
  profile: Object,
  educations: Array,
  workExperiences: Array,
  skills: Array,
  isSameUser: Boolean,
});

const editModal = ref(null);

const openEditModal = () => {
  if (editModal.value) {
    editModal.value.openModal();
  }
};


</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
      <NavBar />
      <main class="flex flex-col items-center justify-center  py-6">
          <div class="flex flex-col space-y-6 w-full max-w-3xl">
              <ProfileHeader :user="user" :isSameUser="isSameUser" :profile="profile" :openEditModal="openEditModal"/>
              <AboutSection :profile="profile" />
              <ExperienceSection
      title="Experiencia Laboral"
      :items="workExperiences"
    />
    <ExperienceSection
      title="Formación Académica"
      :items="educations"
    />
    <EditModal :user="user" :profile="profile" ref="editModal" />
          </div>
      </main>
  </div>
</template>



