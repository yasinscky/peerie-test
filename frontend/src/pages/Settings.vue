<template>
  <div class="min-h-screen bg-white">
    <main class="min-h-screen">
        <div class="bg-[#f34767] h-32 lg:h-36 px-4 lg:px-8 flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="w-10 h-10 rounded-lg bg-white bg-opacity-20 flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </div>
            <h1 class="text-white text-4xl lg:text-6xl font-bold">{{ texts.headerTitle }}</h1>
          </div>
          <div class="flex items-center space-x-2 text-white text-base lg:text-xl font-medium">
            <span>{{ texts.headerAccount }}</span>
            <span class="opacity-40">|</span>
            <span class="opacity-40">{{ texts.headerTitle }}</span>
          </div>
      </div>

        <div class="p-4 lg:p-8 space-y-8">
          <div class="bg-white border-2 border-[#3f4369] rounded-[36px] p-6 lg:p-8">
            <h2 class="text-[#1c1a1b] text-3xl lg:text-4xl font-extrabold mb-2">{{ texts.personalInfoTitle }}</h2>
            <p class="text-[#1c1a1b] text-base lg:text-xl mb-6">
              {{ texts.personalInfoDescription }}
              <span class="text-red-500">*</span>
            </p>
            
            <form @submit.prevent="updateProfile" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border-2 border-[#3f4369] rounded-[30px] p-6">
                  <label class="block text-[#1c1a1b] text-lg font-bold mb-2">{{ texts.firstNameLabel }}</label>
                  <input 
                    v-model="profileForm.firstName"
                    type="text" 
                    class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-base"
                    :placeholder="texts.firstNamePlaceholder"
                    required
                  >
                </div>
                <div class="bg-white border-2 border-[#3f4369] rounded-[30px] p-6">
                  <label class="block text-[#1c1a1b] text-lg font-bold mb-2">{{ texts.lastNameLabel }}</label>
                <input 
                    v-model="profileForm.lastName"
                  type="text" 
                    class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-base"
                    :placeholder="texts.lastNamePlaceholder"
                  required
                >
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border-2 border-[#3f4369] rounded-[30px] p-6">
                  <label class="block text-[#1c1a1b] text-lg font-bold mb-2">{{ texts.emailLabel }}</label>
                <input 
                  v-model="profileForm.email"
                  type="email" 
                    class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-base"
                    :placeholder="texts.emailPlaceholder"
                  required
                >
                </div>
                <div class="bg-white border-2 border-[#3f4369] rounded-[30px] p-6 relative">
                  <label class="block text-[#1c1a1b] text-lg font-bold mb-2">{{ texts.languageLabel }}</label>
                  <select 
                    v-model="profileForm.language"
                    class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-base appearance-none pr-10"
                  >
                    <option value="en">{{ texts.languageEn }}</option>
                    <option value="de">{{ texts.languageDe }}</option>
                  </select>
                  <div class="absolute right-6 top-1/2 transform -translate-y-1/2 pointer-events-none">
                    <svg class="w-6 h-6 text-[#3f4369]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </div>
              </div>

              <div class="flex flex-col sm:flex-row gap-4 justify-end">
                <button 
                  type="button"
                  @click="resetForm"
                  class="px-6 py-3 text-[#f34767] text-xl font-bold uppercase hover:opacity-80 transition-opacity"
                >
                  {{ texts.resetButton }}
                </button>
                <button 
                  type="submit" 
                  :disabled="isUpdating"
                  class="px-8 py-3 bg-white border-2 border-[#f34767] text-[#f34767] rounded-[30px] text-xl font-bold uppercase hover:bg-[#f34767] hover:text-white transition-colors disabled:opacity-50"
                >
                  {{ isUpdating ? texts.saving : texts.saveChanges }}
                </button>
              </div>
            </form>
        </div>

          <div class="bg-white border-2 border-[#3f4369] rounded-[36px] p-6 lg:p-8">
            <h2 class="text-[#1c1a1b] text-3xl lg:text-4xl font-extrabold mb-2">{{ texts.securityTitle }}</h2>
            <p class="text-[#1c1a1b] text-base lg:text-xl mb-6">{{ texts.securityDescription }}</p>
            
            <form @submit.prevent="updatePassword" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border-2 border-[#3f4369] rounded-[30px] p-6">
                  <label class="block text-[#1c1a1b] text-lg font-bold mb-2">{{ texts.passwordLabel }}</label>
                <input 
                    v-model="passwordForm.password"
                  type="password" 
                    class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-base"
                    :placeholder="texts.passwordPlaceholder"
                  required
                  minlength="8"
                >
              </div>
                <div class="bg-white border-2 border-[#3f4369] rounded-[30px] p-6">
                  <label class="block text-[#1c1a1b] text-lg font-bold mb-2">{{ texts.confirmPasswordLabel }}</label>
                <input 
                  v-model="passwordForm.confirmPassword"
                  type="password" 
                    class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-base"
                    :placeholder="texts.confirmPasswordPlaceholder"
                  required
                  minlength="8"
                >
                </div>
              </div>

              <div class="flex items-center justify-between">
                <button 
                  type="button"
                  @click="deleteAccount"
                  class="text-[#f34767] text-xl underline hover:opacity-80 transition-opacity"
                >
                  {{ texts.deleteAccount }}
                </button>
                <div class="flex gap-4">
                  <button 
                    type="button"
                    @click="resetPasswordForm"
                    class="px-6 py-3 text-[#f34767] text-xl font-bold uppercase hover:opacity-80 transition-opacity"
                  >
                    {{ texts.resetButton }}
                  </button>
                  <button 
                    type="submit" 
                    :disabled="isUpdating"
                    class="px-8 py-3 bg-white border-2 border-[#f34767] text-[#f34767] rounded-[30px] text-xl font-bold uppercase hover:bg-[#f34767] hover:text-white transition-colors disabled:opacity-50"
                  >
                    {{ isUpdating ? texts.updating : texts.saveChanges }}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
    </main>

    <div v-if="message" class="fixed top-4 right-4 z-50">
      <div class="p-4 rounded-lg shadow-lg" :class="message.type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
        {{ message.text }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'

const router = useRouter()
const languageStore = useLanguageStore()

const isUpdating = ref(false)
const message = ref(null)
const newsletterEmail = ref('')
const newsletterConsent = ref(false)

const profileForm = ref({
  firstName: '',
  lastName: '',
  email: '',
  language: 'en'
})

const passwordForm = ref({
  password: '',
  confirmPassword: ''
})

const user = ref(null)

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      headerTitle: 'Profil',
      headerAccount: 'Konto',
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
  }
})

const userInitials = computed(() => {
  if (!user.value?.name) return 'U'
  const names = user.value.name.split(' ')
  if (names.length >= 2) {
    return (names[0][0] + names[1][0]).toUpperCase()
  }
  return names[0][0].toUpperCase()
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
      email: profileForm.value.email,
      language: profileForm.value.language
    })
    languageStore.setLanguage(profileForm.value.language)
    showMessage(texts.value.profileUpdated)
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
      password: passwordForm.value.password,
      password_confirmation: passwordForm.value.confirmPassword
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
    password: '',
    confirmPassword: ''
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

const subscribeNewsletter = async () => {
  if (!newsletterConsent.value) {
    showMessage(texts.value.newsletterConsentError, 'error')
    return
  }
  
  try {
    await axios.post('/api/newsletter/subscribe', {
      email: newsletterEmail.value
    })
    showMessage(texts.value.newsletterSubscribed)
    newsletterEmail.value = ''
    newsletterConsent.value = false
  } catch (error) {
    showMessage(error.response?.data?.message || texts.value.newsletterError, 'error')
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