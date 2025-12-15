import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useLanguageStore = defineStore('language', () => {
  const STORAGE_KEY = 'peerie_language'

  const getInitialLanguage = () => {
    try {
      if (typeof window === 'undefined') return 'en'
      const saved = window.localStorage.getItem(STORAGE_KEY)
      if (saved === 'de' || saved === 'en') return saved
    } catch (error) {
      return 'en'
    }

    return 'en'
  }

  const applyToDocument = (value) => {
    if (typeof document === 'undefined') return
    if (!document.documentElement) return
    document.documentElement.lang = value
  }

  const language = ref(getInitialLanguage())
  applyToDocument(language.value)

  const setLanguage = (value) => {
    if (value === 'de' || value === 'en') {
      language.value = value
      applyToDocument(value)
      try {
        window.localStorage.setItem(STORAGE_KEY, value)
      } catch (error) {
        return
      }
    }
  }

  const toggleLanguage = () => {
    setLanguage(language.value === 'de' ? 'en' : 'de')
  }

  return {
    language,
    setLanguage,
    toggleLanguage
  }
})


