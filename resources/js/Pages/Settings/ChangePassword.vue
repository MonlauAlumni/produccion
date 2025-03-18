<script setup>
import Layout from '@/Components/Layout.vue';
import { ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import InputWithIcon from '@/Components/InputWithIcon.vue';

const user = computed(() => usePage().props.auth?.user);
const isDarkMode = ref(localStorage.getItem('theme') == 'dark');

onMounted(() => {
    const theme = localStorage.getItem('theme');
    isDarkMode.value = theme === 'dark';

    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
});

const currentPassword = ref('');
const newPassword = ref('');
const confirmPassword = ref('');

const isSubmitting = ref(false);
const errors = ref({});
const successMessage = ref('');

const passwordStrength = computed(() => {
  if (!newPassword.value) return 0;
  
  let strength = 0;
  if (newPassword.value.length >= 8) strength += 1;
  if (/\d/.test(newPassword.value)) strength += 1;
  if (/[a-z]/.test(newPassword.value)) strength += 1;
  if (/[A-Z]/.test(newPassword.value)) strength += 1;
  if (/[^A-Za-z0-9]/.test(newPassword.value)) strength += 1;
  
  return strength;
});

const passwordStrengthText = computed(() => {
  const strength = passwordStrength.value;
  if (strength === 0) return '';
  if (strength < 3) return 'Débil';
  if (strength < 5) return 'Medio';
  return 'Fuerte';
});

const passwordStrengthColor = computed(() => {
  const strength = passwordStrength.value;
  if (strength === 0) return 'bg-gray-300';
  if (strength < 3) return 'bg-red-500';
  if (strength < 5) return 'bg-yellow-500';
  return 'bg-green-500';
});

const validateForm = () => {
  errors.value = {};
  
  if (!currentPassword.value) {
    errors.value.currentPassword = 'La contraseña actual es obligatoria';
  }
  
  if (!newPassword.value) {
    errors.value.newPassword = 'La nueva contraseña es obligatoria';
  } else if (newPassword.value.length < 8) {
    errors.value.newPassword = 'La contraseña debe tener al menos 8 caracteres';
  } else if (passwordStrength.value < 3) {
    errors.value.newPassword = 'La contraseña es demasiado débil';
  }
  
  if (!confirmPassword.value) {
    errors.value.confirmPassword = 'Por favor, confirma tu nueva contraseña';
  } else if (newPassword.value !== confirmPassword.value) {
    errors.value.confirmPassword = 'Las contraseñas no coinciden';
  }
  
  return Object.keys(errors.value).length === 0;
};

const updatePassword = async () => {
  if (!validateForm()) return;
  
  isSubmitting.value = true;
  successMessage.value = '';
  errors.value = {};
  
  try {
    const response = await axios.put('/settings/change-password', {
      current_password: currentPassword.value,
      password: newPassword.value,
      password_confirmation: confirmPassword.value
    });
    
    if (response.status === 200) {
      currentPassword.value = '';
      newPassword.value = '';
      confirmPassword.value = '';
      
      successMessage.value = response.data.message || '¡Contraseña actualizada correctamente!';
    }
  } catch (error) {
    console.error('Error al actualizar la contraseña:', error);
    successMessage.value = '';
    
    if (error.response?.status === 422) {
      if (error.response.data.errors) {
        const serverErrors = error.response.data.errors;
        
        if (serverErrors.current_password) {
          errors.value.currentPassword = Array.isArray(serverErrors.current_password) 
            ? serverErrors.current_password[0] 
            : serverErrors.current_password;
        }
        
        if (serverErrors.password) {
          errors.value.newPassword = Array.isArray(serverErrors.password)
            ? serverErrors.password[0]
            : serverErrors.password;
        }
        
        if (serverErrors.password_confirmation) {
          errors.value.confirmPassword = Array.isArray(serverErrors.password_confirmation)
            ? serverErrors.password_confirmation[0]
            : serverErrors.password_confirmation;
        }
      } else {
        errors.value.general = 'La validación falló. Por favor, revisa tus datos.';
      }
    } else {
      errors.value.general = 'Ocurrió un error. Por favor, inténtalo de nuevo.';
    }
  } finally {
    isSubmitting.value = false;
  }
};

const goBack = () => {
  router.get('/configuracion');
};
</script>

<template>
  <Layout :auth="auth">
    <div class="max-w-4xl mx-auto">
      <div class="flex items-center mb-6 mt-4">
        <button @click="goBack" class="cursor-pointer flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
          <i class="bx bx-arrow-back mr-2 text-xl"></i>
          <span>Volver a Ajustes</span>
        </button>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700">
        <div class="border-b border-gray-200 dark:border-gray-700 p-6">
          <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Cambiar Contraseña</h1>
          <p class="text-gray-600 dark:text-gray-400 mt-2">
            Actualiza tu contraseña para mantener tu cuenta segura. Te recomendamos usar una contraseña fuerte y única.
          </p>
        </div>

        <form @submit.prevent="updatePassword" class="p-6 space-y-6">
          <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow">
            {{ successMessage }}
          </div>
          
          <div v-if="errors.general" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow">
            {{ errors.general }}
          </div>
          
          <div>
            <label for="current-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Contraseña Actual
            </label>
            <InputWithIcon 
              icon="lock" 
              id="current-password"
              v-model="currentPassword" 
              type="password" 
              class="w-full" 
              placeholder="Ingresa tu contraseña actual"
               
            />
            <p v-if="errors.currentPassword" class="text-red-500 text-sm mt-1">{{ errors.currentPassword }}</p>
          </div>
          
          <div>
            <label for="new-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Nueva Contraseña
            </label>
            <InputWithIcon 
              icon="lock" 
              id="new-password"
              v-model="newPassword" 
              type="password" 
              class="w-full" 
              placeholder="Ingresa tu nueva contraseña"
              required 
            />
            <p v-if="errors.newPassword" class="text-red-500 text-sm mt-1">{{ errors.newPassword }}</p>
            
            <div v-if="newPassword" class="mt-3">
              <div class="flex items-center justify-between mb-1">
                <span class="text-sm text-gray-600 dark:text-gray-400">Seguridad de la contraseña:</span>
                <span class="text-sm font-medium" :class="{
                  'text-red-500': passwordStrengthText === 'Débil',
                  'text-yellow-500': passwordStrengthText === 'Medio',
                  'text-green-500': passwordStrengthText === 'Fuerte'
                }">{{ passwordStrengthText }}</span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                <div class="h-2.5 rounded-full transition-all duration-300" 
                  :class="passwordStrengthColor"
                  :style="{ width: `${(passwordStrength / 5) * 100}%` }"></div>
              </div>
              <ul class="text-xs text-gray-500 dark:text-gray-400 mt-2 space-y-1">
                <li>• Al menos 8 caracteres</li>
                <li>• Incluir números</li>
                <li>• Incluir letras minúsculas</li>
                <li>• Incluir letras mayúsculas</li>
                <li>• Incluir caracteres especiales</li>
              </ul>
            </div>
          </div>
          
          <div>
            <label for="confirm-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Confirmar Nueva Contraseña
            </label>
            <InputWithIcon 
              icon="lock" 
              id="confirm-password"
              v-model="confirmPassword" 
              type="password" 
              class="w-full" 
              placeholder="Confirma tu nueva contraseña"
              required 
            />
            <p v-if="errors.confirmPassword" class="text-red-500 text-sm mt-1">{{ errors.confirmPassword }}</p>
          </div>
          
          <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
            <button 
              type="button" 
              @click="goBack"
              class="px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white rounded-lg transition-colors"
            >
              Cancelar
            </button>
            
            <button 
              type="submit" 
              :disabled="isSubmitting"
              class="px-6 py-2 bg-gradient-to-r from-[#193CB8] to-[#2748c6] hover:from-[#1835a0] hover:to-[#2341b3] text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ isSubmitting ? 'Actualizando...' : 'Actualizar Contraseña' }}
            </button>
          </div>
        </form>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 mt-6">
        <div class="p-6">
          <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Consejos de Seguridad</h2>
          <div class="mt-4 space-y-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <i class="bx bx-shield text-2xl text-blue-500 mr-3"></i>
              </div>
              <div>
                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Usa una contraseña única</h3>
                <p class="text-gray-600 dark:text-gray-400">No reutilices contraseñas en diferentes sitios. Usa un gestor de contraseñas para crear y almacenar contraseñas seguras.</p>
              </div>
            </div>
            
            <div class="flex">
              <div class="flex-shrink-0">
                <i class="bx bx-lock-alt text-2xl text-blue-500 mr-3"></i>
              </div>
              <div>
                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Activa la autenticación de dos factores</h3>
                <p class="text-gray-600 dark:text-gray-400">Añade una capa adicional de seguridad a tu cuenta activando la autenticación de dos factores (2FA).</p>
              </div>
            </div>
            
            <div class="flex">
              <div class="flex-shrink-0">
                <i class="bx bx-refresh text-2xl text-blue-500 mr-3"></i>
              </div>
              <div>
                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Actualiza regularmente</h3>
                <p class="text-gray-600 dark:text-gray-400">Cambia tu contraseña periódicamente, especialmente si sospechas que tu cuenta podría estar comprometida.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>