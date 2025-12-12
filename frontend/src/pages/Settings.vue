<template>
  <div class="min-h-screen bg-white">
    <main class="min-h-screen">
        <div class="bg-[#f34767] h-32 lg:h-28 px-4 lg:px-8 flex items-center justify-between rounded-40 md:block hidden">
          <div class="flex items-center space-x-4">
            <div class="w-10 h-10 rounded-lg bg-opacity-20 flex items-center justify-center">
              <img :src="logoWhite" alt="Peerie Logo" class="w-10 h-10">
            </div>
            <h1 class="text-white text-4xl lg:text-3xl font-bold">{{ texts.headerTitle }}</h1>
          </div>
          <div class="flex items-center space-x-2 text-white text-base lg:text-xl font-medium">
            <span>{{ texts.headerAccount }}</span>
            <span class="opacity-40">|</span>
            <span class="opacity-40">{{ texts.headerTitle }}</span>
          </div>
      </div>

      <div class="md:hidden px-4 pt-4 pb-2">
        <div class="flex gap-2">
          <button
            @click="activeTab = 'profile'"
            class="flex-1 rounded-lg px-4 py-2 text-base font-medium transition-colors"
            :class="activeTab === 'profile' 
              ? 'bg-[#f34767] text-white' 
              : 'bg-white text-[#1c1a1b] border border-[#DCDCDC]'"
          >
            {{ texts.profile }}
          </button>
          <button
            @click="activeTab = 'account'"
            class="flex-1 rounded-lg px-4 py-2 text-base font-medium transition-colors flex items-center justify-center gap-2"
            :class="activeTab === 'account' 
              ? 'bg-[#f34767] text-white' 
              : 'bg-white text-[#1c1a1b] border border-[#DCDCDC]'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            {{ texts.headerAccount }}
          </button>
        </div>
      </div>

        <div class="2xl:mt-[65px] xl:mt-[30px] md:mt-0">
          <div class="2xl:pl-[45px] xl:pl-[30px] md:px-4">
            <div v-show="activeTab === 'profile' || !isMobile">
              <h2 class="text-[#1c1a1b] text-2xl md:text-3xl lg:text-4xl font-extrabold mb-2 px-4 md:px-0 pt-4 md:pt-0">{{ texts.personalInfoTitle }}</h2>
              <p class="text-[#1c1a1b] text-sm md:text-base lg:text-xl mb-6 px-4 md:px-0">
                {{ texts.personalInfoDescription }}
                <span class="text-red-500">*</span>
              </p>
              
              <form @submit.prevent="updateProfile" class="space-y-4 md:space-y-6 px-4 md:px-0 pb-6 md:pb-0">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                  <div class="border-2 border-[#3f4369] rounded-[20px] md:rounded-[30px] p-4 md:2xl:p-6 md:xl:p-4">
                    <label class="block text-[#1c1a1b] text-base md:text-lg font-bold mb-2">
                      {{ texts.firstNameLabel }}
                      <span class="text-red-500">*</span>
                    </label>
                    <input 
                      v-model="profileForm.firstName"
                      type="text" 
                      class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-sm md:text-base placeholder:text-gray-400"
                      :placeholder="texts.firstNamePlaceholder"
                      required
                    >
                  </div>
                  <div class="border-2 border-[#3f4369] rounded-[20px] md:rounded-[30px] p-4 md:2xl:p-6 md:xl:p-4">
                    <label class="block text-[#1c1a1b] text-base md:text-lg font-bold mb-2">
                      {{ texts.lastNameLabel }}
                      <span class="text-red-500">*</span>
                    </label>
                    <input 
                      v-model="profileForm.lastName"
                      type="text" 
                      class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-sm md:text-base placeholder:text-gray-400"
                      :placeholder="texts.lastNamePlaceholder"
                      required
                    >
                  </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                  <div class="border-2 border-[#3f4369] rounded-[20px] md:rounded-[30px] p-4 md:2xl:p-6 md:xl:p-4">
                    <label class="block text-[#1c1a1b] text-base md:text-lg font-bold mb-2">
                      {{ texts.emailLabel }}
                      <span class="text-red-500">*</span>
                    </label>
                    <input 
                      v-model="profileForm.email"
                      type="email" 
                      class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-sm md:text-base placeholder:text-gray-400"
                      :placeholder="texts.emailPlaceholder"
                      required
                    >
                  </div>
                  <div class="border-2 border-[#3f4369] rounded-[20px] md:rounded-[30px] p-4 md:2xl:p-6 md:xl:p-4 relative">
                    <label class="block text-[#1c1a1b] text-base md:text-lg font-bold mb-2">
                      {{ texts.languageLabel }}
                      <span class="text-red-500">*</span>
                    </label>
                    <select 
                      v-model="profileForm.language"
                      class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-sm md:text-base appearance-none pr-10"
                    >
                      <option value="en">{{ texts.languageEn }}</option>
                      <option value="de">{{ texts.languageDe }}</option>
                    </select>
                    <div class="absolute right-4 md:right-6 top-1/2 transform -translate-y-1/2 pointer-events-none w-[40px] h-[40px] md:w-[60px] md:h-[60px] rounded-[12px] md:rounded-[16px] bg-[#3F4369] flex items-center justify-center">
                      <img :src="arrowDown" alt="" class="w-5 h-5 md:w-7 md:h-7">
                    </div>
                  </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-end pt-4">
                  <button 
                    type="button"
                    @click="resetForm"
                    class="flex items-center justify-center gap-2 px-4 md:px-6 py-2 md:py-3 text-[#1c1a1b] text-sm md:text-xl font-bold uppercase hover:opacity-80 transition-opacity"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ texts.resetButton }}
                  </button>
                  <button 
                    type="submit" 
                    :disabled="isUpdating"
                    class="px-6 md:px-8 py-2 md:py-3 bg-white border-2 border-[#f34767] text-[#f34767] rounded-[20px] md:rounded-[30px] text-sm md:text-xl font-bold uppercase hover:bg-[#f34767] hover:text-white transition-colors disabled:opacity-50"
                  >
                    {{ isUpdating ? texts.saving : texts.saveChanges }}
                  </button>
                </div>
              </form>
            </div>
        </div>

          <div class="bg-white rounded-[20px] md:rounded-[36px] p-4 md:p-6 lg:p-8 mt-6 md:mt-0">
            <div v-show="activeTab === 'account' || !isMobile">
              <h2 class="text-[#1c1a1b] text-2xl md:text-3xl lg:text-4xl font-extrabold mb-2 px-4 md:px-0 pt-4 md:pt-0">{{ texts.securityTitle }}</h2>
              <p class="text-[#1c1a1b] text-sm md:text-base lg:text-xl mb-6 px-4 md:px-0">{{ texts.securityDescription }}</p>
              
              <form @submit.prevent="updatePassword" class="space-y-4 md:space-y-6 px-4 md:px-0 pb-6 md:pb-0">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                  <div class="bg-white border-2 border-[#3f4369] rounded-[20px] md:rounded-[30px] p-4 md:p-6">
                    <label class="block text-[#1c1a1b] text-base md:text-lg font-bold mb-2">
                      {{ texts.passwordLabel }}
                      <span class="text-red-500">*</span>
                    </label>
                    <input 
                      v-model="passwordForm.password"
                      type="password" 
                      class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-sm md:text-base placeholder:text-gray-400"
                      :placeholder="texts.passwordPlaceholder"
                      required
                      minlength="8"
                    >
                  </div>
                  <div class="bg-white border-2 border-[#3f4369] rounded-[20px] md:rounded-[30px] p-4 md:p-6">
                    <label class="block text-[#1c1a1b] text-base md:text-lg font-bold mb-2">
                      {{ texts.confirmPasswordLabel }}
                      <span class="text-red-500">*</span>
                    </label>
                    <input 
                      v-model="passwordForm.confirmPassword"
                      type="password" 
                      class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-sm md:text-base placeholder:text-gray-400"
                      :placeholder="texts.confirmPasswordPlaceholder"
                      required
                      minlength="8"
                    >
                  </div>
                </div>

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pt-4">
                  <button 
                    type="button"
                    @click="deleteAccount"
                    class="text-[#f34767] text-base md:text-xl underline hover:opacity-80 transition-opacity text-left md:text-left"
                  >
                    {{ texts.deleteAccount }}
                  </button>
                  <div class="flex flex-col sm:flex-row gap-3 md:gap-4">
                    <button 
                      type="button"
                      @click="resetPasswordForm"
                      class="flex items-center justify-center gap-2 px-4 md:px-6 py-2 md:py-3 text-[#1c1a1b] text-sm md:text-xl font-bold uppercase hover:opacity-80 transition-opacity"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                      {{ texts.resetButton }}
                    </button>
                    <button 
                      type="submit" 
                      :disabled="isUpdating"
                      class="px-6 md:px-8 py-2 md:py-3 bg-white border-2 border-[#f34767] text-[#f34767] rounded-[20px] md:rounded-[30px] text-sm md:text-xl font-bold uppercase hover:bg-[#f34767] hover:text-white transition-colors disabled:opacity-50"
                    >
                      {{ isUpdating ? texts.updating : texts.saveChanges }}
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
    </main>
    </div>

    <div v-if="message" class="fixed top-4 right-4 z-50">
      <div class="p-4 rounded-lg shadow-lg" :class="message.type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
        {{ message.text }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'
import logoWhite from '@/assets/images/logos/logo-white.svg'
import arrowDown from '@/assets/images/icons/account/arrow-down.svg'

const router = useRouter()
const languageStore = useLanguageStore()

const isUpdating = ref(false)
const message = ref(null)
const activeTab = ref('profile')
const isMobile = ref(false)

const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})

