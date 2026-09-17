<template>
  <div class="max-w-[1440px] mx-auto">
    <div class="mb-6 md:mb-8">
      <h1 class="text-[32px] md:text-[40px] font-bold tracking-[-2px] text-black leading-none">{{ texts.headerTitle }}</h1>
      <p class="mt-3 text-[16px] text-purple/70 tracking-[-0.8px] max-w-[720px] leading-snug">
        {{ texts.intro }}
      </p>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-16">
      <svg class="w-12 h-12 text-red animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
      </svg>
    </div>

    <div v-else-if="error" class="bg-white rounded-[30px] shadow-card p-8">
      <p class="text-red">{{ error }}</p>
    </div>

    <div v-else-if="resources.length" class="bg-white rounded-[30px] shadow-card p-5 md:p-8">
      <div class="space-y-3">
        <div
          v-for="item in resources"
          :key="item.id"
          class="flex items-center justify-between gap-4 bg-light-grey rounded-[20px] border-4 border-rose px-4 py-3"
        >
          <div class="min-w-0">
            <div class="text-[18px] font-bold tracking-[-0.9px] text-black truncate">{{ item.title }}</div>
          </div>
          <button
            type="button"
            class="shrink-0 h-[42px] px-5 rounded-[20px] bg-red shadow-red text-[16px] font-bold tracking-[-0.8px] text-white hover:bg-red-dark disabled:opacity-60 flex items-center gap-2"
            :disabled="downloadingId === item.id"
            @click="downloadFile(item)"
          >
            <svg v-if="downloadingId !== item.id" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <svg v-else class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
            </svg>
            {{ downloadingId === item.id ? texts.downloading : texts.download }}
          </button>
        </div>
      </div>
    </div>

    <div v-else class="bg-white rounded-[30px] shadow-card p-8">
      <p class="text-purple/70">{{ texts.noResource }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLanguageStore } from '@/stores/language'
import axios from 'axios'

const languageStore = useLanguageStore()

const loading = ref(true)
const error = ref(null)
const resources = ref([])
const downloadingId = ref(null)

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      headerTitle: 'Guides & Checklisten',
      intro: 'Dateien und Checklisten zum Download – deine ausfüllbaren Dokumente findest du unter Dokumente.',
      download: 'Datei herunterladen',
      downloading: 'Wird heruntergeladen...',
      loading: 'Lädt...',
      noResource: 'Keine Guides verfügbar.'
    }
  }

  return {
    headerTitle: 'Guides & Checklists',
    intro: 'Downloadable files and checklists — your fill-in documents now live in Documents.',
    download: 'Download File',
    downloading: 'Downloading...',
    loading: 'Loading...',
    noResource: 'No guides available.'
  }
})

const fetchResource = async () => {
  try {
    loading.value = true
    error.value = null
    const resourcesResponse = await axios.get('/api/resources')

    if (resourcesResponse.data.success) {
      const list = resourcesResponse.data.resources || (resourcesResponse.data.resource ? [resourcesResponse.data.resource] : [])
      resources.value = list.filter((item) => item.kind === 'file')
    } else {
      error.value = resourcesResponse.data.message || 'Failed to load resource'
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

const getFilenameFromContentDisposition = (header) => {
  if (!header || typeof header !== 'string') return null

  const filenameStarMatch = header.match(/filename\*\s*=\s*([^;]+)/i)
  if (filenameStarMatch?.[1]) {
    let value = filenameStarMatch[1].trim()
    value = value.replace(/^"(.*)"$/, '$1').trim()
    const utf8Prefix = "UTF-8''"
    if (value.toUpperCase().startsWith(utf8Prefix)) {
      value = value.slice(utf8Prefix.length)
    }
    try {
      const decoded = decodeURIComponent(value)
      const cleaned = decoded.replace(/[\u0000-\u001F\u007F]/g, '').trim().replace(/[ .\t]+$/, '')
      return cleaned || null
    } catch {
      const cleaned = value.replace(/[\u0000-\u001F\u007F]/g, '').trim().replace(/[ .\t]+$/, '')
      return cleaned || null
    }
  }

  const filenameMatch = header.match(/filename\s*=\s*([^;]+)/i)
  if (filenameMatch?.[1]) {
    let value = filenameMatch[1].trim()
    value = value.replace(/^"(.*)"$/, '$1')
    const cleaned = value.replace(/[\u0000-\u001F\u007F]/g, '').trim().replace(/[ .\t]+$/, '')
    return cleaned || null
  }

  return null
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
    
    const contentDisposition = response.headers?.['content-disposition'] || response.headers?.['Content-Disposition']
    const filenameFromHeader = getFilenameFromContentDisposition(contentDisposition)
    const filename = filenameFromHeader || item?.filename || 'resource'
    
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
