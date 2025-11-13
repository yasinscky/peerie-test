<template>
  <AuthLayout
    headline="Shape a marketing engine that compounds"
    subheadline="Set up your Peerie workspace to plan smarter, execute faster and learn from every campaign, all in one command centre."
  >
    <div class="space-y-10">
      <header class="space-y-2 text-center">
        <p class="text-primary-600 font-medium tracking-wide uppercase text-xs">Create workspace</p>
        <h1 class="text-3xl font-semibold text-neutral-900">Get started with Peerie</h1>
        <p class="text-neutral-500 text-sm">
          We’ll tailor your first playbook based on your industry, team capacity and current stack.
        </p>
      </header>

      <form @submit.prevent="handleRegister" class="space-y-6">
        <div class="space-y-2">
          <label class="form-label text-sm font-medium text-neutral-700">Full name</label>
          <input
            v-model="form.name"
            type="text"
            class="form-input h-12"
            placeholder="Sofia Martinez"
            autocomplete="name"
            required
          >
        </div>

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

        <div class="grid sm:grid-cols-2 gap-4">
          <div class="space-y-2">
            <label class="form-label text-sm font-medium text-neutral-700">Password</label>
            <input
              v-model="form.password"
              type="password"
              class="form-input h-12"
              placeholder="Minimum 8 characters"
              autocomplete="new-password"
              minlength="8"
              required
            >
          </div>

          <div class="space-y-2">
            <label class="form-label text-sm font-medium text-neutral-700">Confirm password</label>
            <input
              v-model="form.password_confirmation"
              type="password"
              class="form-input h-12"
              placeholder="Re-enter password"
              autocomplete="new-password"
              minlength="8"
              required
            >
          </div>
        </div>

        <button
          type="submit"
          class="btn w-full h-12 text-base font-semibold"
          :disabled="isLoading"
        >
          <span v-if="isLoading" class="spinner mr-2"></span>
          {{ isLoading ? 'Creating workspace...' : 'Create account' }}
        </button>
      </form>

      <div class="text-center text-sm text-neutral-500">
        <span>Already have an account?</span>
        <router-link to="/login" class="text-primary-600 font-semibold ml-1 hover:text-primary-500">
          Sign in
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
  name: 'Register',
  components: {
    AuthLayout
  },
  setup() {
    const router = useRouter()
    const isLoading = ref(false)
    const error = ref('')

    const form = ref({
      name: '',
      email: '',
      password: '',
      password_confirmation: ''
    })

    const handleRegister = async () => {
      isLoading.value = true
      error.value = ''

      if (form.value.password !== form.value.password_confirmation) {
        error.value = 'Passwords do not match'
        isLoading.value = false
        return
      }

      try {
        const response = await axios.post('/api/register', form.value)

        if (response.data.success) {
          localStorage.setItem('user', JSON.stringify(response.data.user))
          router.push('/questionnaire')
        } else {
          error.value = response.data.message || 'Registration error'
        }
      } catch (err) {
        if (err.response?.status === 422) {
          const errors = Object.values(err.response.data.errors || {}).flat()
          error.value = errors.join(', ')
        } else {
          error.value = err.response?.data?.message || 'Registration error'
        }
      } finally {
        isLoading.value = false
      }
    }

    return {
      form,
      isLoading,
      error,
      handleRegister
    }
  }
}
</script>
