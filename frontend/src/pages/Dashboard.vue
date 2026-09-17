<template>
  <div class="min-h-screen bg-light-grey">
    <div class="flex md:h-dvh md:min-h-0 md:overflow-hidden">
      <aside
        class="group/sidebar relative hidden md:flex shrink-0 flex-col m-5 mr-0 transition-all duration-300"
        :class="sidebarCollapsed ? 'w-[72px]' : 'w-[240px]'"
      >
        <div class="flex flex-col h-full min-h-0 bg-white rounded-[30px] shadow-card overflow-hidden">
        <div class="flex flex-col h-full min-h-0 px-3 py-6">
          <div class="mb-8 flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'px-2'">
            <router-link
              to="/dashboard"
              class="block overflow-hidden"
              :class="sidebarCollapsed ? 'w-8 h-8' : 'w-[136px]'"
            >
              <img
                :src="logoImage"
                alt="Peerie"
                :class="sidebarCollapsed ? 'h-8 w-auto max-w-none' : 'w-full h-auto'"
              >
            </router-link>
          </div>

          <nav class="flex-1 min-h-0 overflow-y-auto no-scrollbar space-y-6">
            <div>
              <p v-if="!sidebarCollapsed" class="px-2 mb-2 text-[12px] text-purple/70 tracking-[-0.6px]">
                {{ texts.workspace }}
              </p>
              <div class="space-y-1">
                <router-link
                  to="/dashboard"
                  class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5 transition-colors"
                  :class="navLinkClass('/dashboard')"
                  :title="sidebarCollapsed ? texts.dashboard : ''"
                >
                  <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center shrink-0">
                    <img :src="iconDashboard" alt="" class="w-3.5 h-3.5">
                  </span>
                  <span v-if="!sidebarCollapsed" class="text-[18px] font-bold tracking-[-0.9px]">
                    {{ texts.dashboard }}
                  </span>
                </router-link>

                <router-link
                  to="/dashboard/marketing-plans"
                  class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5 transition-colors"
                  :class="navLinkClass('/dashboard/marketing-plans')"
                  :title="sidebarCollapsed ? texts.yourMarketingPlan : ''"
                >
                  <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center shrink-0">
                    <img :src="iconMarketingPlan" alt="" class="w-3.5 h-3.5">
                  </span>
                  <span v-if="!sidebarCollapsed" class="text-[18px] font-bold tracking-[-0.9px]">
                    {{ texts.yourMarketingPlan }}
                  </span>
                </router-link>

                <div
                  v-if="!sidebarCollapsed && isMarketingPlans && availableMonths.length > 0"
                  class="ml-8 space-y-1 py-1"
                >
                  <button
                    v-for="monthOption in availableMonths"
                    :key="`${monthOption.year}-${monthOption.month}`"
                    type="button"
                    class="flex items-center gap-2 w-full text-left text-[14px] tracking-[-0.7px] transition-colors"
                    :class="isMonthSelected(monthOption.year, monthOption.month)
                      ? 'text-red font-medium'
                      : 'text-purple hover:text-red'"
                    @click="handleMonthSelect({ year: monthOption.year, month: monthOption.month })"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-red shrink-0" />
                    {{ formatMonth(monthOption.year, monthOption.month) }}
                  </button>
                </div>

                <router-link
                  to="/dashboard/content-ideas"
                  class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5 transition-colors"
                  :class="navLinkClass('/dashboard/content-ideas')"
                  :title="sidebarCollapsed ? texts.contentIdeas : ''"
                >
                  <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center shrink-0">
                    <img :src="iconContentCalendar" alt="" class="w-3.5 h-3.5">
                  </span>
                  <span v-if="!sidebarCollapsed" class="text-[18px] font-bold tracking-[-0.9px]">
                    {{ texts.contentIdeas }}
                  </span>
                </router-link>

                <div>
                  <button
                    type="button"
                    class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5 w-full transition-colors hover:bg-rose"
                    :class="[resourceLibraryActive ? 'bg-rose' : '', sidebarCollapsed ? 'justify-center' : '']"
                    :title="sidebarCollapsed ? texts.resourceLibrary : ''"
                    @click="toggleResourceLibrary"
                  >
                    <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center shrink-0">
                      <img :src="iconResourceLibrary" alt="" class="w-3.5 h-3.5">
                    </span>
                    <span v-if="!sidebarCollapsed" class="flex-1 text-left text-[18px] font-bold tracking-[-0.9px]">
                      {{ texts.resourceLibrary }}
                    </span>
                    <img
                      v-if="!sidebarCollapsed"
                      :src="iconChevron"
                      alt=""
                      class="w-3 h-3 transition-transform"
                      :class="resourceLibraryOpen ? 'rotate-90' : ''"
                    >
                  </button>

                  <div v-if="!sidebarCollapsed && resourceLibraryOpen" class="ml-8 space-y-1 py-1">
                    <router-link
                      to="/dashboard/image-generator"
                      class="flex items-center gap-2 text-[14px] tracking-[-0.7px] transition-colors"
                      :class="currentRoute === '/dashboard/image-generator' ? 'text-red font-medium' : 'text-purple hover:text-red'"
                      @click="closeSidebarOnMobile"
                    >
                      <span class="w-1.5 h-1.5 rounded-full bg-red shrink-0" />
                      {{ texts.imageLibrary }}
                    </router-link>
                    <router-link
                      to="/dashboard/hashtags"
                      class="flex items-center gap-2 text-[14px] tracking-[-0.7px] transition-colors"
                      :class="currentRoute === '/dashboard/hashtags' ? 'text-red font-medium' : 'text-purple hover:text-red'"
                      @click="closeSidebarOnMobile"
                    >
                      <span class="w-1.5 h-1.5 rounded-full bg-red shrink-0" />
                      {{ texts.hashtags }}
                    </router-link>
                    <router-link
                      to="/dashboard/learn"
                      class="flex items-center gap-2 text-[14px] tracking-[-0.7px] transition-colors"
                      :class="currentRoute.startsWith('/dashboard/learn') ? 'text-red font-medium' : 'text-purple hover:text-red'"
                      @click="closeSidebarOnMobile"
                    >
                      <span class="w-1.5 h-1.5 rounded-full bg-red shrink-0" />
                      {{ texts.resources }}
                    </router-link>
                  </div>
                </div>

                <router-link
                  to="/dashboard/documents"
                  class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5 transition-colors"
                  :class="navLinkClass('/dashboard/documents')"
                  :title="sidebarCollapsed ? texts.documents : ''"
                >
                  <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center shrink-0">
                    <img :src="iconDocuments" alt="" class="w-[14px] h-[14px]">
                  </span>
                  <span v-if="!sidebarCollapsed" class="text-[18px] font-bold tracking-[-0.9px]">
                    {{ texts.documents }}
                  </span>
                </router-link>

                <router-link
                  to="/dashboard/community"
                  class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5 transition-colors"
                  :class="navLinkClass('/dashboard/community')"
                  :title="sidebarCollapsed ? texts.community : ''"
                >
                  <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center shrink-0">
                    <img :src="iconCommunity" alt="" class="w-3.5 h-3.5">
                  </span>
                  <span v-if="!sidebarCollapsed" class="text-[18px] font-bold tracking-[-0.9px]">
                    {{ texts.community }}
                  </span>
                </router-link>
              </div>
            </div>

            <div>
              <p v-if="!sidebarCollapsed" class="px-2 mb-2 text-[12px] text-purple/70 tracking-[-0.6px]">
                {{ texts.account }}
              </p>
              <router-link
                to="/dashboard/settings"
                class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5 transition-colors"
                :class="navLinkClass('/dashboard/settings')"
                :title="sidebarCollapsed ? texts.profile : ''"
              >
                <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center shrink-0">
                  <img :src="iconSettings" alt="" class="w-3.5 h-3.5">
                </span>
                <span v-if="!sidebarCollapsed" class="text-[18px] font-bold tracking-[-0.9px]">
                  {{ texts.profile }}
                </span>
              </router-link>
            </div>
          </nav>

          <div class="mt-4 space-y-3 shrink-0">
            <div v-if="!sidebarCollapsed" class="space-y-2 px-1">
              <a
                href="https://peerie.com"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center gap-2 text-[14px] text-purple hover:text-red tracking-[-0.7px]"
              >
                <img :src="iconBlog" alt="" class="w-3.5 h-3.5">
                {{ texts.blog }}
              </a>
              <a
                href="mailto:hello@peerie.com"
                class="flex items-center gap-2 text-[14px] text-purple hover:text-red tracking-[-0.7px]"
              >
                <img :src="iconHelp" alt="" class="w-3.5 h-3.5">
                {{ texts.help }}
              </a>
            </div>

            <div
              class="bg-light-grey rounded-[20px] border-4 border-rose p-2 flex items-center gap-2"
              :class="sidebarCollapsed ? 'justify-center' : ''"
            >
              <div class="w-[30px] h-[30px] rounded-[20px] bg-yellow border-4 border-rose flex items-center justify-center overflow-hidden shrink-0">
                <img v-if="user?.avatar" :src="user.avatar" :alt="user?.name" class="w-full h-full object-cover">
                <img v-else :src="iconUser" alt="" class="w-3.5 h-3.5">
              </div>
              <div v-if="!sidebarCollapsed" class="min-w-0 flex-1">
                <p class="text-[12px] font-bold uppercase tracking-[-0.6px] text-black truncate">
                  {{ user?.name || 'User' }}
                </p>
                <p class="text-[12px] text-purple/70 tracking-[-0.6px] truncate">
                  {{ texts.businessAccount }}
                </p>
              </div>
              <button
                v-if="!sidebarCollapsed"
                type="button"
                class="text-[11px] font-bold uppercase tracking-[-0.5px] text-purple hover:text-red shrink-0"
                @click="handleLogout"
              >
                {{ texts.exit }}
              </button>
            </div>
          </div>
        </div>
        </div>

        <button
          type="button"
          class="absolute top-8 -right-3 z-30 flex items-center justify-center w-7 h-7 rounded-full bg-white border border-grey shadow-card text-purple opacity-0 pointer-events-none transition-all duration-150 group-hover/sidebar:opacity-100 group-hover/sidebar:pointer-events-auto hover:text-green hover:border-green"
          :title="sidebarCollapsed ? texts.expandSidebar : texts.collapseSidebar"
          @click="toggleSidebar"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3.5" y="4.5" width="17" height="15" rx="2.5" />
            <path d="M9.5 4.5v15" />
          </svg>
        </button>
      </aside>

      <div
        class="fixed top-0 bottom-0 z-40 w-full md:hidden h-dvh overflow-y-auto no-scrollbar overscroll-contain transition-all duration-300 sidebar bg-white"
        :class="sidebarOpen ? 'translate-x-0 left-0' : 'sidebar-hidden'"
      >
        <div class="flex flex-col min-h-full px-4 pt-10 pb-10">
          <button
            type="button"
            class="absolute top-3 right-3 p-2 text-black hover:bg-rose rounded-lg"
            @click="sidebarOpen = false"
          >
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <router-link to="/dashboard" class="block w-[140px] mb-8" @click="closeSidebarOnMobile">
            <img :src="logoImage" alt="Peerie" class="w-full h-auto">
          </router-link>

          <nav class="space-y-6 flex-1">
            <div>
              <p class="mb-2 text-[12px] text-purple/70">{{ texts.workspace }}</p>
              <div class="space-y-1">
                <router-link
                  to="/dashboard"
                  class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5"
                  :class="navLinkClass('/dashboard')"
                  @click="closeSidebarOnMobile"
                >
                  <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center">
                    <img :src="iconDashboard" alt="" class="w-3.5 h-3.5">
                  </span>
                  <span class="text-[18px] font-bold">{{ texts.dashboard }}</span>
                </router-link>
                <router-link
                  to="/dashboard/marketing-plans"
                  class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5"
                  :class="navLinkClass('/dashboard/marketing-plans')"
                  @click="closeSidebarOnMobile"
                >
                  <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center">
                    <img :src="iconMarketingPlan" alt="" class="w-3.5 h-3.5">
                  </span>
                  <span class="text-[18px] font-bold">{{ texts.yourMarketingPlan }}</span>
                </router-link>
                <router-link
                  to="/dashboard/content-ideas"
                  class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5"
                  :class="navLinkClass('/dashboard/content-ideas')"
                  @click="closeSidebarOnMobile"
                >
                  <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center">
                    <img :src="iconContentCalendar" alt="" class="w-3.5 h-3.5">
                  </span>
                  <span class="text-[18px] font-bold">{{ texts.contentIdeas }}</span>
                </router-link>
                <router-link
                  to="/dashboard/image-generator"
                  class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5"
                  :class="navLinkClass('/dashboard/image-generator')"
                  @click="closeSidebarOnMobile"
                >
                  <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center">
                    <img :src="iconResourceLibrary" alt="" class="w-3.5 h-3.5">
                  </span>
                  <span class="text-[18px] font-bold">{{ texts.imageLibrary }}</span>
                </router-link>
                <router-link
                  to="/dashboard/hashtags"
                  class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5"
                  :class="navLinkClass('/dashboard/hashtags')"
                  @click="closeSidebarOnMobile"
                >
                  <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center">
                    <img :src="iconActionSteps" alt="" class="w-3.5 h-3.5">
                  </span>
                  <span class="text-[18px] font-bold">{{ texts.hashtags }}</span>
                </router-link>
                <router-link
                  to="/dashboard/learn"
                  class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5"
                  :class="navLinkClass('/dashboard/learn')"
                  @click="closeSidebarOnMobile"
                >
                  <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center">
                    <img :src="iconLearn" alt="" class="w-3.5 h-3.5">
                  </span>
                  <span class="text-[18px] font-bold">{{ texts.resources }}</span>
                </router-link>
                <router-link
                  to="/dashboard/documents"
                  class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5"
                  :class="navLinkClass('/dashboard/documents')"
                  @click="closeSidebarOnMobile"
                >
                  <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center">
                    <img :src="iconDocuments" alt="" class="w-[14px] h-[14px]">
                  </span>
                  <span class="text-[18px] font-bold">{{ texts.documents }}</span>
                </router-link>
                <router-link
                  to="/dashboard/community"
                  class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5"
                  :class="navLinkClass('/dashboard/community')"
                  @click="closeSidebarOnMobile"
                >
                  <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center">
                    <img :src="iconCommunity" alt="" class="w-3.5 h-3.5">
                  </span>
                  <span class="text-[18px] font-bold">{{ texts.community }}</span>
                </router-link>
              </div>
            </div>
            <div>
              <p class="mb-2 text-[12px] text-purple/70">{{ texts.account }}</p>
              <router-link
                to="/dashboard/settings"
                class="flex items-center gap-3 rounded-[8px] px-2.5 py-2.5"
                :class="navLinkClass('/dashboard/settings')"
                @click="closeSidebarOnMobile"
              >
                <span class="w-5 h-5 rounded-[12px] bg-mint flex items-center justify-center">
                  <img :src="iconSettings" alt="" class="w-3.5 h-3.5">
                </span>
                <span class="text-[18px] font-bold">{{ texts.profile }}</span>
              </router-link>
            </div>
          </nav>

          <button
            type="button"
            class="mt-6 w-full rounded-[20px] bg-red text-white px-3 py-3 text-[14px] font-bold uppercase tracking-[-0.7px]"
            @click="handleLogout"
          >
            {{ texts.exit }}
          </button>
        </div>
      </div>

      <div
        v-if="sidebarOpen"
        class="fixed inset-0 z-30 bg-black/50 md:hidden"
        @click="sidebarOpen = false"
      />

      <div class="flex-1 min-h-screen md:min-h-0 md:h-full md:overflow-y-auto no-scrollbar">
        <div class="sticky top-0 z-20 bg-light-grey/95 backdrop-blur-sm px-4 md:px-8 py-4 flex items-center justify-between md:justify-end gap-3">
          <div class="md:hidden flex items-center gap-2">
            <img :src="logoImage" alt="Peerie" class="h-6">
          </div>
          <div class="flex items-center gap-3">
            <p class="hidden sm:block text-[12px] font-bold uppercase tracking-[-0.6px] text-black">
              {{ firstName }}
            </p>
            <div class="w-10 h-10 rounded-[20px] bg-yellow border-4 border-rose flex items-center justify-center overflow-hidden">
              <img v-if="user?.avatar" :src="user.avatar" :alt="user?.name" class="w-full h-full object-cover">
              <span v-else class="text-[14px] font-bold text-black">{{ userInitials }}</span>
            </div>
            <button type="button" class="md:hidden p-2" @click="sidebarOpen = true">
              <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>

        <div class="px-4 md:px-8 pb-10">
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
import iconDashboard from '@/assets/images/icons/dashboard/v2/dashboard.svg'
import iconMarketingPlan from '@/assets/images/icons/dashboard/v2/marketing-plan.svg'
import iconContentCalendar from '@/assets/images/icons/dashboard/v2/content-calendar.svg'
import iconResourceLibrary from '@/assets/images/icons/dashboard/v2/resource-library.svg'
import iconActionSteps from '@/assets/images/icons/dashboard/v2/action-steps.svg'
import iconCommunity from '@/assets/images/icons/dashboard/v2/community.svg'
import iconSettings from '@/assets/images/icons/dashboard/v2/settings.svg'
import iconLearn from '@/assets/images/icons/dashboard/v2/learn.svg'
import iconDocuments from '@/assets/images/icons/dashboard/v2/documents.svg'
import iconChevron from '@/assets/images/icons/dashboard/v2/chevron.svg'
import iconBlog from '@/assets/images/icons/dashboard/v2/blog.svg'
import iconHelp from '@/assets/images/icons/dashboard/v2/help.svg'
import iconUser from '@/assets/images/icons/dashboard/v2/user.svg'
import { useLanguageStore } from '@/stores/language'
import TestEnvironmentBanner from '@/components/TestEnvironmentBanner.vue'

