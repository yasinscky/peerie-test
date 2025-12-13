<template>
  <div class="min-h-screen bg-white">
    <nav class="relative px-6 py-4 bg-white">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <router-link to="/" class="flex items-center cursor-pointer">
          <img :src="logoImage" alt="Peerie" class="h-12 w-auto">
        </router-link>

        <div class="hidden md:flex items-center space-x-4">
          <router-link 
            to="/login"
            class="px-6 py-3 text-[#3F4369] border-2 border-[#3F4369] rounded-[15px] hover:bg-[#3F4369] hover:text-white transition-all duration-200 font-bold uppercase text-sm"
          >
            Log in
          </router-link>
          <router-link 
            to="/register"
            class="px-6 py-3 bg-[#F34767] text-white rounded-[15px] hover:bg-[#d93d5a] transition-all duration-200 font-bold uppercase text-sm shadow-lg"
          >
            Get started
          </router-link>
        </div>

        <button 
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="md:hidden p-2 text-[#1c1a1b]"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
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
            Log in
          </router-link>
          <router-link 
            to="/register"
            @click="mobileMenuOpen = false"
            class="block w-full px-4 py-3 text-center bg-[#F34767] text-white rounded-[15px] hover:bg-[#d93d5a] transition-all duration-200 font-bold uppercase text-sm shadow-lg"
          >
            Get started
          </router-link>
        </div>
      </div>
    </nav>

    <div class="relative px-6 py-12 md:py-20 bg-white overflow-hidden min-h-[calc(100vh-200px)]">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div class="relative z-10">
            <h1 class="text-6xl md:text-7xl lg:text-[96px] font-bold text-[#1C1A1B] mb-6 leading-[0.95]">
              Reset password
            </h1>
            <p class="text-3xl md:text-4xl lg:text-[48px] font-bold text-[#1C1A1B] mb-12 tracking-[-1.92px]">
              {{ stepText }}
            </p>

            <div v-if="step === 1" class="space-y-6">
              <form @submit.prevent="requestReset" class="space-y-6">
                <div class="relative">
                  <div class="bg-white border-2 border-[#3F4369] rounded-[30px] h-24 flex items-center px-6">
                    <input
                      v-model="form.email"
                      type="email"
                      class="w-full bg-transparent border-0 outline-none text-[#1C1A1B] text-xl font-bold placeholder-[#1C1A1B]"
                      placeholder="E-mail address"
                      autocomplete="email"
                      required
                    >
                  </div>
                </div>

                <div v-if="error" class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800">
                  <p>{{ error }}</p>
                </div>

                <div class="relative">
                  <button
                    type="submit"
                    class="w-full bg-white border-2 border-[#F34767] rounded-[30px] h-24 flex items-center justify-center text-[#F34767] text-2xl font-bold uppercase transition-all duration-200 hover:bg-[#F34767] hover:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="isLoading"
                  >
                    <span v-if="isLoading" class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#F34767] mr-3 inline-block"></span>
                    {{ isLoading ? 'Sending...' : 'Send reset code' }}
                  </button>
                </div>
              </form>
            </div>

            <div v-if="step === 2" class="space-y-6">
              <form @submit.prevent="goToPasswordStep" class="space-y-6">
                <div class="relative">
                  <div class="bg-white border-2 border-[#3F4369] rounded-[30px] h-24 flex items-center px-6">
                    <input
                      v-model="form.code"
                      type="text"
                      class="w-full bg-transparent border-0 outline-none text-[#1C1A1B] text-xl font-bold placeholder-[#1C1A1B]"
                      placeholder="Verification code"
                      required
                      maxlength="6"
                    >
                  </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center">
                  <div class="flex-1">
                    <button
                      type="submit"
                      class="w-full bg-white border-2 border-[#F34767] rounded-[30px] h-20 flex items-center justify-center text-[#F34767] text-xl font-bold uppercase transition-all duration-200 hover:bg-[#F34767] hover:text-white"
                    >
                      Continue
                    </button>
                  </div>
                  <button
                    type="button"
                    @click="resendCode"
                    class="px-6 py-3 text-[#F34767] text-lg font-medium underline underline-offset-[25%] hover:text-[#d93d5a] transition-colors disabled:opacity-50"
                    :disabled="isResending"
                  >
                    {{ isResending ? 'Resending...' : 'Resend code' }}
                  </button>
                </div>
              </form>
            </div>

            <div v-if="step === 3" class="space-y-6">
              <form @submit.prevent="resetPassword" class="space-y-6">
                <div class="relative">
                  <div class="bg-white border-2 border-[#3F4369] rounded-[30px] h-24 flex items-center px-6">
                    <input
                      v-model="form.password"
                      type="password"
                      class="w-full bg-transparent border-0 outline-none text-[#1C1A1B] text-xl font-bold placeholder-[#1C1A1B]"
                      placeholder="New password"
                      autocomplete="new-password"
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
                      placeholder="Confirm new password"
                      autocomplete="new-password"
                      required
                    >
                  </div>
                </div>

                <div v-if="error" class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800">
                  <p>{{ error }}</p>
                </div>

                <div class="relative">
                  <button
                    type="submit"
                    class="w-full bg-white border-2 border-[#F34767] rounded-[30px] h-24 flex items-center justify-center text-[#F34767] text-2xl font-bold uppercase transition-all duration-200 hover:bg-[#F34767] hover:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="isLoading"
                  >
                    <span v-if="isLoading" class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#F34767] mr-3 inline-block"></span>
                    {{ isLoading ? 'Resetting...' : 'Reset password' }}
                  </button>
                </div>
              </form>
            </div>

            <div v-if="step === 4" class="space-y-6">
              <div class="px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800">
                <p class="text-xl font-medium">Password reset successfully!</p>
              </div>
              <router-link 
                to="/login"
                class="block w-full bg-white border-2 border-[#F34767] rounded-[30px] h-24 flex items-center justify-center text-[#F34767] text-2xl font-bold uppercase transition-all duration-200 hover:bg-[#F34767] hover:text-white"
              >
                Go to login
              </router-link>
            </div>

            <div class="mt-6">
              <router-link to="/login" class="text-[#F34767] text-xl font-medium underline underline-offset-[25%] hover:text-[#d93d5a] transition-colors">
                Back to login
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import logoImage from '@/assets/images/logos/logo.svg'

