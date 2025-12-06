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
      <p class="text-gray-600 dark:text-gray-400 mt-2">{{ texts.subtitle }}</p>
    </div>

    <!-- Search Controls -->
    <div class="bg-[#3F4369] rounded-[30px] p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Search Input -->
          <div>
            <label class="block text-sm font-medium text-white mb-2">
            {{ texts.searchLabel }}
          </label>
          <div class="relative">
            <input 
              v-model="searchQuery"
              @keyup.enter="searchImages"
              type="text" 
              :placeholder="texts.searchPlaceholder"
              class="w-full px-4 py-2 pl-10 border border-white rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-white text-black"
            >
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
        </div>

      </div>

      <!-- Quick Categories -->
      <div class="mt-4">
        <label class="block text-sm font-medium text-white mb-2">
          {{ texts.categoriesLabel }}
        </label>
        <div class="flex flex-wrap gap-2">
          <button 
            v-for="category in categories"
            :key="category"
            @click="searchByCategory(category)"
            class="px-3 py-1 text-sm bg-white text-black rounded-full hover:bg-gray-200 transition-colors"
          >
            {{ category }}
          </button>
        </div>
      </div>

      <!-- Search Button -->
      <div class="mt-4 flex justify-end">
        <button 
          @click="searchImages"
          :disabled="isLoading || !searchQuery.trim()"
          class="px-6 py-2 bg-[#F34767] text-white rounded-lg hover:bg-[#F34767] focus:ring-2 focus:ring-[#F34767] focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ isLoading ? texts.searching : texts.searchButton }}
        </button>
      </div>
    </div>

    <!-- Results -->
    <div v-if="images.length > 0" class="mb-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div 
          v-for="image in images" 
          :key="image.id"
          class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
        >
          <div class="relative group">
            <img 
              :src="image.previewUrl" 
              :alt="image.alt || 'Image'"
              class="w-full h-48 object-cover cursor-pointer hover:opacity-90 transition-opacity"
              @load="image.loaded = true"
              @error="image.error = true"
              @click="viewImage(image)"
            >
            
            <!-- Loading overlay -->
            <div v-if="!image.loaded && !image.error" class="absolute inset-0 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
              <svg class="w-8 h-8 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>

            <!-- Error overlay -->
            <div v-if="image.error" class="absolute inset-0 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
              <div class="text-center text-gray-500">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <p class="text-sm">{{ texts.loadingError }}</p>
              </div>
            </div>

            <!-- Action buttons overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
              <div class="flex space-x-2">
                <button 
                  @click="downloadImage(image)"
                  :disabled="downloadingImages.has(image.id || `${image.title}_${Date.now()}`)"
                  class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="!downloadingImages.has(image.id || `${image.title}_${Date.now()}`)" class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <svg v-else class="w-4 h-4 mr-1 inline animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ downloadingImages.has(image.id || `${image.title}_${Date.now()}`) ? texts.downloading : texts.download }}
                </button>
                <button 
                  @click="viewImage(image)"
                  class="px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
                >
                  <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  View
                </button>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Load More Button / Indicator -->
      <div class="flex justify-center py-8">
        <button
          v-if="hasMoreImages && !isLoadingMore"
          @click="loadMoreImages"
          class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Load more images
        </button>

        <div v-else-if="isLoadingMore" class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#F34767]"></div>
          <span>Loading more images...</span>
        </div>
      </div>

    </div>

    <!-- No Results -->
    <div v-else-if="hasSearched && !isLoading" class="text-center py-12">
      <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
      </svg>
      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ texts.noImagesTitle }}</h3>
      <p class="text-gray-600 dark:text-gray-400">{{ texts.noImagesText }}</p>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-12">
      <svg class="w-16 h-16 text-blue-600 mx-auto mb-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <p class="text-gray-600 dark:text-gray-400">{{ texts.searchingFull }}</p>
    </div>

    <!-- Image Modal -->
    <div v-if="selectedImage" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" @click="selectedImage = null">
      <div class="relative max-w-4xl max-h-full p-4" @click.stop>
        <button 
          @click="selectedImage = null"
          class="absolute top-2 right-2 z-10 p-2 bg-black bg-opacity-50 text-white rounded-full hover:bg-opacity-75"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
        <img 
          :src="selectedImage.fullUrl" 
          :alt="selectedImage.alt"
          class="max-w-full max-h-full rounded-lg"
        >
        <div class="absolute bottom-4 left-4 right-4 bg-black bg-opacity-50 text-white p-4 rounded-lg">
          <div class="flex justify-between items-center">
            <button 
              @click="downloadImage(selectedImage)"
              :disabled="downloadingImages.has(selectedImage.id || `${selectedImage.title}_${Date.now()}`)"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="!downloadingImages.has(selectedImage.id || `${selectedImage.title}_${Date.now()}`)" class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <svg v-else class="w-4 h-4 mr-1 inline animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ downloadingImages.has(selectedImage.id || `${selectedImage.title}_${Date.now()}`) ? 'Downloading...' : 'Download' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'
