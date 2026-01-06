<template>
  <div class="min-h-screen bg-white">
    <nav class="relative px-6 py-4 bg-white">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <router-link to="/" class="flex items-center cursor-pointer">
          <img :src="logoImage" alt="Peerie" class="h-12 w-auto">
        </router-link>

        <div class="hidden md:flex items-center space-x-4">
          <div class="h-[40px] rounded-[10px] border-2 border-[#DCDCDC] overflow-hidden inline-flex">
            <button
              type="button"
              class="px-3 font-bold uppercase text-[12px] transition-colors"
              :class="languageStore.language === 'en' ? 'bg-[#3F4369] text-white' : 'bg-white text-[#3F4369]'"
              @click="languageStore.setLanguage('en')"
            >
              EN
            </button>
            <button
              type="button"
              class="px-3 font-bold uppercase text-[12px] transition-colors"
              :class="languageStore.language === 'de' ? 'bg-[#3F4369] text-white' : 'bg-white text-[#3F4369]'"
              @click="languageStore.setLanguage('de')"
            >
              DE
            </button>
          </div>
          <router-link 
            to="/login"
            class="px-6 py-3 text-[#3F4369] border-2 border-[#3F4369] rounded-[15px] hover:bg-[#3F4369] hover:text-white transition-all duration-200 font-bold uppercase text-sm"
          >
            {{ texts.navLogin }}
          </router-link>
          <router-link 
            to="/register"
            class="px-6 py-3 bg-[#F34767] text-white rounded-[15px] hover:bg-[#d93d5a] transition-all duration-200 font-bold uppercase text-sm shadow-lg"
          >
            {{ texts.navGetStarted }}
          </router-link>
        </div>

        <div class="md:hidden flex items-center gap-3">
          <div class="h-[36px] rounded-[10px] border-2 border-[#DCDCDC] overflow-hidden inline-flex">
            <button
              type="button"
              class="px-3 font-bold uppercase text-[12px] transition-colors"
              :class="languageStore.language === 'en' ? 'bg-[#3F4369] text-white' : 'bg-white text-[#3F4369]'"
              @click="languageStore.setLanguage('en')"
            >
              EN
            </button>
            <button
              type="button"
              class="px-3 font-bold uppercase text-[12px] transition-colors"
              :class="languageStore.language === 'de' ? 'bg-[#3F4369] text-white' : 'bg-white text-[#3F4369]'"
              @click="languageStore.setLanguage('de')"
            >
              DE
            </button>
          </div>
          <button 
            type="button"
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="p-2 text-[#1c1a1b]"
            :aria-label="texts.navMenuLabel"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <div 
        v-if="mobileMenuOpen"
        class="md:hidden absolute top-full left-0 right-0 bg-white border-t border-[#DCDCDC] shadow-lg z-50"
      >
        <div class="px-6 py-4 space-y-3">
          <router-link 
            to="/login"
            @click="mobileMenuOpen = false"
            class="block w-full px-4 py-3 text-center text-[#3F4369] border-2 border-[#3F4369] rounded-[15px] hover:bg-[#3F4369] hover:text-white transition-all duration-200 font-bold uppercase text-sm"
          >
            {{ texts.navLogin }}
          </router-link>
          <router-link 
            to="/register"
            @click="mobileMenuOpen = false"
            class="block w-full px-4 py-3 text-center bg-[#F34767] text-white rounded-[15px] hover:bg-[#d93d5a] transition-all duration-200 font-bold uppercase text-sm shadow-lg"
          >
            {{ texts.navGetStarted }}
          </router-link>
        </div>
      </div>
    </nav>

    <div class="relative px-6 py-12 md:py-20 bg-white overflow-hidden min-h-[calc(100vh-200px)]">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div class="relative z-10">
            <h1 class="text-6xl md:text-7xl lg:text-[96px] font-bold text-[#1C1A1B] mb-6 leading-[0.95]">
              {{ texts.title }}
            </h1>
            <p class="text-3xl md:text-4xl lg:text-[48px] font-bold text-[#1C1A1B] mb-12 tracking-[-1.92px]">
              {{ texts.subtitle }}
            </p>

            <form @submit.prevent="handleRegister" class="space-y-6">
              <div class="relative">
                <div class="bg-white border-2 border-[#3F4369] rounded-[30px] h-24 flex items-center px-6">
                  <input
                    v-model="form.name"
                    type="text"
                    class="w-full bg-transparent border-0 outline-none text-[#1C1A1B] text-xl font-bold placeholder-[#1C1A1B]"
                    :placeholder="texts.fullNamePlaceholder"
                    autocomplete="name"
                    required
                  >
                </div>
              </div>

              <div class="relative">
                <div class="bg-white border-2 border-[#3F4369] rounded-[30px] h-24 flex items-center px-6">
                  <input
                    v-model="form.email"
                    type="email"
                    class="w-full bg-transparent border-0 outline-none text-[#1C1A1B] text-xl font-bold placeholder-[#1C1A1B]"
                    :placeholder="texts.emailPlaceholder"
                    autocomplete="email"
                    required
                  >
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="relative">
                  <div class="bg-white border-2 border-[#3F4369] rounded-[30px] h-24 flex items-center px-6">
                    <input
                      v-model="form.password"
                      type="password"
                      class="w-full bg-transparent border-0 outline-none text-[#1C1A1B] text-xl font-bold placeholder-[#1C1A1B]"
                      :placeholder="texts.passwordPlaceholder"
                      autocomplete="new-password"
                      minlength="8"
                      required
                    >
                  </div>
                </div>
                <div class="relative">
                  <div class="bg-white border-2 border-[#3F4369] rounded-[30px] h-24 flex items-center px-6">
                    <input
                      v-model="form.password_confirmation"
                      type="password"
                      class="w-full bg-transparent border-0 outline-none text-[#1C1A1B] text-xl font-bold placeholder-[#1C1A1B]"
                      :placeholder="texts.confirmPasswordPlaceholder"
                      autocomplete="new-password"
                      minlength="8"
                      required
                    >
                  </div>
                </div>
              </div>

              <div class="relative">
                <button
                  type="submit"
                  class="w-full bg-white border-2 border-[#F34767] rounded-[30px] h-24 flex items-center justify-center text-[#F34767] text-2xl font-bold uppercase transition-all duration-200 hover:bg-[#F34767] hover:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="isLoading"
                >
                  <span v-if="isLoading" class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#F34767] mr-3 inline-block"></span>
                  {{ isLoading ? texts.creatingAccount : texts.signUp }}
                </button>
              </div>

              <p class="text-xl text-[#1C1A1B] font-normal">
                <span>{{ texts.haveAccount }} </span>
                <router-link to="/login" class="text-[#F34767] font-medium underline underline-offset-[25%] hover:text-[#d93d5a] transition-colors">
                  {{ texts.signIn }}
                </router-link>
              </p>

              <div v-if="verificationStep === 'code'" class="mt-8 space-y-4">
                <p class="text-lg text-[#1C1A1B]">
                  {{ texts.verificationText }}
                </p>
                <div class="flex flex-col sm:flex-row flex-wrap gap-4 items-stretch sm:items-center">
                  <div class="flex-1 min-w-[220px]">
                    <div class="bg-white border-2 border-[#3F4369] rounded-[30px] h-20 flex items-center px-6">
                      <input
                        v-model="verification.code"
                        type="text"
                        inputmode="numeric"
                        maxlength="6"
                        class="w-full bg-transparent border-0 outline-none text-[#1C1A1B] text-xl font-bold placeholder:text-gray-400"
                        :placeholder="texts.verificationCodePlaceholder"
                        required
                      >
                    </div>
                  </div>
                  <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                    <button
                      type="button"
                      class="px-6 py-3 bg-[#F34767] text-white rounded-[20px] text-lg font-bold uppercase hover:bg-[#d93d5a] transition-all disabled:opacity-50"
                      :disabled="isVerifying"
                      @click="handleVerifyEmail"
                    >
                      {{ isVerifying ? texts.verifying : texts.verifyEmail }}
                    </button>
                    <button
                      type="button"
                      class="px-6 py-3 border-2 border-[#F34767] text-[#F34767] rounded-[20px] text-lg font-bold uppercase hover:bg-[#F34767] hover:text-white transition-all disabled:opacity-50"
                      :disabled="isVerifying"
                      @click="handleResendCode"
                    >
                      {{ texts.resendCode }}
                    </button>
                  </div>
                </div>
              </div>
            </form>

            <div v-if="error" class="mt-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800">
              <div class="flex items-center gap-2">
                <span class="text-xl">⚠️</span>
                <p>{{ error }}</p>
              </div>
            </div>
          </div>

          <div class="hidden lg:block relative">
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 rotate-[9.276deg]">
              <div class="bg-[#DCDCDC] opacity-[0.43] rounded-[100px] w-[627px] h-[707px]"></div>
            </div>
            <div class="relative z-10 flex items-center justify-center">
              <img :src="logoImage" alt="Peerie Logo" class="w-[732px] h-[182px] object-contain">
            </div>
          </div>
        </div>
      </div>
    </div>


  </div>
