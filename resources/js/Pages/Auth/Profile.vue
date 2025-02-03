<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
      <NavBar />
      <main class="flex flex-col items-center justify-center  py-6">
          <div class="flex flex-col space-y-6 w-full max-w-3xl">
           
              <ProfileHeader :user="user" />
              <AboutSection :profile="profile" />
              <ExperienceSection
      title="Experiencia Laboral"
      :items="workExperiences"
    />
    <ExperienceSection
      title="Formación Académica"
      :items="educations"
    />
    
          </div>
      </main>
  </div>
</template>



<script setup>
import NavBar from "@/Components/NavBar.vue";
import ProfileHeader from "@/Components/Profile/ProfileHeader.vue";
import AboutSection from "@/Components/Profile/AboutSection.vue";
import ExperienceSection from "@/Components/Profile/ExperienceSection.vue";

defineProps({
  user: Object,
  profile: Object,
  educations: Array,
  workExperiences: Array,
  skills: Array
});



</script>