import logoWhite from '@/assets/images/logos/logo-white.svg'

const languageStore = useLanguageStore()

const searchQuery = ref('')
const isLoading = ref(false)
const isLoadingMore = ref(false)
const hasSearched = ref(false)
const images = ref([])
const selectedImage = ref(null)
const downloadingImages = ref(new Set())
const currentPage = ref(1)
const hasMoreImages = ref(true)
const currentSearchQuery = ref('')

const categories = [
  'Business', 'Coach', 'Therapy', 'Beauty', 'Style', 'Nails', 'Sports', 'Massage', 'Doctor'
]

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      headerTitle: 'Bildbibliothek',
      headerSection: 'Erstellen',
      headerCurrent: 'Bildbibliothek',
      title: 'Bildbibliothek',
      subtitle: 'Finde und lade Bilder für dein Marketing herunter',
      searchLabel: 'Bilder suchen',
      searchPlaceholder: 'Suchbegriffe eingeben...',
      categoriesLabel: 'Schnellsuche nach Kategorien',
      searchButton: 'Bilder finden',
      searching: 'Suche...',
      loadingError: 'Ladefehler',
      download: 'Herunterladen',
      downloading: 'Wird heruntergeladen...',
      noImagesTitle: 'Keine Bilder gefunden',
      noImagesText: 'Ändere deine Suchanfrage oder wähle eine andere Kategorie',
      searchingFull: 'Suche nach Bildern...'
    }
  }

  return {
    headerTitle: 'Image Library',
    headerSection: 'Create',
    headerCurrent: 'Image Library',
    title: 'Image Library',
    subtitle: 'Find and download images for your marketing materials',
    searchLabel: 'Search Images',
    searchPlaceholder: 'Enter keywords...',
    categoriesLabel: 'Quick search by categories',
    searchButton: 'Find Images',
    searching: 'Searching...',
    loadingError: 'Loading error',
    download: 'Download',
    downloading: 'Downloading...',
    noImagesTitle: 'No images found',
    noImagesText: 'Try changing your search query or selecting a different category',
    searchingFull: 'Searching images...'
  }
})

const industryToCategoryMap = {
  'beauty': 'Beauty',
  'physio': 'Massage',
  'coaching': 'Therapy'
}

const searchImages = async () => {
  if (!searchQuery.value.trim()) return

  isLoading.value = true
  hasSearched.value = true
  images.value = []
  currentPage.value = 1
  hasMoreImages.value = true
  currentSearchQuery.value = searchQuery.value

  try {
    const response = await axios.post('/api/images/search', {
      query: searchQuery.value,
      page: 1,
      per_page: 16
    })
    
    if (response.data.success) {
      images.value = response.data.data.images
      hasMoreImages.value = response.data.data.hasNextPage && response.data.data.images.length >= 16
    } else {
      console.error('API Error:', response.data.message)
    }
  } catch (error) {
    console.error('Search error:', error)
    images.value = generateMockImages(searchQuery.value, 'pexels')
  } finally {
    isLoading.value = false
  }
}

const searchByCategory = async (category, setQuery = true) => {
  isLoading.value = true
  hasSearched.value = true
  images.value = []
  currentPage.value = 1
  hasMoreImages.value = true
  currentSearchQuery.value = category
  if (setQuery) {
    searchQuery.value = category
  }

  try {
    const response = await axios.post('/api/images/search/category', {
      category: category,
      page: 1,
      per_page: 16
    })
    
    console.log('Category search response:', {
      success: response.data.success,
      data: response.data.data,
      message: response.data.message,
      category: category
    })
    
    if (response.data.success && response.data.data) {
      images.value = response.data.data.images || []
      hasMoreImages.value = response.data.data.hasNextPage && images.value.length >= 16
      
      if (images.value.length === 0) {
        console.log('No images found for category:', category)
      }
    } else {
      console.error('API Error:', response.data.message || 'Unknown error', response.data)
      images.value = []
    }
  } catch (error) {
    console.error('Category search error:', error)
    if (error.response) {
      console.error('Error response:', error.response.data)
    }
    images.value = []
  } finally {
    isLoading.value = false
  }
}