const router = useRouter()
const route = useRoute()

const sidebarOpen = ref(false)
const sidebarCollapsed = ref(localStorage.getItem('sidebarCollapsed') === 'true')
const resourceLibraryOpen = ref(
  ['/dashboard/image-generator', '/dashboard/hashtags'].includes(route.path)
  || route.path.startsWith('/dashboard/learn')
)
const user = ref(null)
const availableMonths = ref([])
const selectedYear = ref(parseInt(route.query.year) || new Date().getFullYear())
const selectedMonth = ref(parseInt(route.query.month) || new Date().getMonth() + 1)

const languageStore = useLanguageStore()

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
  localStorage.setItem('sidebarCollapsed', sidebarCollapsed.value)
}

const toggleResourceLibrary = () => {
  if (sidebarCollapsed.value) {
    sidebarCollapsed.value = false
    localStorage.setItem('sidebarCollapsed', 'false')
    resourceLibraryOpen.value = true
    return
  }
  resourceLibraryOpen.value = !resourceLibraryOpen.value
}

const currentRoute = computed(() => route.path)

const isDashboardHome = computed(() => currentRoute.value === '/dashboard')
const isMarketingPlans = computed(() => currentRoute.value === '/dashboard/marketing-plans')

const resourceLibraryActive = computed(() =>
  currentRoute.value === '/dashboard/image-generator'
  || currentRoute.value === '/dashboard/hashtags'
  || currentRoute.value.startsWith('/dashboard/learn')
)