</template>

<script>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import logoImage from '@/assets/images/logos/logo.svg'
import { useLanguageStore } from '@/stores/language'

export default {
  name: 'Register',
  setup() {
    const router = useRouter()
    const languageStore = useLanguageStore()

    const texts = computed(() => {
      if (languageStore.language === 'de') {
        return {
          navLogin: 'Anmelden',
          navGetStarted: 'Los geht’s',
          navMenuLabel: 'Menü',
          title: 'Los geht’s',
          subtitle: 'Erstelle dein Konto',
          fullNamePlaceholder: 'Vollständiger Name',
          emailPlaceholder: 'E-Mail-Adresse',
          passwordPlaceholder: 'Passwort',
          confirmPasswordPlaceholder: 'Passwort bestätigen',
          creatingAccount: 'Konto wird erstellt...',
          signUp: 'Registrieren',
          haveAccount: 'Schon ein Konto?',
          signIn: 'Anmelden',
          verificationText: 'Wir haben dir einen Bestätigungscode per E-Mail gesendet. Gib ihn unten ein, um dein Konto zu aktivieren.',
          verificationCodePlaceholder: 'Bestätigungscode',
          verifying: 'Wird geprüft...',
          verifyEmail: 'Bestätigungscode senden',
          resendCode: 'Code erneut senden',
          errors: {
            passwordsDoNotMatch: 'Passwörter stimmen nicht überein',
            registration: 'Registrierungsfehler',
            verificationNotAvailable: 'Bestätigungscode ist nicht verfügbar',
            enterVerificationCode: 'Bitte gib den Bestätigungscode ein',
            verificationError: 'Bestätigungsfehler'
          }
        }
      }

      return {
        navLogin: 'Log in',
        navGetStarted: 'Get started',
        navMenuLabel: 'Menu',
        title: 'Get started',
        subtitle: 'Create your account',
        fullNamePlaceholder: 'Full name',
        emailPlaceholder: 'E-mail address',
        passwordPlaceholder: 'Password',
        confirmPasswordPlaceholder: 'Confirm password',
        creatingAccount: 'Creating account...',
        signUp: 'Sign up',
        haveAccount: 'Already have an account?',
        signIn: 'Sign in',
        verificationText: 'We have sent a confirmation code to your email. Enter it below to activate your account.',
        verificationCodePlaceholder: 'Confirmation code',
        verifying: 'Verifying...',
        verifyEmail: 'Send verification code',
        resendCode: 'Resend code',
        errors: {
          passwordsDoNotMatch: 'Passwords do not match',
          registration: 'Registration error',
          verificationNotAvailable: 'Confirmation code is not available',
          enterVerificationCode: 'Please enter the confirmation code',
          verificationError: 'Verification error'
        }
      }
    })
    const isLoading = ref(false)
    const isVerifying = ref(false)
    const error = ref('')

    const form = ref({
      name: '',
      email: '',
      password: '',
      password_confirmation: ''
    })

    const verificationStep = ref('form')
    const verification = ref({
      userId: null,
      code: ''
    })

    const handleRegister = async () => {
      isLoading.value = true
      error.value = ''

      if (form.value.password !== form.value.password_confirmation) {
        error.value = texts.value.errors.passwordsDoNotMatch
        isLoading.value = false
        return
      }

      try {
        const response = await axios.post('/api/register', form.value)

        if (response.data.success) {
          if (response.data.requires_verification) {
            verificationStep.value = 'code'
            verification.value.userId = response.data.user_id
            verification.value.code = ''
            error.value = ''
          } else if (response.data.user) {
          localStorage.setItem('user', JSON.stringify(response.data.user))
          router.push('/questionnaire')
          }
        } else {
          error.value = response.data.message || texts.value.errors.registration
        }
      } catch (err) {
        if (err.response?.status === 422) {
          const errors = Object.values(err.response.data.errors || {}).flat()
          error.value = errors.join(', ')
        } else {
          error.value = err.response?.data?.message || texts.value.errors.registration
        }
      } finally {
        isLoading.value = false
      }
    }

    const handleVerifyEmail = async () => {
      if (!verification.value.userId) {
        error.value = texts.value.errors.verificationNotAvailable
        return
      }

      if (!verification.value.code) {
        error.value = texts.value.errors.enterVerificationCode
        return
      }

      isVerifying.value = true
      error.value = ''

      try {
        const response = await axios.post('/api/email/verify-registration', {
          user_id: verification.value.userId,
          code: verification.value.code
        })

        if (response.data.success) {
          localStorage.setItem('user', JSON.stringify(response.data.user))
          router.push('/questionnaire')
        } else {
          error.value = response.data.message || texts.value.errors.verificationError
        }
      } catch (err) {
        error.value = err.response?.data?.message || texts.value.errors.verificationError
      } finally {
        isVerifying.value = false
      }
    }

    const handleResendCode = async () => {
      if (!verification.value.userId) {
        return
      }

      isVerifying.value = true

      try {
        await axios.post('/api/email/resend-registration-code', {
          user_id: verification.value.userId
        })
      } catch {
      } finally {
        isVerifying.value = false
      }
    }

    const mobileMenuOpen = ref(false)

    return {
      form,
      isLoading,
      error,
      handleRegister,
      verificationStep,
      verification,
      isVerifying,
      handleVerifyEmail,
      handleResendCode,
      logoImage,
      mobileMenuOpen,
      languageStore,
      texts
    }
  }
}
</script>
