<template>
  <div class="min-h-screen bg-white">
    <!-- Top Header -->
    <div class="fixed top-0 left-0 right-0 z-50 h-16 flex items-center justify-between px-6 bg-white border-b border-[#DCDCDC]">
      <!-- Logo -->
      <div class="flex items-center space-x-3">
          <img src="@/assets/images/logos/logo.png" alt="Peerie Logo" class="w-40 h-40 rounded-lg">
      </div>

      <!-- Right side - User avatar and dropdown -->
      <div class="flex items-center space-x-4">
        <!-- User dropdown -->
        <div class="relative">
          <button 
            @click="userDropdownOpen = !userDropdownOpen"
            class="flex items-center space-x-3 p-2 rounded-lg transition-colors hover:bg-[#FFEBD0]"
          >
            <div class="w-8 h-8 bg-[#F34767] rounded-full flex items-center justify-center">
              <span class="text-white text-sm font-medium">{{ userInitials }}</span>
            </div>
            <div class="hidden md:block text-left">
              <p class="text-sm font-medium text-[#3F4369]">{{ user?.name }}</p>
              <p class="text-xs text-[#3F4369] opacity-70">{{ user?.email }}</p>
            </div>
            <svg class="w-4 h-4 text-[#3F4369]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>

          <!-- Dropdown menu -->
          <div 
            v-if="userDropdownOpen" 
            class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-[#DCDCDC] py-2 z-50"
          >
            <!-- User info header -->
            <div class="px-4 py-3 border-b border-[#DCDCDC]">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-[#F34767] rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-medium">{{ userInitials }}</span>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-[#3F4369]">{{ user?.name }}</p>
                    <p class="text-xs text-[#3F4369] opacity-70">{{ user?.email }}</p>
                  </div>
                </div>
                <button @click="userDropdownOpen = false" class="text-[#3F4369] hover:text-[#F34767]">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Menu items -->
            <div class="py-2">
              <!-- Settings -->
              <button 
                @click="navigateToSettings"
                class="w-full flex items-center px-4 py-2 text-sm hover:bg-[#FFEBD0] transition-colors text-[#3F4369]"
              >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Settings
              </button>
            </div>

            <div class="border-t py-2 border-[#DCDCDC]">
              <!-- Logout -->
              <button 
                @click="handleLogout"
                class="w-full flex items-center px-4 py-2 text-sm text-[#F34767] hover:bg-red-50 transition-colors"
              >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Left Sidebar -->
    <div class="fixed top-16 left-0 bottom-0 z-40 w-64 md:translate-x-0" 
         :class="[
           'bg-[#3F4369]',
           sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
         ]">
      <div class="flex flex-col h-full">
        
        <!-- Sidebar Header -->
        <div class="px-6 py-6 border-b border-[#1C8E9E]">
          <h2 class="text-sm font-medium text-white opacity-70">NAVIGATION</h2>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-3">
          

          <router-link 
            to="/dashboard/marketing-plans" 
            class="flex items-center px-4 py-3 rounded-lg transition-colors group"
            :class="currentRoute === '/dashboard/marketing-plans' 
              ? 'bg-[#F34767] text-white shadow-lg' 
              : 'text-white hover:bg-[#1C8E9E] hover:shadow-md'"
          >
            <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3"
                 :class="currentRoute === '/dashboard/marketing-plans' 
                   ? 'bg-white bg-opacity-20' 
                   : 'group-hover:bg-white group-hover:bg-opacity-10'">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            Marketing Plans
          </router-link>

          <router-link 
            to="/dashboard/image-generator" 
            class="flex items-center px-4 py-3 rounded-lg transition-colors group"
            :class="currentRoute === '/dashboard/image-generator' 
              ? 'bg-[#F34767] text-white shadow-lg' 
              : 'text-white hover:bg-[#1C8E9E] hover:shadow-md'"
          >
            <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3"
                 :class="currentRoute === '/dashboard/image-generator' 
                   ? 'bg-white bg-opacity-20' 
                   : 'group-hover:bg-white group-hover:bg-opacity-10'">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
            Image Generator
          </router-link>

          <router-link 
            to="/dashboard/hashtags" 
            class="flex items-center px-4 py-3 rounded-lg transition-colors group"
            :class="currentRoute === '/dashboard/hashtags' 
              ? 'bg-[#F34767] text-white shadow-lg' 
              : 'text-white hover:bg-[#1C8E9E] hover:shadow-md'"
          >
            <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3"
                 :class="currentRoute === '/dashboard/hashtags' 
                   ? 'bg-white bg-opacity-20' 
                   : 'group-hover:bg-white group-hover:bg-opacity-10'">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
              </svg>
            </div>
            Hashtags
          </router-link>

          
        </nav>

        <!-- Mobile close button -->
        <div class="p-4 md:hidden border-t border-[#1C8E9E]">
          <button @click="sidebarOpen = false" class="w-full flex items-center justify-center px-4 py-2 text-sm text-white hover:bg-[#1C8E9E] rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black bg-opacity-50 md:hidden"></div>

    <!-- Main content area -->
    <div class="pt-16 md:ml-64 bg-[#FFEBD0] min-h-screen">
      <!-- Mobile menu button -->
      <div class="md:hidden p-4">
        <button @click="sidebarOpen = true" class="flex items-center px-4 py-2 text-sm text-[#3F4369] hover:bg-white rounded-lg transition-colors shadow-md">
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

const router = useRouter()
const route = useRoute()

const sidebarOpen = ref(false)
const userDropdownOpen = ref(false)
const user = ref(null)

const currentRoute = computed(() => route.path)
const userInitials = computed(() => {
  if (!user.value?.name) return 'U'
  return user.value.name.split(' ').map(n => n[0]).join('').toUpperCase()
})

const handleLogout = async () => {
  try {
    await axios.post('/api/logout')
    localStorage.removeItem('user')
    userDropdownOpen.value = false
    user.value = null
    
    router.push('/login')
  } catch (error) {
    console.error('Ошибка выхода:', error)
    localStorage.removeItem('user')
    user.value = null
    router.push('/login')
  }
}

const navigateToSettings = () => {
  userDropdownOpen.value = false
  router.push('/dashboard/settings')
}

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/user')
    user.value = response.data
    
    localStorage.setItem('user', JSON.stringify(response.data))
  } catch (error) {
    console.error('Ошибка получения пользователя:', error)
    
    // Если пользователь не авторизован, перенаправляем на логин
    if (error.response?.status === 401) {
      localStorage.removeItem('user')
      router.push('/login')
    }
  }
}

onMounted(async () => {
  // Загружаем сохраненные данные пользователя
  const savedUser = localStorage.getItem('user')
  if (savedUser) {
    try {
      user.value = JSON.parse(savedUser)
    } catch (error) {
      console.error('Ошибка парсинга сохраненных данных пользователя:', error)
      localStorage.removeItem('user')
    }
  }
  
  // Загружаем актуальные данные пользователя с сервера
  await fetchUser()
  
  // Закрываем dropdown при клике вне его
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.relative')) {
      userDropdownOpen.value = false
    }
  })
})
</script>