const router = useRouter()

const mobileMenuOpen = ref(false)
const step = ref(1)
const isLoading = ref(false)
const isResending = ref(false)
const error = ref('')
const userId = ref(null)

const form = ref({
  email: '',
  code: '',
  password: '',
  password_confirmation: ''
})

const stepText = computed(() => {
  switch (step.value) {
    case 1:
      return 'Enter your email address'
    case 2:
      return 'Enter verification code'
    case 3:
      return 'Enter new password'
    case 4:
      return 'Password reset successful'
    default:
      return ''
  }
})

const requestReset = async () => {
  isLoading.value = true
  error.value = ''

  try {
    const response = await axios.post('/api/password/forgot', {
      email: form.value.email
    })

    if (response.data.success) {
      userId.value = response.data.user_id
      step.value = 2
      if (response.data.debug_verification_code) {
        console.log('Debug verification code:', response.data.debug_verification_code)
      }
    } else {
      error.value = response.data.message || 'Failed to send reset code'
    }
  } catch (err) {
    if (err.response?.status === 404) {
      error.value = 'User with this email not found'
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Failed to send reset code. Please try again.'
    }
  } finally {
    isLoading.value = false
  }
}

const goToPasswordStep = () => {
  if (!form.value.code || form.value.code.length < 6) {
    error.value = 'Please enter a valid verification code'
    return
  }
  error.value = ''
  step.value = 3
}

const resetPassword = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Passwords do not match'
    return
  }

  if (form.value.password.length < 8) {
    error.value = 'Password must be at least 8 characters'
    return
  }

  isLoading.value = true
  error.value = ''

  try {
    const response = await axios.post('/api/password/reset', {
      user_id: userId.value,
      code: form.value.code,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation
    })

    if (response.data.success) {
      step.value = 4
    } else {
      error.value = response.data.message || 'Failed to reset password'
    }
  } catch (err) {
    if (err.response?.status === 422) {
      const errors = Object.values(err.response.data.errors || {}).flat()
      error.value = errors.join(', ')
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Failed to reset password. Please try again.'
    }
  } finally {
    isLoading.value = false
  }
}

const resendCode = async () => {
  isResending.value = true
  error.value = ''

  try {
    const response = await axios.post('/api/password/forgot', {
      email: form.value.email
    })

    if (response.data.success) {
      if (response.data.debug_verification_code) {
        console.log('Debug verification code:', response.data.debug_verification_code)
      }
    } else {
      error.value = response.data.message || 'Failed to resend code'
    }
  } catch (err) {
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Failed to resend code. Please try again.'
    }
  } finally {
    isResending.value = false
  }
}
</script>
