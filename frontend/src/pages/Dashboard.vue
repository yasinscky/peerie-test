<template>
  <div class="min-h-screen bg-white">
    <!-- Left Sidebar -->
    <div class="fixed top-0 left-0 bottom-0 z-40 w-80 xl:w-96 bg-white border-r-2 border-[#3f4369] overflow-y-auto transition-transform duration-300 md:translate-x-0" 
         :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
         style="border-right: 2px solid #3f4369;">
      <div class="flex flex-col h-full p-4 lg:p-6">
        <!-- Logo -->
        <div class="mb-6">
          <img :src="logoImage" alt="Peerie Logo" class="w-full max-w-[150px]">
        </div>

        <div class="mb-6 rounded-[36px] border-2 border-[#3f4369] bg-white shadow-[0_4px_4px_0_rgba(0,0,0,0.25)] p-4">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-14 h-14 rounded-full bg-[#DCDCDC] flex items-center justify-center overflow-hidden">
              <img v-if="user?.avatar" :src="user.avatar" :alt="user?.name" class="w-full h-full object-cover">
              <span v-else class="text-[#3f4369] text-lg font-bold">{{ userInitials }}</span>
            </div>
            <div class="flex-1">
              <p class="text-[#1c1a1b] text-sm font-medium">My account</p>
              <p class="text-[#3f4369] text-xl font-bold">{{ user?.name || 'User' }}</p>
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <button @click="navigateToSettings" class="flex items-center space-x-1 text-[#1c1a1b] text-xs font-bold uppercase px-2 py-1 hover:bg-[#FFEB88] hover:bg-opacity-20 rounded">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              <span>Edit</span>
            </button>
            <button @click="handleLogout" class="flex items-center space-x-1 text-[#1c1a1b] text-xs font-bold uppercase px-2 py-1 hover:bg-[#FFEB88] hover:bg-opacity-20 rounded">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
              </svg>
              <span>Exit</span>
            </button>
          </div>
        </div>

        <nav class="space-y-6 flex-1">
          <div class="rounded-[36px] border-2 border-[#3f4369] bg-white shadow-[0_4px_4px_0_rgba(0,0,0,0.25)] p-4">
            <div class="flex items-center space-x-2 mb-3">
              <div class="w-8 h-8 rounded-lg border-2 border-[#3f4369] flex items-center justify-center">
                <svg class="w-5 h-5 text-[#3f4369]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </div>
              <h3 class="text-[#3f4369] text-2xl font-bold">Create</h3>
            </div>
            <div class="ml-10 space-y-2 flex flex-col">
              <router-link 
                to="/dashboard/marketing-plans"
                class="block w-fit rounded-[17px] border px-3 py-2 text-lg font-medium transition-colors cursor-pointer"
                :class="currentRoute === '/dashboard/marketing-plans' 
                  ? 'bg-[#f34767] text-white border-[#f34767]' 
                  : 'border-[#DCDCDC] bg-white text-[#1c1a1b] hover:bg-[#f34767] hover:text-white'"
              >
                Your Marketing Plan
              </router-link>
              <router-link 
                to="/dashboard/image-generator"
                class="block w-fit rounded-[17px] border px-3 py-2 text-lg font-medium transition-colors cursor-pointer"
                :class="currentRoute === '/dashboard/image-generator' 
                  ? 'bg-[#f34767] text-white border-[#f34767]' 
                  : 'border-[#DCDCDC] bg-white text-[#1c1a1b] hover:bg-[#f34767] hover:text-white'"
              >
                Image Library
              </router-link>
              <router-link 
                to="/dashboard/hashtags"
                class="block w-fit rounded-[17px] border px-3 py-2 text-lg font-medium transition-colors cursor-pointer"
                :class="currentRoute === '/dashboard/hashtags' 
                  ? 'bg-[#f34767] text-white border-[#f34767]' 
                  : 'border-[#DCDCDC] bg-white text-[#1c1a1b] hover:bg-[#f34767] hover:text-white'"
              >
                Hashtags
              </router-link>
            </div>
          </div>

          <div class="rounded-[36px] border-2 border-[#3f4369] bg-white shadow-[0_4px_4px_0_rgba(0,0,0,0.25)] p-4">
            <div class="flex items-center space-x-2 mb-3">
              <div class="w-8 h-8 rounded-lg border-2 border-[#3f4369] flex items-center justify-center">
                <svg class="w-5 h-5 text-[#3f4369]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
              </div>
              <h3 class="text-[#3f4369] text-2xl font-bold">Engage</h3>
            </div>
            <div class="ml-10 space-y-2 flex flex-col">
              <p class="block w-fit rounded-[17px] border border-[#DCDCDC] bg-white px-3 py-2 text-[#1c1a1b] text-lg font-medium hover:bg-[#f34767] hover:text-white transition-colors cursor-pointer">Community</p>
            </div>
          </div>

          <div class="rounded-[36px] border-2 border-[#3f4369] bg-white shadow-[0_4px_4px_0_rgba(0,0,0,0.25)] p-4">
            <div class="flex items-center space-x-2 mb-3">
              <div class="w-8 h-8 rounded-lg border-2 border-[#3f4369] flex items-center justify-center">
                <svg class="w-5 h-5 text-[#3f4369]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <h3 class="text-[#3f4369] text-2xl font-bold">Account</h3>
            </div>
            <div class="ml-10 space-y-2 flex flex-col">
              <router-link 
                to="/dashboard/settings" 
                class="block w-fit text-left px-3 py-2 rounded-[17px] border text-lg font-medium transition-colors"
                :class="currentRoute === '/dashboard/settings' 
                  ? 'bg-[#f34767] text-white border-[#f34767]' 
                  : 'border-[#DCDCDC] bg-white text-[#1c1a1b] hover:bg-[#f34767] hover:text-white'"
              >
                Profile
              </router-link>
              <button class="block w-fit text-left px-3 py-2 rounded-[17px] border border-[#DCDCDC] bg-white text-[#1c1a1b] hover:bg-[#f34767] hover:text-white text-lg font-medium transition-colors">
                Billing
              </button>
              <button class="block w-fit text-left px-3 py-2 rounded-[17px] border border-[#DCDCDC] bg-white text-[#1c1a1b] hover:bg-[#f34767] hover:text-white text-lg font-medium transition-colors">
                Orders
              </button>
            </div>
          </div>
        </nav>

        <div class="md:hidden border-t border-[#3f4369] pt-4 mt-4">
          <button @click="sidebarOpen = false" class="w-full flex items-center justify-center px-4 py-2 text-sm text-[#3f4369] hover:bg-[#FFEB88] hover:bg-opacity-20 rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-[#1C1A1B] bg-opacity-50 md:hidden"></div>

    <!-- Main content area -->
    <div class="md:ml-80 xl:ml-96 bg-white min-h-screen">
      <!-- Mobile menu button -->
      <div class="md:hidden p-4">
        <button @click="sidebarOpen = true" class="flex items-center px-4 py-2 text-sm text-[#3F4369] hover:bg-[#FFEB88] hover:bg-opacity-20 rounded-lg transition-colors shadow-md">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
          Menu
        </button>
      </div>

      <!-- Page content -->
      <div class="p-6">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import logoImage from '@/assets/images/logos/logo.png'

const router = useRouter()
const route = useRoute()

const sidebarOpen = ref(false)
const user = ref(null)

const currentRoute = computed(() => route.path)
const userInitials = computed(() => {
  if (!user.value?.name) return 'U'
  const names = user.value.name.split(' ')
  if (names.length >= 2) {
    return (names[0][0] + names[1][0]).toUpperCase()
  }
  return names[0][0].toUpperCase()
})

const handleLogout = async () => {
  try {
    await axios.post('/api/logout')
    localStorage.removeItem('user')
    user.value = null
    
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
    localStorage.removeItem('user')
    user.value = null
    router.push('/login')
  }
}

const navigateToSettings = () => {
  router.push('/dashboard/settings')
}

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/user')
    
    if (response.data.success) {
      user.value = {
        id: response.data.id,
        name: response.data.name,
        email: response.data.email
      }
    
      localStorage.setItem('user', JSON.stringify(user.value))
    } else {
      throw new Error('Failed to fetch user')
    }
  } catch (error) {
    console.error('Failed to fetch user:', error)
    
    if (error.response?.status === 401) {
      localStorage.removeItem('user')
      user.value = null
      router.push('/login')
    }
  }
}

onMounted(async () => {
  const savedUser = localStorage.getItem('user')
  if (savedUser) {
    try {
      user.value = JSON.parse(savedUser)
    } catch (error) {
      console.error('Failed to parse saved user data:', error)
      localStorage.removeItem('user')
    }
  }
  
  await fetchUser()
})
</script>