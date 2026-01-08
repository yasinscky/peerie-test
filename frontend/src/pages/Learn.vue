<template>
  <div class="max-w-7xl mx-auto">
    <div class="bg-[#f34767] pt-[19px] pb-[19px] lg:h-28 lg:pt-0 lg:pb-0 px-4 lg:px-8 flex items-center justify-between rounded-[20px] lg:rounded-40 mb-8">
      <div class="flex items-center space-x-4">
        <div class="w-10 h-10 rounded-lg bg-opacity-20 hidden md:flex items-center justify-center">
          <img :src="logoWhite" alt="Peerie Logo" class="w-10 h-10">
        </div>
        <h1 class="text-white text-2xl lg:text-3xl font-bold">{{ texts.headerTitle }}</h1>
      </div>
    </div>

    <div class="mb-8">
      <p class="text-[#3F4369] opacity-70 leading-relaxed max-w-5xl">
        {{ texts.intro }}
      </p>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="text-[#3F4369]">{{ texts.loading }}</div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-2xl p-6">
      <p class="text-red-800">{{ error }}</p>
    </div>

    <div v-else-if="resources.length" class="bg-white rounded-2xl shadow-lg border border-[#DCDCDC] p-6">
      <div class="space-y-4">
        <div
          v-for="item in resources"
          :key="item.id"
          class="flex items-center justify-between gap-4 p-4 border border-[#DCDCDC] rounded-2xl"
        >
          <div class="min-w-0">
            <div class="text-[#3F4369] font-semibold truncate">{{ item.title }}</div>
          </div>
          <button
            @click="downloadFile(item)"
            :disabled="downloadingId === item.id"
            class="shrink-0 px-4 py-2 bg-[#f34767] text-white font-semibold rounded-xl hover:bg-[#e63950] transition-colors disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2"
          >
            <svg v-if="downloadingId !== item.id" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <svg v-else class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ downloadingId === item.id ? texts.downloading : texts.download }}
          </button>
        </div>
      </div>
    </div>

    <div v-else class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
      <p class="text-[#3F4369] opacity-70">{{ texts.noResource }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLanguageStore } from '@/stores/language'
import axios from 'axios'
import logoWhite from '@/assets/images/logos/logo-white.svg'

const languageStore = useLanguageStore()

const loading = ref(true)
const error = ref(null)
const resources = ref([])
const downloadingId = ref(null)

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      headerTitle: 'Ressourcen',
      intro: 'Verwenden Sie die Vorlagen auf dieser Seite, um wichtige Teile Ihres Marketings schnell zu dokumentieren und zu organisieren – von Zielen und idealen Kunden (Personas) bis hin zur Wettbewerbsanalyse und mehr – ohne bei Null anfangen zu müssen. Alles hier ist auf Ihren Marketingplan abgestimmt, und wenn eine Vorlage relevant ist, weisen wir in den Aufgaben Ihres Plans darauf hin, damit Sie wissen, welche Sie als Nächstes verwenden sollten. Um loszulegen, laden Sie einfach eine Vorlage auf Ihren Computer herunter und füllen Sie sie in Ihrem eigenen Tempo aus.',
      download: 'Datei herunterladen',
      downloading: 'Wird heruntergeladen...',
      loading: 'Lädt...',
      noResource: 'Keine Ressource verfügbar.'
    }
  }

  return {
    headerTitle: 'Resources',
    intro: "Use the templates on this page to quickly document and organise key parts of your marketing — from goals and ideal customers (personas) to competitor analysis and more — without starting from scratch. Everything here is aligned with your marketing plan, and when a template is relevant, we’ll call it out in the tasks in your plan so you know what to use next. To get started, simply download a template to your computer and fill it in at your own pace.",
    download: 'Download File',
    downloading: 'Downloading...',
    loading: 'Loading...',
    noResource: 'No resource available.'
  }
})

const fetchResource = async () => {
  try {
    loading.value = true
    error.value = null
    const response = await axios.get('/api/resources')
    
    if (response.data.success) {
      resources.value = response.data.resources || (response.data.resource ? [response.data.resource] : [])
    } else {
      error.value = response.data.message || 'Failed to load resource'
    }
  } catch (err) {
    if (err.response?.status === 404) {
      resources.value = []
    } else {
      error.value = err.response?.data?.message || 'An error occurred while loading the resource'
    }
  } finally {
    loading.value = false
  }
}

const downloadFile = async (item) => {
  try {
    downloadingId.value = item.id
    const response = await axios.get(`/api/resources/download/${item.id}`, {
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    
    const contentDisposition = response.headers['content-disposition']
    let filename = item?.filename || 'resource'
    if (contentDisposition) {
      const filenameMatch = contentDisposition.match(/filename="?(.+)"?/i)
      if (filenameMatch) {
        filename = filenameMatch[1]
      }
    }
    
    link.setAttribute('download', filename)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (err) {
    error.value = err.response?.data?.message || 'An error occurred while downloading the file'
  } finally {
    downloadingId.value = null
  }
}

onMounted(() => {
  fetchResource()
})
</script>
