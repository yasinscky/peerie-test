<template>
  <div>
    <div
      v-if="isModalVisible"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
    >
      <div class="relative max-w-xl w-full mx-4 bg-white rounded-3xl shadow-2xl p-8">
        <button
          type="button"
          class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 transition-colors"
          @click="closeModal"
        >
          <span class="text-lg leading-none">×</span>
        </button>

        <h2 class="text-2xl font-bold text-[#3F4369] mb-4">
          {{ texts.title }}
        </h2>

        <div class="space-y-3 text-sm text-[#3F4369] opacity-90">
          <p v-for="(paragraph, index) in texts.paragraphs" :key="index">
            {{ paragraph }}
          </p>
        </div>
      </div>
    </div>

    <button
      v-if="isIconVisible"
      type="button"
      class="fixed bottom-4 right-4 z-40 w-12 h-12 rounded-full bg-[#f34767] text-white flex items-center justify-center shadow-lg hover:bg-[#d93b57] transition-colors"
      @click="openModalFromIcon"
    >
      <span class="text-xl font-bold">i</span>
    </button>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLanguageStore } from '@/stores/language'

const languageStore = useLanguageStore()

const isModalVisible = ref(false)
const isIconVisible = ref(false)

const storageKey = 'peerie_test_env_notice_dismissed'

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      title: 'Willkommen in deinem Peerie-Testbereich!',
      paragraphs: [
        'Dies ist eine Testumgebung – Design und Layout sind noch nicht final.',
        'Am wichtigsten ist jetzt, dass du deinen Marketingplan wirklich benutzt: klicke dich durch die Schritte, probiere die Aufgaben aus und schaue, wie gut sie in deine Woche passen.',
        'Während der Testphase werden wir nach und nach neue Funktionen hinzufügen und Dinge verbessern.',
        'Wenn dir ein Fehler auffällt, etwas unklar ist oder du Hilfe brauchst, melde dich gern in der Peerie Community auf Discord.',
        'Danke, dass du uns hilfst, Peerie noch besser zu machen!'
      ]
    }
  }

  return {
    title: 'Welcome to your Peerie test space!',
    paragraphs: [
      'This is an early test environment, so the design and layout are not final yet.',
      'For now, the most important thing is that you use your marketing plan: click through the steps, try the tasks, and see how it fits into your real week.',
      'We’ll keep adding and tweaking features throughout the test period, so you might notice new things popping up as you go.',
      'If you spot a bug, something feels confusing, or you just need a hand, you can get help and share feedback in the Peerie Community on Discord.',
      'Thanks for helping us make Peerie better!'
    ]
  }
})

const closeModal = () => {
  isModalVisible.value = false
  isIconVisible.value = true
  try {
    window.localStorage.setItem(storageKey, '1')
  } catch (e) {}
}

const openModalFromIcon = () => {
  isModalVisible.value = true
  isIconVisible.value = false
}

onMounted(() => {
  let dismissed = false
  try {
    dismissed = window.localStorage.getItem(storageKey) === '1'
  } catch (e) {}

  if (dismissed) {
    isModalVisible.value = false
    isIconVisible.value = true
  } else {
    isModalVisible.value = true
    isIconVisible.value = false
  }
})
</script>
