<template>
  <AuthLayout
    headline="Master your marketing operations"
    subheadline="Sign in to access your personalised marketing workspace, track progress and guide your team through the weekly playbooks."
  >
    <div class="space-y-10">
      <header class="space-y-2 text-center">
        <p class="text-primary-600 font-medium tracking-wide uppercase text-xs">Welcome back</p>
        <h1 class="text-3xl font-semibold text-neutral-900">Sign in to Peerie</h1>
        <p class="text-neutral-500 text-sm">
          Use your work email to manage campaigns, tasks and learnings in one place.
        </p>
      </header>

      <form @submit.prevent="handleLogin" class="space-y-6">
        <div class="space-y-2">
          <label class="form-label text-sm font-medium text-neutral-700">Work email</label>
          <input
            v-model="form.email"
            type="email"
            class="form-input h-12"
            placeholder="name@company.com"
            autocomplete="email"
            required
          >
        </div>

        <div class="space-y-2">
          <div class="flex items-center justify-between">
            <label class="form-label text-sm font-medium text-neutral-700">Password</label>
            <button type="button" class="text-xs font-medium text-primary-600 hover:text-primary-500">
              Forgot password?
            </button>
          </div>
          <input
            v-model="form.password"
            type="password"
            class="form-input h-12"
            placeholder="Enter your password"
            autocomplete="current-password"
            required
          >
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="inline-flex items-center gap-2 text-neutral-600">
            <input
              v-model="form.remember"
              type="checkbox"
              class="form-checkbox"
            >
            Remember me
          </label>
        </div>

        <button
          type="submit"
          class="btn w-full h-12 text-base font-semibold"
          :disabled="isLoading"
        >
          <span v-if="isLoading" class="spinner mr-2"></span>
          {{ isLoading ? 'Signing in...' : 'Sign in' }}
        </button>
      </form>

      <div class="text-center text-sm text-neutral-500">
        <span>New to Peerie?</span>
        <router-link to="/register" class="text-primary-600 font-semibold ml-1 hover:text-primary-500">
          Create an account
        </router-link>
      </div>

      <div v-if="error" class="alert alert-error">
        <div class="flex items-center gap-2">
          <span class="text-xl">⚠️</span>
          <p>{{ error }}</p>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AuthLayout from '@/components/AuthLayout.vue'

export default {
  name: 'Login',
  components: {
    AuthLayout
  },
  setup() {
    const router = useRouter()
    const isLoading = ref(false)
    const error = ref('')

    const form = ref({
      email: '',
      password: '',
      remember: false
    })

    const handleLogin = async () => {
      isLoading.value = true
      error.value = ''

      try {
        const response = await axios.post('/api/login', form.value)

        if (response.data.success) {
          localStorage.setItem('user', JSON.stringify(response.data.user))
          router.push('/dashboard')
        } else {
          error.value = response.data.message || 'Login error'
        }
      } catch (err) {
        if (err.response?.status === 422) {
          const errors = Object.values(err.response.data.errors || {}).flat()
          error.value = errors.join(', ')
        } else {
          error.value = err.response?.data?.message || 'Login error'
        }
      } finally {
        isLoading.value = false
      }
    }

    return {
      form,
      isLoading,
      error,
      handleLogin
    }
  }
}
</script>
