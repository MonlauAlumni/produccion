<script setup>
import Layout from '@/Components/Layout.vue';
import { ref, computed, onMounted, defineProps } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import StandardButton from '@/Components/StandardButton.vue';

const user = computed(() => usePage().props.auth.user);

const pfp = computed(() => user.value?.profile?.profile_picture ?? null);

const props = defineProps({
    settings: {
        type: Array,
        required: true
    },
    profile: {
        type: Object,
        required: true
    },
});
const username = ref(user.value.name);
const email = ref(user.value.email);

const t = (key) => {
    const keys = key.split('.');
    let value = usePage().props.translations;

    for (const k of keys) {
        if (value && typeof value === 'object' && k in value) {
            value = value[k];
        } else {
            return key;
        }
    }

    return value;
};

const selectedLanguage = ref(props.settings.language);

// Agregar arreglo de idiomas con bandera, nombre y código
const languages = [
    { code: 'es', name: 'Español (España)', flag: '/images/flags/es.png' },
    { code: 'ca', name: 'Català', flag: '/images/flags/ca.png' },
    { code: 'en', name: 'English', flag: '/images/flags/en.png' },
    { code: 'fr', name: 'Français', flag: '/images/flags/fr.png' },
    { code: 'de', name: 'Deutsch', flag: '/images/flags/de.png' }
];

const changeLanguage = () => {
    router.put('/settings/change-language', {
        preserveScroll: true,
        preserveSate: true,
        language: selectedLanguage.value
    });
};

const isTwoFactorEnabled = ref(!!user.value?.two_factor_secret);
const qrCode = ref("");

const isDarkMode = ref(localStorage.getItem('theme') == 'dark');
const emailSending = ref(false);

const isUpdating = ref(false);
const isAppearanceUpdating = ref(false);
const updateErrors = ref({});
const updateAppearanceErrors = ref({});
const updateSuccess = ref(false);
const updateAppearanceSuccess = ref(false);


//Appareance settings
const highlightColor = ref(props.settings.highlight_color);

// Social settings
const socialProfiles = ref({
    twitter: user.value?.social_profiles?.twitter || '',
    linkedin: user.value?.social_profiles?.linkedin || '',
    github: user.value?.social_profiles?.github || '',
    website: user.value?.social_profiles?.website || ''
});

const notificationPreferences = ref({
    mentions: user.value?.notification_preferences?.mentions || true,
    comments: user.value?.notification_preferences?.comments || true,
    messages: user.value?.notification_preferences?.messages || true,
    updates: user.value?.notification_preferences?.updates || false
});

// Privacy settings
const privacySettings = ref({
    profileVisibility: user.value?.privacy_settings?.profileVisibility || 'public',
    showEmail: user.value?.privacy_settings?.showEmail || false,
    showActivity: user.value?.privacy_settings?.showActivity || true,
    allowTagging: user.value?.privacy_settings?.allowTagging || true
});

const toggleDarkMode = () => {
    if (isDarkMode.value) {
        localStorage.setItem('theme', 'dark');
        document.documentElement.classList.add('dark');
        highlightColor.value = '#ffffff';
    } else {
        localStorage.setItem('theme', 'light');
        document.documentElement.classList.remove('dark');
        highlightColor.value = '#000000';
    }
    updateAppearanceInfo();
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
    qrModal.value = true;
    try {
        const response = await axios.get('/user/two-factor-qr-code');
        if (response.data && response.data.svg) {
            qrCode.value = response.data.svg;
        } else {
            qrCode.value = '';
            console.error("QR Code data not found in response.");
        }
    } catch (error) {
        qrCode.value = '';
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
        await axios.delete('/user/two-factor-authentication');
        isTwoFactorEnabled.value = false;
        qrModal.value = false;
        disableConfirmModal.value = false;
    } catch (error) {
        console.error('Error disabling 2FA', error);
    }
};