const generateMockImages = (query, provider) => {
  const mockImages = []
  const providers = {
    pixabay: 'Pixabay',
    pexels: 'Pexels', 
    unsplash: 'Unsplash'
  }

  for (let i = 1; i <= 12; i++) {
    const width = Math.floor(Math.random() * 800) + 400
    const height = Math.floor(Math.random() * 600) + 300
    
    mockImages.push({
      id: `${provider}-${i}`,
      title: `${query} - image ${i}`,
      alt: `${query} image`,
      provider: providers[provider],
      width,
      height,
      previewUrl: `https://picsum.photos/400/300?random=${i}`,
      fullUrl: `https://picsum.photos/${width}/${height}?random=${i}`,
      downloadUrl: `https://picsum.photos/${width}/${height}?random=${i}`,
      loaded: false,
      error: false
    })
  }

  return mockImages
}

const downloadImage = async (image) => {
  const imageId = image.id || `${image.title}_${Date.now()}`
  
  try {
    console.log('Downloading image:', image)
    
    downloadingImages.value.add(imageId)
    
    const downloadUrl = image.downloadUrl || image.fullUrl || image.previewUrl
    const filename = `${image.title || 'image'}_${Date.now()}.jpg`
    
    if (!downloadUrl) {
      throw new Error('No download URL available')
    }
    
    try {
      const response = await axios.post('/api/images/download-proxy', {
        url: downloadUrl,
        filename: filename
      }, {
        responseType: 'blob'
      })
      
      const blob = new Blob([response.data])
      const url = window.URL.createObjectURL(blob)
      
      const link = document.createElement('a')
      link.href = url
      link.download = filename
      link.style.display = 'none'
      
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      
      window.URL.revokeObjectURL(url)
      
      console.log('Download completed successfully')
      
    } catch (proxyError) {
      console.log('Proxy download failed, falling back to direct method:', proxyError)
      
      const link = document.createElement('a')
      link.href = downloadUrl
      link.download = filename
      link.target = '_blank'
      link.rel = 'noopener noreferrer'
      link.style.display = 'none'
      
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      
      console.log('Direct download started:', downloadUrl)
    }
    
  } catch (error) {
    console.error('Download error:', error)
    alert('Error downloading image. Please try again.')
  } finally {
    downloadingImages.value.delete(imageId)
  }
}

const viewImage = (image) => {
  console.log('Viewing image:', image)
  selectedImage.value = image
}

const loadMoreImages = async () => {
  if (isLoadingMore.value || !hasMoreImages.value) return

  isLoadingMore.value = true
  currentPage.value++

  try {
    let response
    if (categories.includes(currentSearchQuery.value)) {
      response = await axios.post('/api/images/search/category', {
        category: currentSearchQuery.value,
        page: currentPage.value,
        per_page: 16
      })
    } else {
      response = await axios.post('/api/images/search', {
        query: currentSearchQuery.value,
        page: currentPage.value,
        per_page: 16
      })
    }
    
    if (response.data.success) {
      images.value = [...images.value, ...response.data.data.images]
      hasMoreImages.value = response.data.data.hasNextPage && response.data.data.images.length >= 16
    }
  } catch (error) {
    console.error('Load more error:', error)
    currentPage.value-- // Revert page increment on error
  } finally {
    isLoadingMore.value = false
  }
}

const getUserIndustry = async () => {
  try {
    const response = await axios.get('/api/plans')
    
    if (response.data.success && response.data.plans && response.data.plans.length > 0) {
      const latestPlan = response.data.plans[0]
      if (latestPlan.industries && Array.isArray(latestPlan.industries) && latestPlan.industries.length > 0) {
        const industry = latestPlan.industries[0]
        const mappedCategory = industryToCategoryMap[industry]
        if (mappedCategory && categories.includes(mappedCategory)) {
          return mappedCategory
        }
      }
    }
  } catch (error) {
    console.error('Error fetching user industry:', error)
  }
  return null
}

onMounted(async () => {
  const userIndustry = await getUserIndustry()
  if (userIndustry) {
    await searchByCategory(userIndustry, false)
  } else {
    hasSearched.value = false
    isLoading.value = false
  }
})
</script>
