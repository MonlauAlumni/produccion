<script setup>
    import NavBar from "@/Components/Navbar.vue";
    import CompanyProfileHeader from "@/Components/Company/CompanyProfileHeader.vue";
    import AboutSection from "@/Components/Profile/AboutSection.vue";
    import JobListingSection from "@/Components/Company/JobListingSection.vue";
    import EditModal from "@/Components/Profile/EditModal.vue";
    
    import { ref } from "vue";
    
    const props = defineProps({
      company: Object,
      jobListings: Array,
      isAdmin: Boolean,
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
        <main class="flex flex-col items-center justify-center py-6">
          <div class="flex flex-col space-y-6 w-full max-w-3xl">
            <CompanyProfileHeader :company="company" :isAdmin="isAdmin" :profile="profile" :openEditModal="openEditModal" />
            <AboutSection :profile="company" :isCompany="true" />
            <JobListingSection title="Ofertas de Empleo" :isAdmin="isAdmin" :items="jobListings" :slang="slang" />
            <EditModal :company="company" :profile="company" :isCompany="true" ref="editModal" />
          </div>
        </main>
      </div>
    </template>