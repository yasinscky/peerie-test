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
            <h1 class="text-white text-4xl lg:text-6xl font-bold">Profile</h1>
          </div>
          <div class="flex items-center space-x-2 text-white text-base lg:text-xl font-medium">
            <span>Account</span>
            <span class="opacity-40">|</span>
            <span class="opacity-40">Profile</span>
          </div>
      </div>

        <div class="p-4 lg:p-8 space-y-8">
          <div class="bg-white border-2 border-[#3f4369] rounded-[36px] p-6 lg:p-8">
            <h2 class="text-[#1c1a1b] text-3xl lg:text-4xl font-extrabold mb-2">Personal Information</h2>
            <p class="text-[#1c1a1b] text-base lg:text-xl mb-6">Please fill in all required fields marked with <span class="text-red-500">*</span></p>
            
            <form @submit.prevent="updateProfile" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border-2 border-[#3f4369] rounded-[30px] p-6">
                  <label class="block text-[#1c1a1b] text-lg font-bold mb-2">First name</label>
                  <input 
                    v-model="profileForm.firstName"
                    type="text" 
                    class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-base"
                    placeholder="Enter first name"
                    required
                  >
                </div>
                <div class="bg-white border-2 border-[#3f4369] rounded-[30px] p-6">
                  <label class="block text-[#1c1a1b] text-lg font-bold mb-2">Last name</label>
                <input 
                    v-model="profileForm.lastName"
                  type="text" 
                    class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-base"
                    placeholder="Enter last name"
                  required
                >
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border-2 border-[#3f4369] rounded-[30px] p-6">
                  <label class="block text-[#1c1a1b] text-lg font-bold mb-2">E-mail</label>
                <input 
                  v-model="profileForm.email"
                  type="email" 
                    class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-base"
                    placeholder="Enter email"
                  required
                >
                </div>
                <div class="bg-white border-2 border-[#3f4369] rounded-[30px] p-6 relative">
                  <label class="block text-[#1c1a1b] text-lg font-bold mb-2">Language</label>
                  <select 
                    v-model="profileForm.language"
                    class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-base appearance-none pr-10"
                  >
                    <option value="en">English</option>
                    <option value="ru">Russian</option>
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
                  Reset
                </button>
                <button 
                  type="submit" 
                  :disabled="isUpdating"
                  class="px-8 py-3 bg-white border-2 border-[#f34767] text-[#f34767] rounded-[30px] text-xl font-bold uppercase hover:bg-[#f34767] hover:text-white transition-colors disabled:opacity-50"
                >
                  {{ isUpdating ? 'Saving...' : 'Save changes' }}
                </button>
              </div>
            </form>
        </div>

          <div class="bg-white border-2 border-[#3f4369] rounded-[36px] p-6 lg:p-8">
            <h2 class="text-[#1c1a1b] text-3xl lg:text-4xl font-extrabold mb-2">Security Settings</h2>
            <p class="text-[#1c1a1b] text-base lg:text-xl mb-6">Use a strong password with at least 8 characters, including a number and a special symbol</p>
            
            <form @submit.prevent="updatePassword" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border-2 border-[#3f4369] rounded-[30px] p-6">
                  <label class="block text-[#1c1a1b] text-lg font-bold mb-2">Password</label>
                <input 
                    v-model="passwordForm.password"
                  type="password" 
                    class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-base"
                    placeholder="Enter new password"
                  required
                  minlength="8"
                >
              </div>
                <div class="bg-white border-2 border-[#3f4369] rounded-[30px] p-6">
                  <label class="block text-[#1c1a1b] text-lg font-bold mb-2">Confirm password</label>
                <input 
                  v-model="passwordForm.confirmPassword"
                  type="password" 
                    class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-base"
                    placeholder="Confirm new password"
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
                  Delete my account
                </button>
                <div class="flex gap-4">
                  <button 
                    type="button"
                    @click="resetPasswordForm"
                    class="px-6 py-3 text-[#f34767] text-xl font-bold uppercase hover:opacity-80 transition-opacity"
                  >
                    Reset
                  </button>
                  <button 
                    type="submit" 
                    :disabled="isUpdating"
                    class="px-8 py-3 bg-white border-2 border-[#f34767] text-[#f34767] rounded-[30px] text-xl font-bold uppercase hover:bg-[#f34767] hover:text-white transition-colors disabled:opacity-50"
                  >
                    {{ isUpdating ? 'Updating...' : 'Save changes' }}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
    </main>

    <footer class="bg-white border-t-2 border-[#DCDCDC] mt-16">
      <div class="max-w-7xl mx-auto px-4 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 mb-8">
          <div class="lg:col-span-1">
            <div class="h-12 w-48 mb-4">
              <img src="/assets/images/logos/logo.png" alt="Peerie Logo" class="h-full w-auto">
          </div>
        </div>

          <div>
            <h3 class="text-[#1c1a1b] text-2xl lg:text-4xl font-bold mb-4">Resources</h3>
            <ul class="space-y-2">
              <li><a href="#" class="text-[#1c1a1b] text-base lg:text-xl font-medium hover:text-[#f34767] transition-colors">Blog</a></li>
              <li><a href="#" class="text-[#1c1a1b] text-base lg:text-xl font-medium hover:text-[#f34767] transition-colors">Resource library</a></li>
            </ul>
          </div>

          <div>
            <h3 class="text-[#1c1a1b] text-2xl lg:text-4xl font-bold mb-4">Services</h3>
            <ul class="space-y-2">
              <li><a href="#" class="text-[#1c1a1b] text-base lg:text-xl font-medium hover:text-[#f34767] transition-colors">Web development</a></li>
              <li><a href="#" class="text-[#1c1a1b] text-base lg:text-xl font-medium hover:text-[#f34767] transition-colors">Consulting</a></li>
              <li><a href="#" class="text-[#1c1a1b] text-base lg:text-xl font-medium hover:text-[#f34767] transition-colors">Request an industry</a></li>
            </ul>
        </div>

          <div>
            <h3 class="text-[#1c1a1b] text-2xl lg:text-4xl font-bold mb-4">Support</h3>
            <ul class="space-y-2">
              <li><a href="#" class="text-[#1c1a1b] text-base lg:text-xl font-medium hover:text-[#f34767] transition-colors">Help centre</a></li>
              <li><a href="#" class="text-[#1c1a1b] text-base lg:text-xl font-medium hover:text-[#f34767] transition-colors">FAQ</a></li>
              <li><a href="#" class="text-[#1c1a1b] text-base lg:text-xl font-medium hover:text-[#f34767] transition-colors">Contact</a></li>
            </ul>
          </div>

          <div>
            <h3 class="text-[#1c1a1b] text-2xl lg:text-4xl font-bold mb-4">Company</h3>
            <ul class="space-y-2">
              <li><a href="#" class="text-[#1c1a1b] text-base lg:text-xl font-medium hover:text-[#f34767] transition-colors">About us</a></li>
              <li><a href="#" class="text-[#1c1a1b] text-base lg:text-xl font-medium hover:text-[#f34767] transition-colors">Legal</a></li>
              <li><a href="#" class="text-[#1c1a1b] text-base lg:text-xl font-medium hover:text-[#f34767] transition-colors">Privacy policy</a></li>
              <li><a href="#" class="text-[#1c1a1b] text-base lg:text-xl font-medium hover:text-[#f34767] transition-colors">Terms of use</a></li>
            </ul>
          </div>
        </div>

        <div class="border-t-2 border-[#DCDCDC] pt-8">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
              <h3 class="text-[#1c1a1b] text-2xl lg:text-4xl font-bold mb-4">Subscribe to our news and special offers!</h3>
              <form @submit.prevent="subscribeNewsletter" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 bg-white border-2 border-[#DCDCDC] rounded-[30px] p-4">
                  <input 
                    v-model="newsletterEmail"
                    type="email" 
                    placeholder="Your e-mail"
                    class="w-full bg-transparent border-none outline-none text-[#1c1a1b] text-base lg:text-xl"
                    required
                  >
                </div>
                <button 
                  type="submit"
                  class="px-6 py-4 bg-[#f34767] border-2 border-[#f34767] text-white rounded-[15px] text-base lg:text-xl font-bold uppercase hover:bg-opacity-90 transition-opacity whitespace-nowrap"
                >
                  Join newsletter
                </button>
              </form>
              <div class="mt-4 flex items-start space-x-2">
                <input 
                  v-model="newsletterConsent"
                  type="checkbox" 
                  id="newsletter-consent"
                  class="mt-1 w-5 h-5 text-[#f34767] border-[#DCDCDC] rounded focus:ring-[#f34767]"
                >
                <label for="newsletter-consent" class="text-[#1c1a1b] text-sm lg:text-base">
                  Yes, I would like to receive email marketing communications from Peerie. I understand that I can unsubscribe at any time.
                </label>
              </div>
            </div>
          </div>
          <p class="text-[#DCDCDC] text-base lg:text-2xl font-medium mt-8">Copyright 2025 Peerie</p>
        </div>
      </div>
    </footer>

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

const router = useRouter()

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
      first_name: profileForm.value.firstName,
      last_name: profileForm.value.lastName,
      email: profileForm.value.email,
      language: profileForm.value.language
    })
    showMessage('Profile updated successfully!')
  } catch (error) {
    showMessage(error.response?.data?.message || 'Profile update error', 'error')
  } finally {
    isUpdating.value = false
  }
}

const updatePassword = async () => {
  if (passwordForm.value.password !== passwordForm.value.confirmPassword) {
    showMessage('Passwords do not match', 'error')
    return
  }

  isUpdating.value = true
  try {
    await axios.put('/api/profile/password', {
      password: passwordForm.value.password,
      password_confirmation: passwordForm.value.confirmPassword
    })
    showMessage('Password updated successfully!')
    resetPasswordForm()
  } catch (error) {
    showMessage(error.response?.data?.message || 'Password update error', 'error')
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
  if (!confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
    return
  }
  
  try {
    await axios.delete('/api/profile')
    localStorage.removeItem('user')
    router.push('/login')
  } catch (error) {
    showMessage(error.response?.data?.message || 'Account deletion error', 'error')
  }
}

const subscribeNewsletter = async () => {
  if (!newsletterConsent.value) {
    showMessage('Please consent to receive marketing communications', 'error')
    return
  }
  
  try {
    await axios.post('/api/newsletter/subscribe', {
      email: newsletterEmail.value
    })
    showMessage('Successfully subscribed to newsletter!')
    newsletterEmail.value = ''
    newsletterConsent.value = false
  } catch (error) {
    showMessage(error.response?.data?.message || 'Subscription error', 'error')
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