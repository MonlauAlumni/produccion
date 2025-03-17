<script setup>
  import { ref } from 'vue'
  import { router } from '@inertiajs/vue3'


  const props = defineProps({
    isOpen: {
      type: Boolean,
      default: false
    },
    jobOffer: {
      type: Object,
      required: true
    }
  })

  const emit = defineEmits(['close'])

  const isSubmitting = ref(false)

  const applyToJob = () => {
    isSubmitting.value = true

    router.post(`/ofertas/${props.jobOffer.id}/aplicar`), {
      onSuccess: () => {
        isSubmitting.value = false
        closeModal()
      },
      onError: () => {
        isSubmitting.value = false
      }
    }

  }

  const closeModal = () => {

    emit('close')
  }
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 transition-opacity" @click="closeModal"></div>

    <!-- Modal -->
    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden">
      <!-- Header -->
      <div class="bg-[#193CB8] text-white py-4 px-6 border-b-4 border-[#193CB8]/20">
        <div class="flex items-center gap-2">
          <i class='bx bx-check-circle text-xl'></i>
          <h3 class="text-xl font-bold">Confirmar inscripción</h3>
        </div>
        <p class="text-blue-100 pt-1 text-sm">
          Estás a punto de inscribirte a esta oferta de trabajo
        </p>
      </div>

      <!-- Body -->
      <div class="p-6">
        <div class="mb-6 bg-[#193CB8]/5 p-4 rounded-lg border-l-4 border-[#193CB8]">
          <h3 class="font-semibold text-gray-800">{{ jobOffer.title }}</h3>
          <p class="text-sm text-gray-600 flex items-center mt-1">
            <i class='bx bx-buildings mr-1 text-[#193CB8]'></i>
            {{ jobOffer.company.company_name }}
          </p>
        </div>

        <div class="space-y-4 text-sm mb-6">
          <p class="text-gray-700">
            Al inscribirte a esta oferta:
          </p>
          <ul class="space-y-2">
            <li class="flex items-start">
              <i class='bx bx-check-circle text-[#193CB8] mt-0.5 mr-2'></i>
              <span>Tu currículum será enviado a la empresa</span>
            </li>
            <li class="flex items-start">
              <i class='bx bx-check-circle text-[#193CB8] mt-0.5 mr-2'></i>
              <span>Recibirás notificaciones sobre el estado de tu candidatura</span>
            </li>
            <li class="flex items-start">
              <i class='bx bx-check-circle text-[#193CB8] mt-0.5 mr-2'></i>
              <span>Podrás cancelar tu inscripción en cualquier momento</span>
            </li>
          </ul>
        </div>

        <!-- Cover Letter Field -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">
            Carta de presentación (opcional)
          </label>
          <textarea id="cover_letter" rows="4"
            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:border-transparent"
            placeholder="Escribe una breve carta de presentación para destacar tu candidatura..."></textarea>
          <p class="text-red-500 text-xs mt-1"></p>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex flex-col sm:flex-row gap-2 p-4 bg-gray-50 border-t border-gray-100 justify-end">
        <button @click="closeModal"
          class="px-4 py-2 border cursor-pointer border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50">
          Cancelar
        </button>
        <button @click="applyToJob" :disabled="isSubmitting"
          class="px-4 py-2 bg-[#193CB8] cursor-pointer hover:bg-[#142d8c] text-white rounded-lg font-medium flex items-center justify-center">
          <i v-if="isSubmitting" class='bx bx-loader-alt animate-spin mr-2'></i>
          <i v-else class='bx bx-send mr-2'></i>
          {{ isSubmitting ? 'Procesando...' : 'Confirmar inscripción' }}
        </button>
      </div>
    </div>
  </div>
</template>