const updateUserInfo = () => {
    isUpdating.value = true;
    updateErrors.value = {};
    updateSuccess.value = false;

    router.put('/user/update', {
        name: username.value,
        email: email.value,
        allow_emails: emailSending.value,
        social_profiles: socialProfiles.value,
        notification_preferences: notificationPreferences.value,
        privacy_settings: privacySettings.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            updateSuccess.value = true;
        },
        onError: (errors) => {
            console.error('Error updating user information:', errors);

            if (errors.name) {
                updateErrors.value.name = Array.isArray(errors.name)
                    ? errors.name[0]
                    : errors.name;
            }

            if (errors.email) {
                updateErrors.value.email = Array.isArray(errors.email)
                    ? errors.email[0]
                    : errors.email;
            } else {
                updateErrors.value.general = 'An error occurred while updating your information.';
            }
        },
        onFinish: () => {
            isUpdating.value = false;
            if (updateSuccess.value) {
                setTimeout(() => {
                    const el = document.querySelector('.bg-green-100.border.border-green-400.text-green-700');
                    if (el) {
                        el.style.transition = 'opacity 500ms ease';
                        el.style.opacity = '0';
                        setTimeout(() => {
                            updateSuccess.value = false;
                        }, 500);
                    } else {
                        updateSuccess.value = false;
                    }
                }, 2500);
            }
        }
    });
};

const updateAppearanceInfo = () => {
    isAppearanceUpdating.value = true;
    updateAppearanceErrors.value = {};
    updateAppearanceSuccess.value = false;

    router.put('/settings/update-appearance', {
        highlight_color: highlightColor.value,
        font_size: fontSize.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            updateAppearanceSuccess.value = true;
        },
        onError: (errors) => {
            console.error('Error actualizando la configuración de apariencia:', errors);
            updateAppearanceErrors.value.general = 'Error inesperado al actualizar la apariencia.';
        },
        onFinish: () => {
            isAppearanceUpdating.value = false;
            if (updateAppearanceSuccess.value) {
                setTimeout(() => {
                    const el = document.querySelector('.bg-green-100.border.border-green-400.text-green-700');
                    if (el) {
                        el.style.transition = 'opacity 500ms ease';
                        el.style.opacity = '0';
                        setTimeout(() => {
                            updateAppearanceSuccess.value = false;
                        }, 500);
                    } else {
                        updateAppearanceSuccess.value = false;
                    }
                }, 2500);
            }
        }
    });
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
const fontSize = ref(props.settings.font_size)

// Profile picture handling
const profilePictureSrc = ref('/storage/images/profile-pictures/foto.png');
const fileInputRef = ref(null);

const triggerFileInput = () => {
    fileInputRef.value.click();
};

const uploadProfilePicture = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('profile_picture', file);

    try {
        await router.post(`/perfil/${props.profile.slang}/update-profile-picture`, formData, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                console.log('Profile picture uploaded successfully');
            },
            onError: (error) => {
                console.error('Error uploading profile picture:', error);
            }
        });
    } catch (error) {
        console.error('Error uploading profile picture:', error);
    }
};

// Social account connection status
const connectedAccounts = ref({
    google: false,
    github: true,
    twitter: false,
    facebook: false
});

const connectAccount = (platform) => {
    // This would typically redirect to OAuth flow
    console.log(`Connecting to ${platform}...`);
    // For demo purposes, toggle the connection status
    connectedAccounts.value[platform] = !connectedAccounts.value[platform];
};

const resetToDefaults = () => {
    fontSize.value = 16;
    highlightColor.value = '#000000';
    console.log(props.settings, user.value);
};

const activeSessions = ref([
    {
        device: 'Chrome on Windows',
        location: 'Madrid, Spain',
        ip: '192.168.1.1',
        lastActive: '2 minutes ago',
        current: true
    },
    {
        device: 'Safari on iPhone',
        location: 'Barcelona, Spain',
        ip: '192.168.1.2',
        lastActive: '2 days ago',
        current: false
    },
    {
        device: 'Firefox on MacOS',
        location: 'Valencia, Spain',
        ip: '192.168.1.3',
        lastActive: '5 days ago',
        current: false
    }
]);