const profileForm = ref({
  firstName: '',
  lastName: '',
  email: '',
  language: 'en'
})

const passwordForm = ref({
  currentPassword: '',
  password: '',
  confirmPassword: '',
  code: ''
})

const user = ref(null)
const originalEmail = ref('')

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      headerTitle: 'Profil',
      headerAccount: 'Konto',
      profile: 'Profil',
      personalInfoTitle: 'Persönliche Daten',
      personalInfoDescription: 'Bitte fülle alle Pflichtfelder aus, die mit',
      firstNameLabel: 'Vorname',
      firstNamePlaceholder: 'Vornamen eingeben',
      lastNameLabel: 'Nachname',
      lastNamePlaceholder: 'Nachnamen eingeben',
      emailLabel: 'E-Mail',
      emailPlaceholder: 'E-Mail eingeben',
      languageLabel: 'Sprache',
      languageEn: 'Englisch',
      languageDe: 'Deutsch',
      resetButton: 'Zurücksetzen',
      saveChanges: 'Änderungen speichern',
      saving: 'Speichern...',
      securityTitle: 'Sicherheitseinstellungen',
      securityDescription: 'Verwende ein starkes Passwort mit mindestens 8 Zeichen, einer Zahl und einem Sonderzeichen',
      passwordLabel: 'Passwort',
      passwordPlaceholder: 'Neues Passwort eingeben',
      confirmPasswordLabel: 'Passwort bestätigen',
      confirmPasswordPlaceholder: 'Neues Passwort bestätigen',
      deleteAccount: 'Mein Konto löschen',
      updating: 'Aktualisiere...',
      profileUpdated: 'Profil wurde erfolgreich aktualisiert!',
      profileUpdateError: 'Fehler beim Aktualisieren des Profils',
      passwordsDontMatch: 'Passwörter stimmen nicht überein',
      passwordUpdated: 'Passwort wurde erfolgreich aktualisiert!',
      passwordUpdateError: 'Fehler beim Aktualisieren des Passworts',
      newsletterConsentError: 'Bitte stimme dem Erhalt von Marketing-E-Mails zu',
      newsletterSubscribed: 'Erfolgreich zum Newsletter angemeldet!',
      newsletterError: 'Fehler bei der Newsletter-Anmeldung',
      deleteAccountConfirm: 'Bist du sicher, dass du dein Konto löschen möchtest? Diese Aktion kann nicht rückgängig gemacht werden.',
      deleteAccountError: 'Fehler beim Löschen des Kontos',
    }
  }

  return {
    headerTitle: 'Profile',
    headerAccount: 'Account',
    personalInfoTitle: 'Personal Information',
    personalInfoDescription: 'Please fill in all required fields marked with',
    firstNameLabel: 'First name',
    firstNamePlaceholder: 'Enter first name',
    lastNameLabel: 'Last name',
    lastNamePlaceholder: 'Enter last name',
    emailLabel: 'E-mail',
    emailPlaceholder: 'Enter email',
    languageLabel: 'Language',
    languageEn: 'English',
    languageDe: 'German',
    resetButton: 'Reset',
    saveChanges: 'Save changes',
    saving: 'Saving...',
    securityTitle: 'Security Settings',
    securityDescription: 'Use a strong password with at least 8 characters, including a number and a special symbol',
    passwordLabel: 'Password',
    passwordPlaceholder: 'Enter new password',
    confirmPasswordLabel: 'Confirm password',
    confirmPasswordPlaceholder: 'Confirm new password',
      deleteAccount: 'Delete my account',
      profile: 'Profile',
    updating: 'Updating...',
    profileUpdated: 'Profile updated successfully!',
    profileUpdateError: 'Profile update error',
    passwordsDontMatch: 'Passwords do not match',
    passwordUpdated: 'Password updated successfully!',
    passwordUpdateError: 'Password update error',
    newsletterConsentError: 'Please consent to receive marketing communications',
    newsletterSubscribed: 'Successfully subscribed to newsletter!',
    newsletterError: 'Subscription error',
    deleteAccountConfirm: 'Are you sure you want to delete your account? This action cannot be undone.',
    deleteAccountError: 'Account deletion error',
      emailChangeCodeSent: 'We have sent a verification code to your new email address.',
      emailChangeConfirmed: 'Email updated successfully!',
      emailChangeError: 'Email change error',
      passwordCodeSent: 'Password change code sent to your email.',
      passwordCodeError: 'Password change code error',
      currentPasswordRequired: 'Please enter your current password.',
      currentPasswordLabel: 'Current password',
      currentPasswordPlaceholder: 'Enter current password',
      passwordCodeLabel: 'Verification code',
      passwordCodePlaceholder: 'Enter code from email',
  }
})