const navLinkClass = (path) => {
  const active = path === '/dashboard'
    ? isDashboardHome.value
    : path === '/dashboard/learn'
      ? currentRoute.value.startsWith('/dashboard/learn')
      : path === '/dashboard/documents'
        ? currentRoute.value.startsWith('/dashboard/documents')
        : currentRoute.value === path
  const base = active
    ? 'bg-red text-white'
    : 'text-black hover:bg-rose'
  return sidebarCollapsed.value ? `${base} justify-center` : base
}

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      workspace: 'Workspace',
      dashboard: 'Dashboard',
      yourMarketingPlan: 'Marketingplan',
      imageLibrary: 'Bildbibliothek',
      contentIdeas: 'Content Ideen',
      hashtags: 'Hashtags',
      resourceLibrary: 'Ressourcen',
      community: 'Community',
      resources: 'Guides & Checklisten',
      documents: 'Dokumente',
      account: 'Konto',
      profile: 'Profil',
      exit: 'Abmelden',
      collapseSidebar: 'Seitenleiste einklappen',
      expandSidebar: 'Seitenleiste ausklappen',
      blog: 'Peerie Blog',
      help: 'Hilfe & Support',
      businessAccount: 'Business-Konto'
    }
  }

  return {
    workspace: 'Workspace',
    dashboard: 'Dashboard',
    yourMarketingPlan: 'Marketing Plan',
    imageLibrary: 'Image Library',
    contentIdeas: 'Content Ideas',
    hashtags: 'Hashtags',
    resourceLibrary: 'Resource Library',
    community: 'Community',
    resources: 'Guides & Checklists',
    documents: 'Documents',
    account: 'Account',
    profile: 'Profile',
    exit: 'Log out',
    collapseSidebar: 'Collapse sidebar',
    expandSidebar: 'Expand sidebar',
    blog: 'Peerie blog',
    help: 'Help & Support',
    businessAccount: 'Business account'
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

const firstName = computed(() => {
  if (!user.value?.name) return 'USER'
  return user.value.name.split(' ')[0].toUpperCase()
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

const closeSidebarOnMobile = () => {
  if (window.innerWidth < 768) {
    sidebarOpen.value = false
  }
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
      const hasCompletedQuestionnaire = Boolean(
        (response.data?.user?.has_completed_questionnaire ?? response.data?.has_completed_questionnaire) === true
      )

      if (!hasCompletedQuestionnaire) {
        router.replace('/questionnaire')
        return
      }

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
          path: '/dashboard/marketing-plans',
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
          path: '/dashboard/marketing-plans',
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
          path: '/dashboard/marketing-plans',
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
  const monthNames = languageStore.language === 'de'
    ? ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
    : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']

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
  closeSidebarOnMobile()
}

watch(() => route.path, async (newPath) => {
  if (newPath === '/dashboard/marketing-plans') {
    await fetchAvailableMonths()
    if (route.query.year && route.query.month) {
      selectedYear.value = parseInt(route.query.year)
      selectedMonth.value = parseInt(route.query.month)
    }
  }
  if (
    ['/dashboard/image-generator', '/dashboard/hashtags'].includes(newPath)
    || newPath.startsWith('/dashboard/learn')
  ) {
    resourceLibraryOpen.value = true
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
