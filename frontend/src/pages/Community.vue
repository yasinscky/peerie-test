<template>
  <div class="max-w-[1440px] mx-auto">
    <div class="mb-6 md:mb-8">
      <p class="text-[12px] font-bold uppercase tracking-[-0.6px] text-purple/70 mb-2">{{ texts.headerSection }}</p>
      <h1 class="text-[32px] md:text-[40px] font-bold tracking-[-2px] text-black leading-none">{{ texts.headerTitle }}</h1>
      <p class="mt-3 text-[16px] text-purple/70 tracking-[-0.8px] max-w-[720px]">
        {{ texts.subtitle }}
      </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_360px] gap-5">
      <section class="bg-white rounded-[30px] shadow-card p-5 md:p-8">
        <h2 class="text-[24px] md:text-[32px] font-bold tracking-[-1.6px] text-black leading-none mb-3">
          {{ texts.mainTitle }}
        </h2>
        <p class="text-[16px] text-purple/70 tracking-[-0.8px] mb-6">
          {{ texts.mainText }}
        </p>

        <div class="space-y-3">
          <div class="bg-light-grey rounded-[20px] border-4 border-rose px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-[12px] bg-yellow flex items-center justify-center shrink-0 text-[14px] font-bold text-black">
              1
            </div>
            <p class="text-[16px] text-purple tracking-[-0.8px]">
              {{ texts.stepOne }}
            </p>
          </div>
          <div class="bg-light-grey rounded-[20px] border-4 border-rose px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-[12px] bg-yellow flex items-center justify-center shrink-0 text-[14px] font-bold text-black">
              2
            </div>
            <p class="text-[16px] text-purple tracking-[-0.8px]">
              {{ texts.stepTwo }}
            </p>
          </div>
        </div>
      </section>

      <section class="bg-white rounded-[30px] shadow-card p-5 md:p-8 flex flex-col justify-between min-h-[220px]">
        <div>
          <p class="text-[12px] font-bold uppercase tracking-[-0.6px] text-purple/70 mb-2">
            Discord
          </p>
          <h2 class="text-[24px] font-bold tracking-[-1.2px] text-black mb-2">
            {{ texts.discordTitle }}
          </h2>
          <p class="text-[16px] text-purple/70 tracking-[-0.8px] mb-6">
            {{ texts.discordText }}
          </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
          <button
            type="button"
            class="h-[42px] px-5 rounded-[20px] bg-red shadow-red text-[16px] font-bold tracking-[-0.8px] text-white hover:bg-red-dark disabled:opacity-60"
            :disabled="!discordInviteUrl || isLoading"
            @click="goToDiscord"
          >
            {{ texts.openDiscord }}
          </button>
          <span
            v-if="isLoading"
            class="text-[13px] text-purple/70"
          >
            {{ texts.loading }}
          </span>
          <span
            v-else-if="!discordInviteUrl"
            class="text-[13px] text-red"
          >
            {{ texts.error }}
          </span>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'

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