const showMessage = (text, type = 'success') => {
  message.value = { text, type }
  setTimeout(() => {
    message.value = null
  }, 5000)
}

const updateProfile = async () => {
  isUpdating.value = true
  try {
    await axios.put('/api/profile', {
      name: `${profileForm.value.firstName} ${profileForm.value.lastName}`.trim(),
      language: profileForm.value.language
    })
    languageStore.setLanguage(profileForm.value.language)

    const updatedName = `${profileForm.value.firstName} ${profileForm.value.lastName}`.trim()
    window.dispatchEvent(new CustomEvent('profile-updated', {
      detail: {
        name: updatedName,
        email: profileForm.value.email,
        language: profileForm.value.language
      }
    }))

    const emailChanged = profileForm.value.email !== originalEmail.value

    if (emailChanged) {
      await axios.post('/api/profile/email/request-change', {
        new_email: profileForm.value.email
      })
      showMessage(texts.value.emailChangeCodeSent)
    } else {
      showMessage(texts.value.profileUpdated)
    }
  } catch (error) {
    showMessage(error.response?.data?.message || texts.value.profileUpdateError, 'error')
  } finally {
    isUpdating.value = false
  }
}

const updatePassword = async () => {
  if (passwordForm.value.password !== passwordForm.value.confirmPassword) {
    showMessage(texts.value.passwordsDontMatch, 'error')
    return
  }

  isUpdating.value = true
  try {
    await axios.put('/api/profile/password', {
      current_password: passwordForm.value.currentPassword,
      new_password: passwordForm.value.password,
      new_password_confirmation: passwordForm.value.confirmPassword,
      code: passwordForm.value.code
    })
    showMessage(texts.value.passwordUpdated)
    resetPasswordForm()
  } catch (error) {
    showMessage(error.response?.data?.message || texts.value.passwordUpdateError, 'error')
  } finally {
    isUpdating.value = false
  }
}

