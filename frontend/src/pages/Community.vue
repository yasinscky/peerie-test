<template>
  <div class="max-w-7xl mx-auto">
    <div class="bg-[#f34767] h-32 lg:h-28 px-4 lg:px-8 flex items-center justify-between rounded-40 mb-8">
      <div class="flex items-center space-x-4">
        <div class="w-10 h-10 rounded-lg bg-opacity-20 flex items-center justify-center">
          <img :src="logoWhite" alt="Peerie Logo" class="w-10 h-10">
        </div>
        <h1 class="text-white text-4xl lg:text-3xl font-bold">{{ texts.headerTitle }}</h1>
      </div>
      <div class="flex items-center space-x-2 text-white text-base lg:text-xl font-medium">
        <span>{{ texts.headerSection }}</span>
        <span class="opacity-40">|</span>
        <span class="opacity-40">{{ texts.headerCurrent }}</span>
      </div>
    </div>

    <div class="mb-8">
      <p class="text-[#3F4369] opacity-70 mt-2">
        {{ texts.subtitle }}
      </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[2fr,1.2fr] gap-6">
      <div class="bg-white rounded-2xl shadow-lg border border-[#DCDCDC] p-6">
        <h2 class="text-2xl font-bold text-[#3F4369] mb-3">
          {{ texts.mainTitle }}
        </h2>
        <p class="text-sm text-[#3F4369] opacity-80 mb-6">
          {{ texts.mainText }}
        </p>

        <div class="space-y-4">
          <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-full bg-[#FFEBD0] flex items-center justify-center">
              <span class="text-[#3F4369] text-base font-semibold">1</span>
            </div>
            <p class="text-sm text-[#3F4369] opacity-80">
              {{ texts.stepOne }}
            </p>
          </div>
          <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-full bg-[#FFEBD0] flex items-center justify-center">
              <span class="text-[#3F4369] text-base font-semibold">2</span>
            </div>
            <p class="text-sm text-[#3F4369] opacity-80">
              {{ texts.stepTwo }}
            </p>
          </div>
        </div>
      </div>

      <div class="bg-[#5865F2] rounded-2xl p-6 flex flex-col justify-between min-h-[220px]">
        <div>
          <p class="text-white text-sm font-semibold uppercase tracking-wide mb-2">
            Discord
          </p>
          <h2 class="text-white text-2xl font-bold mb-2">
            {{ texts.discordTitle }}
          </h2>
          <p class="text-white text-sm opacity-80 mb-4">
            {{ texts.discordText }}
          </p>
        </div>
        <div class="flex items-center justify-between gap-3">
          <button
            type="button"
            class="px-4 py-2 bg-white text-[#5865F2] font-semibold rounded-xl hover:bg-gray-100 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
            :disabled="!discordInviteUrl || isLoading"
            @click="goToDiscord"
          >
            {{ texts.openDiscord }}
          </button>
          <span
            v-if="isLoading"
            class="text-xs text-white opacity-80"
          >
            {{ texts.loading }}
          </span>
          <span
            v-else-if="!discordInviteUrl"
            class="text-xs text-white opacity-80"
          >
            {{ texts.error }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'
import logoWhite from '@/assets/images/logos/logo-white.svg'

const languageStore = useLanguageStore()

const discordInviteUrl = ref(null)
const isLoading = ref(true)

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      headerTitle: 'Community',
      headerSection: 'Interaktion',
      headerCurrent: 'Community',
      subtitle: 'Verbinde dich mit anderen Gründern, stelle Fragen und teile deine Ergebnisse.',
      mainTitle: 'Peerie Community',
      mainText: 'In unserer Discord-Community kannst du Fragen zu deinem Marketingplan stellen, Feedback zu Inhalten bekommen und dich mit Gründern auf dem gleichen Level austauschen.',
      stepOne: 'Klicke auf „Discord öffnen“, um unserem privaten Server beizutreten.',
      stepTwo: 'Stelle dich kurz vor und teile dein Business – wir helfen dir, die nächsten Schritte zu priorisieren.',
      discordTitle: 'Tritt unserem Discord-Server bei',
      discordText: 'Erhalte schnelle Antworten, Inspiration für Inhalte und Zugang zu exklusiven Sessions.',
      openDiscord: 'Discord öffnen',
      loading: 'Lade Einladungslink…',
      error: 'Der Einladungslink konnte nicht geladen werden.',
    }
  }

  return {
    headerTitle: 'Community',
    headerSection: 'Engage',
    headerCurrent: 'Community',
    subtitle: 'Connect with other founders, ask questions and share your results.',
    mainTitle: 'Peerie Community',
    mainText: 'In our Discord community you can ask questions about your marketing plan, get feedback on your content and connect with founders at a similar stage.',
    stepOne: 'Click “Open Discord” to join our private server.',
    stepTwo: 'Introduce yourself and share your business so we can help you prioritise your next steps.',
    discordTitle: 'Join our Discord server',
    discordText: 'Get quick answers, content inspiration and access to exclusive sessions.',
    openDiscord: 'Open Discord',
    loading: 'Loading invite link…',
    error: 'Failed to load invite link.',
  }
})

const fetchDiscordInvite = async () => {
  try {
    isLoading.value = true
    const response = await axios.get('/api/discord/invite')
    discordInviteUrl.value = response.data.url
  } catch (error) {
    console.error('Failed to load Discord invite:', error)
    discordInviteUrl.value = null
  } finally {
    isLoading.value = false
  }
}

const goToDiscord = () => {
  if (!discordInviteUrl.value) {
    return
  }

  window.open(discordInviteUrl.value, '_blank')
}

onMounted(() => {
  fetchDiscordInvite()
})
</script>