const terminateSession = (index) => {
    if (!activeSessions.value[index].current) {
        activeSessions.value.splice(index, 1);
    }
};

const terminateAllSessions = () => {
    activeSessions.value = activeSessions.value.filter(session => session.current);
};

</script>

<template>
    <Layout :auth="auth">

        <div class="grid grid-cols-2 gap-4 p-4">
            <div class="p-6 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg shadow-xl space-y-6 transition-colors duration-300">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">CUENTA</h1>
                        <p class="text-gray-600 dark:text-gray-400">Administre la información de tu cuenta.</p>
                    </div>
                    <div class="flex space-x-2 mt-4 md:mt-0">
                        <button @click="updateUserInfo" :disabled="isUpdating"
                            class="bg-gradient-to-r from-[#193CB8] to-[#2748c6] px-4 py-2 rounded hover:bg-blue-600 cursor-pointer text-white transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ isUpdating ? 'Actualizando...' : 'Actualizar Información' }}
                        </button>
                        <button @click="router.get('/configuracion/cambiar-contraseña')"
                            class="bg-gradient-to-r from-[#193CB8] to-[#2748c6] px-4 py-2 rounded hover:bg-blue-600 cursor-pointer text-white transition duration-300">
                            Cambiar Contraseña
                        </button>
                    </div>
                </div>

                <div v-if="updateSuccess"
                    class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded shadow transition-colors duration-300">
                    Información actualizada exitosamente.
                </div>

                <div v-if="updateErrors.general"
                    class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded shadow transition-colors duration-300">
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
                                <i class="bx bx-user text-gray-500 dark:text-gray-400"></i>
                            </div>
                            <input v-model="username" type="text" :placeholder="user.name" required
                                class="block w-full pl-10 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-300" />
                        </div>
                    </div>
                    <p v-if="updateErrors.name" class="text-red-500 dark:text-red-400 text-sm">{{ updateErrors.name }}</p>

                    <div class="flex items-center">
                        <label for="email"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mr-4 w-1/3">
                            Correo electrónico
                        </label>
                        <div class="relative w-2/3">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bx bx-envelope text-gray-500 dark:text-gray-400"></i>
                            </div>
                            <input v-model="email" type="email" :placeholder="user.email" required
                                class="block w-full pl-10 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-300" />
                        </div>
                    </div>
                    <p v-if="updateErrors.email" class="text-red-500 dark:text-red-400 text-sm">{{ updateErrors.email }}</p>
                </div>

                <!-- Profile Picture Section -->
                <div class="flex items-center mt-4">
                    <div class="relative">
                        <img v-if="pfp" :src="pfp"
                            class="w-16 h-16 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600 transition-colors duration-300" alt="Profile Picture" />
                        <div v-else
                            class="w-16 h-16 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-300 flex items-center justify-center rounded-full transition-colors duration-300">
                            <i class="bx bxs-user text-2xl"></i>
                        </div>
                        <div @click="triggerFileInput"
                            class="absolute inset-0 flex items-center justify-center bg-black/10 dark:bg-black/30 hover:bg-black/20 dark:hover:bg-black/40 text-white font-semibold rounded-full cursor-pointer opacity-0 hover:opacity-100 transition duration-200">
                            Cambiar
                        </div>
                    </div>
                    <input type="file" ref="fileInputRef" @change="uploadProfilePicture" class="hidden" />
                    <div class="ml-4">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Foto de Perfil</h2>
                        <p class="text-gray-600 dark:text-gray-400">Personaliza tu foto de perfil.</p>
                    </div>
                </div>
            </div>



            <div
                class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-2xl space-y-6 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
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
                        <span class="text-gray-700 dark:text-gray-200">Modo Oscuro</span>
                        <label class="relative inline-flex items-center cursor-pointer ml-2">
                            <input type="checkbox" v-model="isDarkMode" @change="toggleDarkMode" class="sr-only peer">
                            <div class="w-11 h-6 rounded-full transition-all duration-300 bg-gray-300 dark:bg-gray-700"
                                :class="isDarkMode ? 'bg-blue-500 dark:bg-blue-600' : 'bg-gray-300 dark:bg-gray-700'">
                                <div class="absolute top-1 left-1 h-4 w-4 bg-white dark:bg-gray-300 border border-gray-300 dark:border-gray-500 rounded-full transition-all duration-300"
                                    :class="isDarkMode ? 'translate-x-5 border-white dark:border-gray-300' : ''"></div>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Personalización Avanzada</h3>
                    <div v-if="updateAppearanceSuccess"
                        class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded shadow transition-colors duration-300">
                        Apariencia actualizada exitosamente.
                    </div>
                    <div v-if="updateAppearanceErrors.general"
                        class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded shadow transition-colors duration-300">
                        {{ updateAppearanceErrors.general }}
                    </div>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Explora opciones adicionales para configurar tu interfaz, como la selección de colores de acento
                        y ajustes de fuente para mejorar la legibilidad.
                    </p>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Color de
                                Acento</label>
                            <select v-model="highlightColor"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors duration-300">
                                <option :value="isDarkMode ? '#ffffff' : '#000000'">
                                    {{ isDarkMode ? 'Blanco' : 'Negro' }}
                                </option>
                                <option value="#0000ff">Azul</option>
                                <option value="#ff0000">Rojo</option>
                                <option value="#00ff00">Verde</option>
                                <option value="#800080">Púrpura</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tamaño de
                                Fuente de las Ofertas</label>
                            <input v-model="fontSize" type="range" min="12" max="24" class="w-full mt-1 accent-blue-500 dark:accent-blue-400" />
                        </div>
                        <div class="flex justify-end mt-4"></div>
                        <button @click="resetToDefaults"
                            class="bg-gradient-to-r from-[#193CB8] to-[#2748c6] px-4 py-2 rounded hover:bg-blue-600 cursor-pointer text-white transition duration-300">
                            Restablecer
                        </button>
                    </div>
                    <div class="mt-4 flex flex-col space-y-2">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Previsualización del Tema
                        </h3>
                        <div class="p-4 bg-gray-100 dark:bg-gray-900 rounded-lg shadow-inner transition-colors duration-300">
                            <p class="text-sm text-gray-700 dark:text-gray-300" :style="{ fontSize: fontSize + 'px' }">
                                Esta es una previsualización del <span class="font-bold"
                                    :style="{ color: highlightColor }">tema seleccionado</span>. Cambia las opciones de
                                personalización y
                                observa cómo se transforma el entorno de tu aplicación.
                            </p>
                        </div>
                        <button @click="updateAppearanceInfo" :disabled="isAppearanceUpdating"
                            class="mt-2 bg-gradient-to-r from-[#193CB8] to-[#2748c6] px-4 py-2 rounded hover:bg-blue-600 cursor-pointer text-white transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ isAppearanceUpdating ? 'Actualizando...' : 'Actualizar apariencia' }}
                        </button>
                    </div>
                </div>
            </div>


            <!-- <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl border border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">IDIOMA</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Selecciona tu idioma:</p>
                <div class="grid grid-cols-3 gap-4">
                    <div v-for="lang in languages" :key="lang.code"
                        @click="selectedLanguage = lang.code; changeLanguage()" :class="[
                            'cursor-pointer p-4 rounded text-center',
                            selectedLanguage === lang.code ? 'bg-blue-200' : 'bg-white dark:bg-gray-800'
                        ]">
                        <img :src="lang.flag" alt="" class="mx-auto mb-2 w-20 h-12" />
                        <span class="block text-sm font-semibold">{{ lang.name }}</span>
                    </div>
                </div>
            </div> -->

        </div>

        <!-- SECURITY SECTION -->
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mt-6 px-4 border-t pt-4 dark:border-gray-700">SEGURIDAD</h1>
        <p class="px-4 mt-4 text-gray-700 dark:text-gray-300">Añade una capa extra de seguridad a tu cuenta.</p>
        <div class="p-4 grid grid-cols-2 gap-4">
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 shadow-xl transition-colors duration-300">
                <p class="text-xl font-bold text-gray-800 dark:text-gray-100">Autenticación de Dos Factores (2FA)</p>
                <p class="mt-2 text-justify text-gray-700 dark:text-gray-300">
                    La Autenticación de Dos Factores añade una capa extra de seguridad a tu cuenta.
                    Al requerir una segunda forma de verificación, ayuda a prevenir accesos no autorizados,
                    incluso si alguien conoce tu contraseña.
                </p>
                <p class="mt-2 text-justify text-gray-700 dark:text-gray-300">
                    Una vez habilitada, se te pedirá un código de verificación enviado a tu dispositivo cada vez
                    que inicies sesión. Puedes usar una aplicación de autenticación para completar este proceso.
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
                    'bg-gradient-to-r from-[#193CB8] to-[#2748c6] hover:bg-blue-600'
                ]">
                    {{ isTwoFactorEnabled ? "DESACTIVAR 2FA (NO RECOMENDADO)" : "ACTIVAR 2FA" }}
                </button>
            </div>
        </div>

        <!-- QR Code Modal -->
        <div v-if="qrCode && qrModal" class="fixed inset-0 flex items-center justify-center bg-black/50 dark:bg-black/80 p-4 z-50">
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-2xl w-full max-w-md text-center border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <h2 class="text-2xl font-bold text-blue-700 dark:text-blue-300 mb-2">Escanea este QR</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Usa tu app de autenticación para activar 2FA</p>
            <div class="flex justify-center mb-4">
                <div v-html="qrCode" class="p-4 bg-gray-100 rounded-lg inline-block border border-gray-200 dark:border-gray-700 shadow transition-colors duration-300"></div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 mt-4">
                <button @click="qrModal = false"
                class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition duration-300">
                Confirmar
                </button>
                <button @click="disable2FA()"
                class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition duration-300">
                Cancelar
                </button>
            </div>
            </div>
        </div>

        <!-- Disable 2FA Confirmation Modal -->
        <div v-if="disableConfirmModal" class="fixed inset-0 flex items-center justify-center bg-black/50 dark:bg-black/80 p-4 z-50">
            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg w-full max-w-md text-center border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <h2 class="text-xl font-bold text-red-600 dark:text-red-400 mb-4">ADVERTENCIA: Deshabilitar 2FA</h2>
            <div class="p-4 bg-red-100 dark:bg-red-800 rounded-lg mb-4">
                <p class="text-red-800 dark:text-red-200 font-semibold">Esta acción no es recomendable</p>
            </div>
            <p class="text-gray-700 dark:text-gray-300 text-justify mb-4">
                Deshabilitar la Autenticación de Dos Factores reducirá significativamente la seguridad de tu cuenta.
                Sin 2FA, tu cuenta será más vulnerable a accesos no autorizados.
            </p>
            <p class="text-gray-700 dark:text-gray-300 text-justify mb-4">
                Recomendamos encarecidamente mantener 2FA habilitado para proteger tu información personal y datos.
            </p>
            <div class="flex justify-between gap-2">
                <button @click="closeDisableConfirmation()"
                class="mt-4 px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-lg hover:bg-blue-600 dark:hover:bg-blue-700 cursor-pointer transition duration-300 flex-1">
                Cancelar
                </button>
                <button @click="disable2FA()" :disabled="!confirmButtonEnabled"
                class="mt-4 px-4 py-2 rounded-lg transition duration-300 flex-1 flex items-center justify-center"
                :class="confirmButtonEnabled ? 'bg-red-500 dark:bg-red-600 hover:bg-red-600 dark:hover:bg-red-700 text-white cursor-pointer' : 'bg-gray-500 dark:bg-gray-600 text-gray-300 cursor-not-allowed'">
                <span v-if="!confirmButtonEnabled">Espera {{ confirmCountdown }}s</span>
                <span v-else>Entiendo, deshabilitar 2FA</span>
                </button>
            </div>
            </div>
        </div>
    </Layout>
</template>