const resetForm = () => {
  fetchUser()
}

const resetPasswordForm = () => {
  passwordForm.value = {
    currentPassword: '',
    password: '',
    confirmPassword: '',
    code: ''
  }
}

const deleteAccount = async () => {
  if (!confirm(texts.value.deleteAccountConfirm)) {
    return
  }
  
  try {
    await axios.delete('/api/profile')
    localStorage.removeItem('user')
    router.push('/login')
  } catch (error) {
    showMessage(error.response?.data?.message || texts.value.deleteAccountError, 'error')
  }
}

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/user')
    
    if (response.data.success) {
      user.value = {
        id: response.data.id,
        name: response.data.name,
        email: response.data.email,
        avatar: response.data.avatar
      }
      
      const nameParts = response.data.name?.split(' ') || []
    profileForm.value = {
        firstName: nameParts[0] || '',
        lastName: nameParts.slice(1).join(' ') || '',
        email: response.data.email || '',
        language: response.data.language || 'en'
      }
      originalEmail.value = response.data.email || ''
    } else {
      throw new Error('Failed to fetch user')
    }
  } catch (error) {
    console.error('Fetch user error:', error)
    
    if (error.response?.status === 401) {
      router.push('/login')
    }
  }
}

onMounted(() => {
  fetchUser()
})
</script>