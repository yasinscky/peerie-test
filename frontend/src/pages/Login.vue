<template>
  <div class="min-h-screen bg-white">
    <!-- Navigation -->
    <nav class="relative px-6 py-4 bg-white">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center">
          <img :src="logoImage" alt="Peerie" class="h-12 w-auto">
        </div>

        <!-- Navigation Links -->
        <div class="hidden md:flex items-center space-x-8">
          <router-link to="/" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors">Home</router-link>
          <div class="relative group">
            <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors flex items-center">
              Industries
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </a>
          </div>
          <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors">Services</a>
          <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors">Blog</a>
          <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors">Pricing</a>
        </div>

        <!-- Action Buttons -->
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
      </div>
    </nav>

    <!-- Main Content -->
    <div class="relative px-6 py-12 md:py-20 bg-white overflow-hidden min-h-[calc(100vh-200px)]">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <!-- Left Side - Login Form -->
          <div class="relative z-10">
            <h1 class="text-6xl md:text-7xl lg:text-[96px] font-bold text-[#1C1A1B] mb-6 leading-[0.95]">
              Welcome back
            </h1>
            <p class="text-3xl md:text-4xl lg:text-[48px] font-bold text-[#1C1A1B] mb-12 tracking-[-1.92px]">
              Please enter your details
            </p>

            <form @submit.prevent="handleLogin" class="space-y-6">
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

              <div class="relative">
                <div class="bg-white border-2 border-[#3F4369] rounded-[30px] h-24 flex items-center px-6">
                  <input
                    v-model="form.password"
                    type="password"
                    class="w-full bg-transparent border-0 outline-none text-[#1C1A1B] text-xl font-bold placeholder-[#1C1A1B]"
                    placeholder="Password"
                    autocomplete="current-password"
                    required
                  >
                </div>
              </div>

              <div class="text-right">
                <button 
                  type="button" 
                  class="text-[#F34767] text-xl font-medium underline underline-offset-[25%] hover:text-[#d93d5a] transition-colors"
                >
                  Forgot password?
                </button>
              </div>

              <div class="relative">
                <button
                  type="submit"
                  class="w-full bg-white border-2 border-[#F34767] rounded-[30px] h-24 flex items-center justify-center text-[#F34767] text-2xl font-bold uppercase transition-all duration-200 hover:bg-[#F34767] hover:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="isLoading"
                >
                  <span v-if="isLoading" class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#F34767] mr-3 inline-block"></span>
                  {{ isLoading ? 'Signing in...' : 'Sign in' }}
                </button>
              </div>

              <!-- Sign Up Link -->
              <p class="text-xl text-[#1C1A1B] font-normal">
                <span>Don't have an account? </span>
                <router-link to="/register" class="text-[#F34767] font-medium underline underline-offset-[25%] hover:text-[#d93d5a] transition-colors">
                  Sign up
                </router-link>
              </p>

              <div v-if="verification.show" class="mt-8 space-y-4">
                <p class="text-lg text-[#1C1A1B]">
                  Your email is not verified. Enter the verification code we sent to your email.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center">
                  <div class="flex-1">
                    <div class="bg-white border-2 border-[#3F4369] rounded-[30px] h-20 flex items-center px-6">
                      <input
                        v-model="verification.code"
                        type="text"
                        inputmode="numeric"
                        maxlength="6"
                        class="w-full bg-transparent border-0 outline-none text-[#1C1A1B] text-xl font-bold placeholder-[#1C1A1B]"
                        placeholder="Verification code"
                        required
                      >
                    </div>
                  </div>
                  <div class="flex flex-col sm:flex-row gap-3">
                    <button
                      type="button"
                      class="px-6 py-3 bg-[#F34767] text-white rounded-[20px] text-lg font-bold uppercase hover:bg-[#d93d5a] transition-all disabled:opacity-50"
                      :disabled="isVerifying"
                      @click="verifyEmail"
                    >
                      {{ isVerifying ? 'Verifying...' : 'Verify email' }}
                    </button>
                    <button
                      type="button"
                      class="px-6 py-3 border-2 border-[#F34767] text-[#F34767] rounded-[20px] text-lg font-bold uppercase hover:bg-[#F34767] hover:text-white transition-all disabled:opacity-50"
                      :disabled="isVerifying"
                      @click="resendCode"
                    >
                      Resend code
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

          <!-- Right Side - Decorative Element -->
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

    <!-- Footer -->
    <footer class="px-6 py-12 bg-white border-t border-[#DCDCDC]">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 mb-12">
          <!-- Logo and Copyright -->
          <div class="lg:col-span-1">
            <img :src="logoImage" alt="Peerie" class="h-12 w-auto mb-4">
            <p class="text-[#DCDCDC] text-2xl font-medium">Copyright 2025 Peerie</p>
          </div>

          <!-- Services -->
          <div>
            <h3 class="text-4xl font-bold text-[#1C1A1B] mb-4 tracking-[-1.6px]">Services</h3>
            <ul class="space-y-2">
              <li><a href="#" class="text-xl text-[#1C1A1B] font-medium hover:text-[#F34767] transition-colors">Web development</a></li>
              <li><a href="#" class="text-xl text-[#1C1A1B] font-medium hover:text-[#F34767] transition-colors">Consulting</a></li>
              <li><a href="#" class="text-xl text-[#1C1A1B] font-medium hover:text-[#F34767] transition-colors">Request an industry</a></li>
            </ul>
          </div>

          <!-- Resources -->
          <div>
            <h3 class="text-4xl font-bold text-[#1C1A1B] mb-4 tracking-[-1.6px]">Resources</h3>
            <ul class="space-y-2">
              <li><a href="#" class="text-xl text-[#1C1A1B] font-medium hover:text-[#F34767] transition-colors">Blog</a></li>
              <li><a href="#" class="text-xl text-[#1C1A1B] font-medium hover:text-[#F34767] transition-colors">Resource library</a></li>
            </ul>
          </div>

          <!-- Support -->
          <div>
            <h3 class="text-4xl font-bold text-[#1C1A1B] mb-4 tracking-[-1.6px]">Support</h3>
            <ul class="space-y-2">
              <li><a href="#" class="text-xl text-[#1C1A1B] font-medium hover:text-[#F34767] transition-colors">Help centre</a></li>
              <li><a href="#" class="text-xl text-[#1C1A1B] font-medium hover:text-[#F34767] transition-colors">FAQ</a></li>
              <li><a href="#" class="text-xl text-[#1C1A1B] font-medium hover:text-[#F34767] transition-colors">Contact</a></li>
            </ul>
          </div>

          <!-- Company -->
          <div>
            <h3 class="text-4xl font-bold text-[#1C1A1B] mb-4 tracking-[-1.6px]">Company</h3>
            <ul class="space-y-2">
              <li><a href="#" class="text-xl text-[#1C1A1B] font-medium hover:text-[#F34767] transition-colors">About us</a></li>
              <li><a href="#" class="text-xl text-[#1C1A1B] font-medium hover:text-[#F34767] transition-colors">Legal</a></li>
              <li><a href="#" class="text-xl text-[#1C1A1B] font-medium hover:text-[#F34767] transition-colors">Privacy policy</a></li>
              <li><a href="#" class="text-xl text-[#1C1A1B] font-medium hover:text-[#F34767] transition-colors">Terms of use</a></li>
            </ul>
          </div>
        </div>

        <!-- Newsletter Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start mt-8">
          <div class="lg:col-span-1">
            <h3 class="text-4xl font-bold text-[#1C1A1B] mb-4 tracking-[-1.6px]">Subscribe to our news and special offers!</h3>
            <div class="bg-white border-2 border-[#DCDCDC] rounded-[30px] h-24 flex items-center px-6 mb-4">
              <input
                type="email"
                class="w-full bg-transparent border-0 outline-none text-[#1C1A1B] text-xl font-medium placeholder-[#1C1A1B]"
                placeholder="Your e-mail"
              >
            </div>
            <div class="flex items-start gap-2 mb-4">
              <input type="checkbox" id="newsletter" class="mt-1 w-5 h-5 text-[#F34767] border-[#DCDCDC] rounded focus:ring-[#F34767]">
              <label for="newsletter" class="text-sm text-[#1C1A1B] leading-[1.2]">
                Yes, I would like to receive email marketing communications from Peerie. I understand that I can unsubscribe at any time.
              </label>
            </div>
            <button class="bg-[#F34767] border-2 border-[#F34767] rounded-[15px] h-[60px] px-8 text-white text-xl font-bold uppercase hover:bg-[#d93d5a] transition-all duration-200">
              Join newsletter
            </button>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import logoImage from '@/assets/images/logos/logo.svg'

