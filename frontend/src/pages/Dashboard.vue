<template>
  <div class="min-h-screen relative 2xl:pt-[120px] 2xl:pb-[120px] xl:pt-[60px] xl:pb-[60px]">
    <div class="dashboard-logo-bg"></div>
      <!-- Logo -->
      <div class="absolute top-[137px] left-[6.3rem] 2xl:block hidden">
        <img :src="logoImage" alt="Peerie Logo" class="w-full max-w-[205px]">
      </div>
      <div class="page-container">
      <!-- Left Sidebar -->
      <div class="fixed z-40 w-80 xl:w-[387px] overflow-y-auto transition-transform duration-300 md:translate-x-0" 
          :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex flex-col h-full">

          <div class="mb-6 rounded-[36px] border-2 border-[#3f4369] bg-white shadow-[0_4px_4px_0_rgba(0,0,0,0.25)] pl-[26px] pb-10 pt-10 xl:pl-[20px] xl:pb-[20px] xl:pt-[20px]">
            <div class="flex items-center gap-[30px]">
              <div class="w-14 h-14 rounded-full bg-[#DCDCDC] flex items-center justify-center overflow-hidden">
                <img v-if="user?.avatar" :src="user.avatar" :alt="user?.name" class="w-full h-full object-cover">
                <span v-else class="text-[#3f4369] text-lg font-bold">{{ userInitials }}</span>
              </div>
              <div class="">
                <p class="text-[#1c1a1b] text-sm font-medium">{{ texts.myAccount }}</p>
                <p class="text-[#3f4369] mt-[15px] text-xl font-bold">{{ user?.name || 'User' }}</p>
                <div class="flex mt-[18px] items-center justify-between min-w-[200px]">
                  <button @click="navigateToSettings" class="flex items-center text-[#1c1a1b] text-xs font-bold uppercase hover:bg-[#FFEB88] hover:bg-opacity-20 rounded">
                    <img :src="iconEdit" alt="" class="w-4 h-4 mr-1">
                <span>{{ texts.edit }}</span>
              </button>
                  <button @click="handleLogout" class="flex items-center text-[#1c1a1b] text-xs font-bold uppercase hover:bg-[#FFEB88] hover:bg-opacity-20 rounded">
                    <img :src="iconLogout" alt="" class="w-4 h-4 mr-1">
                <span>{{ texts.exit }}</span>
              </button>
                </div>
              </div>
            </div>
          </div>

          <nav class="space-y-6 flex-1">
            <div class="rounded-[36px] border-2 border-[#3f4369] bg-white shadow-[0_4px_4px_0_rgba(0,0,0,0.25)] p-4">
              <div class="flex items-center space-x-2 mb-3">
                <div class="w-8 h-8 rounded-lg border-2 border-[#3f4369] flex items-center justify-center">
                  <img :src="iconNotebook" alt="" class="w-5 h-5">
                </div>
                <h3 class="text-[#3f4369] text-2xl font-bold">{{ texts.create }}</h3>
              </div>
              <div class="ml-10 space-y-2 flex flex-col">
                <router-link 
                  to="/dashboard/marketing-plans"
                  class="block w-fit rounded-[17px] border px-3 py-2 text-lg font-medium transition-colors cursor-pointer"
                  :class="currentRoute === '/dashboard/marketing-plans' 
                    ? 'bg-[#f34767] text-white border-[#f34767]' 
                    : 'border-[#DCDCDC] bg-white text-[#1c1a1b] hover:bg-[#f34767] hover:text-white'"
                >
                  {{ texts.yourMarketingPlan }}
                </router-link>
                <div v-if="currentRoute === '/dashboard/marketing-plans' && availableMonths.length > 0" class="ml-4 mt-1 space-y-1">
                  <button
                    v-for="monthOption in availableMonths"
                    :key="`${monthOption.year}-${monthOption.month}`"
                    type="button"
                    class="block w-full text-left text-sm font-medium transition-colors"
                    :class="isMonthSelected(monthOption.year, monthOption.month)
                      ? 'text-[#f34767]'
                      : 'text-[#3F4369] hover:text-[#f34767]'"
                    @click="handleMonthSelect({ year: monthOption.year, month: monthOption.month })"
                  >
                    • {{ formatMonth(monthOption.year, monthOption.month) }}
                  </button>
                </div>
                <router-link 
                  to="/dashboard/image-generator"
                  class="block w-fit rounded-[17px] border px-3 py-2 text-lg font-medium transition-colors cursor-pointer"
                  :class="currentRoute === '/dashboard/image-generator' 
                    ? 'bg-[#f34767] text-white border-[#f34767]' 
                    : 'border-[#DCDCDC] bg-white text-[#1c1a1b] hover:bg-[#f34767] hover:text-white'"
                >
                  {{ texts.imageLibrary }}
                </router-link>
                <router-link 
                  to="/dashboard/hashtags"
                  class="block w-fit rounded-[17px] border px-3 py-2 text-lg font-medium transition-colors cursor-pointer"
                  :class="currentRoute === '/dashboard/hashtags' 
                    ? 'bg-[#f34767] text-white border-[#f34767]' 
                    : 'border-[#DCDCDC] bg-white text-[#1c1a1b] hover:bg-[#f34767] hover:text-white'"
                >
                  {{ texts.hashtags }}
                </router-link>
              </div>
            </div>

            <div class="rounded-[36px] border-2 border-[#3f4369] bg-white shadow-[0_4px_4px_0_rgba(0,0,0,0.25)] p-4">
              <div class="flex items-center space-x-2 mb-3">
                <div class="w-8 h-8 rounded-lg border-2 border-[#3f4369] flex items-center justify-center">
                  <img :src="iconStar" alt="" class="w-5 h-5">
                </div>
                <h3 class="text-[#3f4369] text-2xl font-bold">{{ texts.engage }}</h3>
              </div>
              <div class="ml-10 space-y-2 flex flex-col">
                <router-link
                  to="/dashboard/community"
                  class="block w-fit rounded-[17px] border px-3 py-2 text-lg font-medium transition-colors cursor-pointer"
                  :class="currentRoute === '/dashboard/community' 
                    ? 'bg-[#f34767] text-white border-[#f34767]' 
                    : 'border-[#DCDCDC] bg-white text-[#1c1a1b] hover:bg-[#f34767] hover:text-white'"
                >
                  {{ texts.community }}
                </router-link>
              </div>
            </div>

            <div class="rounded-[36px] border-2 border-[#3f4369] bg-white shadow-[0_4px_4px_0_rgba(0,0,0,0.25)] p-4">
              <div class="flex items-center space-x-2 mb-3">
                <div class="w-8 h-8 rounded-lg border-2 border-[#3f4369] flex items-center justify-center">
                  <img :src="iconUser" alt="" class="w-5 h-5">
                </div>
                <h3 class="text-[#3f4369] text-2xl font-bold">{{ texts.account }}</h3>
              </div>
              <div class="ml-10 space-y-2 flex flex-col">
                <router-link 
                  to="/dashboard/settings" 
                  class="block w-fit text-left px-3 py-2 rounded-[17px] border text-lg font-medium transition-colors"
                  :class="currentRoute === '/dashboard/settings' 
                    ? 'bg-[#f34767] text-white border-[#f34767]' 
                    : 'border-[#DCDCDC] bg-white text-[#1c1a1b] hover:bg-[#f34767] hover:text-white'"
                >
                  {{ texts.profile }}
                </router-link>
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
        <!-- Mobile header -->
        <div class="md:hidden sticky top-0 z-20 bg-white border-b border-[#DCDCDC] px-4 py-3 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <img :src="logoImage" alt="Peerie Logo" class="h-6">
          </div>
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-[#DCDCDC] flex items-center justify-center overflow-hidden">
              <img v-if="user?.avatar" :src="user.avatar" :alt="user?.name" class="w-full h-full object-cover">
              <span v-else class="text-[#3f4369] text-xs font-bold">{{ userInitials }}</span>
            </div>
            <button @click="sidebarOpen = true" class="p-2">
              <svg class="w-6 h-6 text-[#1c1a1b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Page content -->
        <div class="md:ml-[73px]">
          <router-view />
        </div>
      </div>
    </div>
    <TestEnvironmentBanner />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import logoImage from '@/assets/images/logos/logo.svg'
import iconNotebook from '@/assets/images/icons/dashboard/notebook.svg'
import iconStar from '@/assets/images/icons/dashboard/star.svg'
import iconUser from '@/assets/images/icons/dashboard/user.svg'
import iconEdit from '@/assets/images/icons/dashboard/edit.svg'
import iconLogout from '@/assets/images/icons/dashboard/log-out.svg'
import { useLanguageStore } from '@/stores/language'
import TestEnvironmentBanner from '@/components/TestEnvironmentBanner.vue'

const router = useRouter()
const route = useRoute()

const sidebarOpen = ref(false)
const user = ref(null)
const availableMonths = ref([])
const selectedYear = ref(parseInt(route.query.year) || new Date().getFullYear())
const selectedMonth = ref(parseInt(route.query.month) || new Date().getMonth() + 1)

const languageStore = useLanguageStore()

const currentRoute = computed(() => route.path)

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      myAccount: 'Mein Konto',
      edit: 'Bearbeiten',
      exit: 'Abmelden',
      create: 'Erstellen',
      yourMarketingPlan: 'Dein Marketingplan',
      imageLibrary: 'Bildbibliothek',
      hashtags: 'Hashtags',
      engage: 'Interaktion',
      community: 'Community',
      account: 'Konto',
      profile: 'Profil',
      menu: 'Menü'
    }
  }

  return {
    myAccount: 'My account',
    edit: 'Edit',
    exit: 'Exit',
    create: 'Create',
    yourMarketingPlan: 'Your Marketing Plan',
    imageLibrary: 'Image Library',
    hashtags: 'Hashtags',
    engage: 'Engage',
    community: 'Community',
    account: 'Account',
    profile: 'Profile',
    menu: 'Menu'
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

let lastUserFetchTime = 0
const USER_FETCH_CACHE_TIME = 1000

const fetchUser = async (force = false) => {
  const now = Date.now()
  const timeSinceLastFetch = now - lastUserFetchTime
  
  if (!force && timeSinceLastFetch < USER_FETCH_CACHE_TIME) {
    return
  }
  
  try {
    const response = await axios.get('/api/user')
    lastUserFetchTime = Date.now()
    
    if (response.data.success) {
      user.value = {
        id: response.data.id,
        name: response.data.name,
        email: response.data.email,
        avatar: response.data.avatar
      }
      
      if (response.data.language) {
        languageStore.setLanguage(response.data.language)
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

const handleProfileUpdated = (event) => {
  const detail = event?.detail || {}
  if (!user.value) {
    user.value = {}
  }
  if (detail.name) {
    user.value.name = detail.name
  }
  if (detail.email) {
    user.value.email = detail.email
  }
  if (detail.language) {
    languageStore.setLanguage(detail.language)
  }
  try {
    localStorage.setItem('user', JSON.stringify(user.value))
  } catch (error) {
    console.error('Failed to store updated user:', error)
  }
}

const fetchAvailableMonths = async () => {
  try {
    const response = await axios.get('/api/plans/available-months')
    if (response.data.success && response.data.months) {
      availableMonths.value = response.data.months
      
      const now = new Date()
      const currentYear = now.getFullYear()
      const currentMonth = now.getMonth() + 1
      
      if (availableMonths.value.length > 0 && !route.query.year && !route.query.month) {
        const latest = availableMonths.value[0]
        selectedYear.value = latest.year
        selectedMonth.value = latest.month
        router.replace({
          query: {
            ...route.query,
            year: latest.year,
            month: latest.month
          }
        })
      } else if (!route.query.year && !route.query.month) {
        selectedYear.value = currentYear
        selectedMonth.value = currentMonth
        router.replace({
          query: {
            ...route.query,
            year: currentYear,
            month: currentMonth
          }
        })
      }
    } else {
      const now = new Date()
      const currentYear = now.getFullYear()
      const currentMonth = now.getMonth() + 1
      availableMonths.value = [{
        year: currentYear,
        month: currentMonth
      }]
      if (!route.query.year && !route.query.month) {
        selectedYear.value = currentYear
        selectedMonth.value = currentMonth
        router.replace({
          query: {
            ...route.query,
            year: currentYear,
            month: currentMonth
          }
        })
      }
    }
  } catch (error) {
    console.error('Failed to load available months:', error)
    const now = new Date()
    const currentYear = now.getFullYear()
    const currentMonth = now.getMonth() + 1
    availableMonths.value = [{
      year: currentYear,
      month: currentMonth
    }]
  }
}

const isMonthSelected = (year, month) => {
  return selectedYear.value === year && selectedMonth.value === month
}

const formatMonth = (year, month) => {
  const date = new Date(year, month - 1, 1)
  const monthNames = languageStore.language === 'de'
    ? ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez']
    : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  
  return `${monthNames[month - 1]} ${year}`
}

const handleMonthSelect = ({ year, month }) => {
  selectedYear.value = year
  selectedMonth.value = month
  router.push({
    path: '/dashboard/marketing-plans',
    query: {
      year,
      month
    }
  })
}

watch(() => route.path, async (newPath) => {
  if (newPath === '/dashboard/marketing-plans') {
    await fetchAvailableMonths()
    if (route.query.year && route.query.month) {
      selectedYear.value = parseInt(route.query.year)
      selectedMonth.value = parseInt(route.query.month)
    }
  }
}, { immediate: true })

watch(() => route.query, (newQuery) => {
  if (newQuery.year && newQuery.month && route.path === '/dashboard/marketing-plans') {
    selectedYear.value = parseInt(newQuery.year)
    selectedMonth.value = parseInt(newQuery.month)
  }
}, { immediate: false })

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
  window.addEventListener('profile-updated', handleProfileUpdated)
})

onUnmounted(() => {
  window.removeEventListener('profile-updated', handleProfileUpdated)
})
</script>