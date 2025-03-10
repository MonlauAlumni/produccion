<script setup>
import Layout from '@/Components/Layout.vue';
import { ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';

const user = computed(() => usePage().props.auth?.user);
const username = ref(user.value.name);
const email = ref(user.value.email);

const isTwoFactorEnabled = ref(!!user.value?.two_factor_secret);
const qrCode = ref("");

const isDarkMode = ref(localStorage.getItem('theme') == 'dark');
const emailSending = ref(false);

const isUpdating = ref(false);
const updateErrors = ref({});
const updateSuccess = ref(false);

const toggleDarkMode = () => {
    if (isDarkMode.value) {
        localStorage.setItem('theme', 'dark');
        document.documentElement.classList.add('dark');
    } else {
        localStorage.setItem('theme', 'light');
        document.documentElement.classList.remove('dark');
    }
};

onMounted(() => {
    const theme = localStorage.getItem('theme');
    isDarkMode.value = theme === 'dark';

    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
});

const getQRCode = async () => {
    try {
        const response = await axios.get('/user/two-factor-qr-code');
        qrCode.value = response.data.svg;
        qrModal.value = true;
    } catch (error) {
        console.error("Error fetching QR Code:", error);
    }
};

const enable2FA = async () => {
    try {
        await router.post('/user/two-factor-authentication');
        isTwoFactorEnabled.value = true;
        getQRCode();
    } catch (error) {
        console.error('Error enabling 2FA', error);
    }
};

const disable2FA = async () => {
    try {
        await router.delete('/user/two-factor-authentication');
        isTwoFactorEnabled.value = false;
        qrModal.value = false;
        disableConfirmModal.value = false;
    } catch (error) {
        console.error('Error disabling 2FA', error);
    }
};

const updateUserInfo = async () => {
    isUpdating.value = true;
    updateErrors.value = {};
    updateSuccess.value = false;

    try {
        const response = await axios.put('/user/update', {
            name: username.value,
            email: email.value,
            allow_emails: emailSending.value
        });

        if (response.status === 200) {
            updateSuccess.value = true;
        }
    } catch (error) {
        console.error('Error updating user information:', error);

        if (error.response?.status === 422 && error.response.data.errors) {
            const serverErrors = error.response.data.errors;

            if (serverErrors.name) {
                updateErrors.value.name = Array.isArray(serverErrors.name)
                    ? serverErrors.name[0]
                    : serverErrors.name;
            }

            if (serverErrors.email) {
                updateErrors.value.email = Array.isArray(serverErrors.email)
                    ? serverErrors.email[0]
                    : serverErrors.email;
            }
        } else {
            updateErrors.value.general = 'An error occurred while updating your information.';
        }
    } finally {
        isUpdating.value = false;

        if (updateSuccess.value) {
            setTimeout(() => {
                updateSuccess.value = false;
            }, 3000);
        }
    }
};

// Modals
const qrModal = ref(false);
const disableConfirmModal = ref(false);


const confirmCountdown = ref(10);
const confirmButtonEnabled = ref(false);
const countdownTimer = ref(null);

const showDisableConfirmation = () => {
    if (countdownTimer.value) {
        clearInterval(countdownTimer.value);
    }

    disableConfirmModal.value = true;
    confirmCountdown.value = 10;
    confirmButtonEnabled.value = false;

    countdownTimer.value = setInterval(() => {
        confirmCountdown.value--;
        if (confirmCountdown.value <= 0) {
            clearInterval(countdownTimer.value);
            confirmButtonEnabled.value = true;
        }
    }, 1000);
};

const closeDisableConfirmation = () => {
    disableConfirmModal.value = false;

    if (countdownTimer.value) {
        clearInterval(countdownTimer.value);
        countdownTimer.value = null;
    }
};

const handle2FAButtonClick = () => {
    if (isTwoFactorEnabled.value) {
        showDisableConfirmation();
    } else {
        enable2FA();
    }
};

// New reactive variable for font size preview
const fontSize = ref(16);

// --- New code start ---
const profilePictureSrc = ref('/storage/images/profile-pictures/foto.png');
const fileInputRef = ref(null);

const triggerFileInput = () => {
    fileInputRef.value.click();
};

const uploadProfilePicture = async (e) => {
    const file = e.target.files[0];
    if (file) {
        // TODO: Upload file using axios
        console.log('Uploading:', file);
    }
};
// --- New code end ---

</script>

<template>
    <Layout :auth="auth">

        <div class="grid grid-cols-2 gap-4">
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-xl space-y-6">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">CUENTA</h1>
                        <p class="text-gray-600 dark:text-gray-400">Administre la información de tu cuenta.</p>
                    </div>
                    <div class="flex space-x-2 mt-4 md:mt-0">
                        <button @click="updateUserInfo" :disabled="isUpdating"
                            class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600 cursor-pointer text-white transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ isUpdating ? 'Actualizando...' : 'Actualizar Información' }}
                        </button>
                        <button @click="router.get('/settings/change-password')"
                            class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600 cursor-pointer text-white transition duration-300">
                            Cambiar Contraseña
                        </button>
                    </div>
                </div>

                <div v-if="updateSuccess"
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow">
                    Información actualizada exitosamente.
                </div>

                <div v-if="updateErrors.general"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow">
                    {{ updateErrors.general }}
                </div>

                <div class="flex flex-col gap-4">
                    <div class="flex items-center">
                        <label for="username"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mr-4 w-1/3">
                            Nombre de usuario
                        </label>
                        <div class="relative w-2/3">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bx bx-user text-gray-500"></i>
                            </div>
                            <input v-model="username" type="text" :placeholder="user.name" required
                                class="block w-full pl-10 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600" />
                        </div>
                    </div>
                    <p v-if="updateErrors.name" class="text-red-500 text-sm">{{ updateErrors.name }}</p>

                    <div class="flex items-center">
                        <label for="email"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mr-4 w-1/3">
                            Correo electrónico
                        </label>
                        <div class="relative w-2/3">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bx bx-envelope text-gray-500"></i>
                            </div>
                            <input v-model="email" type="email" :placeholder="user.email" required
                                class="block w-full pl-10 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600" />
                        </div>
                    </div>
                    <p v-if="updateErrors.email" class="text-red-500 text-sm">{{ updateErrors.email }}</p>
                </div>

                <!-- Profile Picture Section -->
                <div class="flex items-center mt-4">
                    <div class="relative">
                        <img v-if="user.profile_picture" :src="profilePictureSrc" @error="profilePictureSrc = '/images/placeholder.png'"
                            class="w-24 h-24 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600" />

                        <div v-else class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                            <i class="bx bxs-user text-5xl text-gray-500"></i>
                        </div>
                        <div @click="triggerFileInput"
                            class="absolute bottom-0 right-0 bg-gray-800 text-white p-1 rounded-full cursor-pointer">
                            <i class="bx bx-camera"></i>
                        </div>
                    </div>
                    <input type="file" ref="fileInputRef" @change="uploadProfilePicture" class="hidden" />

                    <div class="ml-4">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Foto de Perfil</h2>
                        <p class="text-gray-600 dark:text-gray-400">Personaliza tu foto de perfil.</p>
                    </div>
                </div>
                <!-- End Profile Picture Section -->

            </div>

            <div
                class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-2xl space-y-6 border border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">APARIENCIA</h1>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 mt-2">TEMA</h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">
                            Personaliza tu experiencia eligiendo el modo de visualización y configurando
                            opciones adicionales que se adapten a tu estilo.
                        </p>
                    </div>
                    <div class="flex flex-col items-center space-y-2 md:space-y-0 md:flex-row">
                        <span class="text-gray-700 dark:text-white">Modo Oscuro</span>
                        <label class="relative inline-flex items-center cursor-pointer ml-2">
                            <input type="checkbox" v-model="isDarkMode" @change="toggleDarkMode" class="sr-only peer">
                            <div class="w-11 h-6 rounded-full transition-all duration-300"
                                :class="isDarkMode ? 'bg-blue-500' : 'bg-gray-600'">
                                <div class="absolute top-1 left-1 h-4 w-4 bg-white border border-gray-300 rounded-full transition-all duration-300"
                                    :class="isDarkMode ? 'translate-x-5 border-white' : ''"></div>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="mt-4 border-t pt-4">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Personalización Avanzada</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Explora opciones adicionales para configurar tu interfaz, como la selección de colores de acento
                        y ajustes de fuente para mejorar la legibilidad.
                    </p>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Color de
                                Acento</label>
                            <select
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                <option>Azul</option>
                                <option>Rojo</option>
                                <option>Verde</option>
                                <option>Púrpura</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tamaño de
                                Fuente de las Publicaciones</label>
                            <input v-model="fontSize" type="range" min="12" max="24" class="w-full mt-1" />
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col space-y-2">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Previsualización del Tema
                        </h3>
                        <div class="p-4 bg-gray-100 dark:bg-gray-900 rounded-lg shadow-inner">
                            <p class="text-sm text-gray-700 dark:text-gray-300" :style="{ fontSize: fontSize + 'px' }">
                                Esta es una previsualización del tema seleccionado. Cambia las opciones de
                                personalización y
                                observa cómo se transforma el entorno de tu aplicación.
                            </p>
                            <div class="mt-2 flex justify-around">
                                <span
                                    class="px-2 py-1 bg-blue-200 dark:bg-blue-800 text-blue-800 dark:text-blue-200 rounded"
                                    :style="{ fontSize: fontSize + 'px' }">
                                    Ejemplo 1
                                </span>
                                <span
                                    class="px-2 py-1 bg-green-200 dark:bg-green-800 text-green-800 dark:text-green-200 rounded"
                                    :style="{ fontSize: fontSize + 'px' }">
                                    Ejemplo 2
                                </span>
                                <span
                                    class="px-2 py-1 bg-red-200 dark:bg-red-800 text-red-800 dark:text-red-200 rounded"
                                    :style="{ fontSize: fontSize + 'px' }">
                                    Ejemplo 3
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <h1 class="border-y border-gray-500 p-2 dark:text-gray-500">SOCIAL</h1>
        <div class="p-4">
            <div class="flex items-center">
                <span class="text-gray-300 dark:text-gray-700">Allow emails</span>
                <label class="relative inline-flex items-center cursor-pointer ml-2">
                    <input type="checkbox" v-model="emailSending" class="sr-only peer">
                    <div class="w-11 h-6 rounded-full transition-all duration-300"
                        :class="!emailSending ? 'bg-yellow-500' : 'bg-gray-600'">
                        <div class="absolute top-1 left-1 h-4 w-4 bg-white border border-gray-300 rounded-full transition-all duration-300"
                            :class="!emailSending ? 'translate-x-5 border-white' : ''"></div>
                    </div>
                </label>
            </div>
        </div>

        <h1 class="border-y border-gray-500 p-2 dark:text-gray-500">SECURITY</h1>
        <p class="px-4 mt-4 dark:text-gray-700">Add an extra layer of security to your account.</p>
        <div class="p-4 grid grid-cols-2 gap-4">
            <div class="bg-gray-900 dark:bg-gray-50 p-6 rounded-lg dark:text-gray-700 shadow-lg">
                <p class="text-xl font-bold">Two-Factor Authentication (2FA)</p>
                <p class="mt-2 text-justify">
                    Two-Factor Authentication adds an extra layer of security to your account.
                    By requiring a second form of verification, it helps prevent unauthorized access,
                    even if someone knows your password.
                </p>
                <p class="mt-2 text-justify">
                    Once enabled, you'll be asked for a verification code sent to your device each time
                    you log in. You can use an authenticator app to complete this process.
                </p>
                <button @click="handle2FAButtonClick()" :class="[
                    'w-1/2',
                    'text-white',
                    'font-semibold',
                    'p-3',
                    'rounded-lg',
                    'cursor-pointer',
                    'text-center',
                    'mt-4',
                    'transition',
                    'duration-300',
                    isTwoFactorEnabled ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600'
                ]">
                    {{ isTwoFactorEnabled ? "DISABLE 2FA (NOT RECOMMENDED)" : "ENABLE 2FA" }}
                </button>
            </div>
        </div>

        <!-- QR Code Modal -->
        <div v-if="qrCode && qrModal" class="fixed inset-0 flex items-center justify-center bg-black/50">
            <div class="bg-gray-900 dark:bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-lg font-semibold mb-4">Scan this QR Code</h2>
                <div v-html="qrCode" class="p-4 bg-gray-100 dark:border rounded-lg inline-block"></div>
                <p class="text text-gray-300 mt-2 dark:text-gray-500">Use your authenticator app to scan the code and
                    enable 2FA</p>
                <div class="flex gap-2">
                    <button @click="disable2FA()"
                        class="mt-4 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 cursor-pointer transition duration-300">Cancel</button>
                    <button @click="qrModal = false"
                        class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 cursor-pointer transition duration-300">Confirm</button>
                </div>
            </div>
        </div>

        <!-- Disable 2FA Confirmation Modal -->
        <div v-if="disableConfirmModal" class="fixed inset-0 flex items-center justify-center bg-black/50">
            <div class="bg-gray-900 dark:bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-xl font-bold text-red-500 mb-4">WARNING: Disabling 2FA</h2>
                <div class="p-4 bg-red-100 dark:bg-red-50 rounded-lg mb-4">
                    <p class="text-red-800 font-semibold">This action is not recommended</p>
                </div>
                <p class="text-gray-300 dark:text-gray-700 text-justify mb-4">
                    Disabling Two-Factor Authentication will significantly reduce the security of your account.
                    Without 2FA, your account will be more vulnerable to unauthorized access.
                </p>
                <p class="text-gray-300 dark:text-gray-700 text-justify mb-4">
                    We strongly recommend keeping 2FA enabled to protect your personal information and data.
                </p>
                <div class="flex justify-between gap-2">
                    <button @click="closeDisableConfirmation()"
                        class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 cursor-pointer transition duration-300 flex-1">
                        Cancel
                    </button>
                    <button @click="disable2FA()" :disabled="!confirmButtonEnabled"
                        class="mt-4 px-4 py-2 rounded-lg cursor-pointer transition duration-300 flex-1 flex items-center justify-center"
                        :class="confirmButtonEnabled ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-gray-500 text-gray-300 cursor-not-allowed'">
                        <span v-if="!confirmButtonEnabled">Wait {{ confirmCountdown }}s</span>
                        <span v-else>I understand, disable 2FA</span>
                    </button>
                </div>
            </div>
        </div>
    </Layout>

</template>