export default {
  name: 'Login',
  setup() {
    const router = useRouter()
    const isLoading = ref(false)
    const isVerifying = ref(false)
    const error = ref('')

    const form = ref({
      email: '',
      password: '',
      remember: false
    })

    const verification = ref({
      show: false,
      userId: null,
      code: ''
    })

    const handleLogin = async () => {
      isLoading.value = true
      error.value = ''

      try {
        const response = await axios.post('/api/login', form.value)

        if (response.data.success) {
          verification.value.show = false
          localStorage.setItem('user', JSON.stringify(response.data.user))
          router.push('/dashboard')
        } else {
          error.value = response.data.message || 'Login error'
        }
      } catch (err) {
        if (err.response?.status === 403 && err.response.data?.requires_verification) {
          verification.value.show = true
          verification.value.userId = err.response.data.user_id
          verification.value.code = ''
          error.value = err.response.data.message || 'Email not verified. Enter the code we sent to your email.'
        } else if (err.response?.status === 422) {
          const errors = Object.values(err.response.data.errors || {}).flat()
          error.value = errors.join(', ')
        } else {
          error.value = err.response?.data?.message || 'Login error'
        }
      } finally {
        isLoading.value = false
      }
    }

    const verifyEmail = async () => {
      if (!verification.value.userId) {
        error.value = 'Verification is not available'
        return
      }

      if (!verification.value.code) {
        error.value = 'Please enter the verification code'
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
          verification.value.show = false
          localStorage.setItem('user', JSON.stringify(response.data.user))
          router.push('/dashboard')
        } else {
          error.value = response.data.message || 'Verification error'
        }
      } catch (err) {
        error.value = err.response?.data?.message || 'Verification error'
      } finally {
        isVerifying.value = false
      }
    }

    const resendCode = async () => {
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

    return {
      form,
      isLoading,
      error,
      handleLogin,
      verification,
      isVerifying,
      verifyEmail,
      resendCode,
      logoImage
    }
  }
}
</script>
