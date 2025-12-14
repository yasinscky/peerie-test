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

    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="text-[#3F4369]">{{ texts.loading }}</div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-2xl p-6">
      <p class="text-red-800">{{ error }}</p>
    </div>

    <div v-else-if="resource" class="bg-white rounded-2xl shadow-lg border border-[#DCDCDC] p-6">
      <h2 class="text-2xl font-bold text-[#3F4369] mb-4">
        {{ resource.title }}
      </h2>
      <p class="text-[#3F4369] opacity-70 mb-6">
        {{ texts.description }}
      </p>
      <button
        @click="downloadFile"
        :disabled="downloading"
        class="px-6 py-3 bg-[#f34767] text-white font-semibold rounded-xl hover:bg-[#e63950] transition-colors disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2"
      >
        <svg v-if="!downloading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
        </svg>
        <svg v-else class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ downloading ? texts.downloading : texts.download }}
      </button>
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
const resource = ref(null)
const downloading = ref(false)

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      headerTitle: 'Ressourcen',
      description: 'Lade dir unsere Content Bank Vorlage herunter, um deine Marketing-Inhalte zu erstellen.',
      download: 'Datei herunterladen',
      downloading: 'Wird heruntergeladen...',
      loading: 'Lädt...',
      noResource: 'Keine Ressource verfügbar.'
    }
  }

  return {
    headerTitle: 'Resources',
    description: 'Download our Content Bank Template to create your marketing content.',
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
      resource.value = response.data.resource
    } else {
      error.value = response.data.message || 'Failed to load resource'
    }
  } catch (err) {
    if (err.response?.status === 404) {
      resource.value = null
    } else {
      error.value = err.response?.data?.message || 'An error occurred while loading the resource'
    }
  } finally {
    loading.value = false
  }
}

const downloadFile = async () => {
  try {
    downloading.value = true
    const response = await axios.get('/api/resources/download', {
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    
    const contentDisposition = response.headers['content-disposition']
    let filename = 'resource.docx'
    if (contentDisposition) {
      const filenameMatch = contentDisposition.match(/filename="?(.+)"?/i)
      if (filenameMatch) {
        filename = filenameMatch[1]
      }
    } else if (resource.value?.filename) {
      filename = resource.value.filename
    }
    
    link.setAttribute('download', filename)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (err) {
    error.value = err.response?.data?.message || 'An error occurred while downloading the file'
  } finally {
    downloading.value = false
  }
}

onMounted(() => {
  fetchResource()
})
</script>
