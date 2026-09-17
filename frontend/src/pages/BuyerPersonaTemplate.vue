<template>
  <div class="max-w-[1440px] mx-auto pb-10">
    <button
      type="button"
      class="inline-flex items-center gap-2 text-[14px] tracking-[-0.7px] text-purple hover:text-red mb-5"
      @click="goBack"
    >
      <span class="text-[18px] leading-none">←</span>
      {{ texts.back }}
    </button>

    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
      <div class="min-w-0">
        <p class="text-[12px] font-bold uppercase tracking-[-0.6px] text-purple/70 mb-2">{{ texts.section }}</p>
        <h1 class="text-[32px] md:text-[40px] font-bold tracking-[-2px] text-black leading-none">
          {{ texts.headerTitle }}
        </h1>
        <p class="mt-3 text-[16px] text-purple/70 tracking-[-0.8px] max-w-[720px]">
          {{ texts.intro }}
        </p>
      </div>
      <div class="flex flex-wrap items-center gap-3 shrink-0">
        <span class="text-[13px] tracking-[-0.6px]" :class="saveStatusClass">{{ saveStatusLabel }}</span>
        <button
          type="button"
          class="h-[42px] px-5 rounded-[20px] bg-light-grey border-4 border-rose text-[16px] font-bold tracking-[-0.8px] text-black hover:bg-rose disabled:opacity-50"
          :disabled="isExporting !== null"
          @click="downloadExport('docx')"
        >
          {{ isExporting === 'docx' ? texts.downloading : texts.downloadWord }}
        </button>
        <button
          type="button"
          class="h-[42px] px-5 rounded-[20px] bg-red shadow-red text-[16px] font-bold tracking-[-0.8px] text-white hover:bg-red-dark disabled:opacity-50"
          :disabled="isExporting !== null"
          @click="downloadExport('pdf')"
        >
          {{ isExporting === 'pdf' ? texts.downloading : texts.downloadPdf }}
        </button>
      </div>
    </div>

    <div v-if="isLoading" class="text-center py-16">
      <svg class="w-12 h-12 text-red mx-auto mb-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
      </svg>
    </div>

    <div v-else-if="error" class="bg-white rounded-[30px] shadow-card p-8">
      <p class="text-red">{{ error }}</p>
    </div>

    <div v-else>
      <div
        v-if="planTaskId"
        class="mb-5 bg-white rounded-[30px] shadow-card px-5 py-4 flex flex-wrap items-center justify-between gap-3"
      >
        <p class="text-[14px] text-purple tracking-[-0.7px]">
          {{ texts.linkedTask }}
        </p>
        <router-link
          :to="{ name: 'TaskDetail', params: { planTaskId }, query: { from: 'learn' } }"
          class="text-[14px] font-bold tracking-[-0.7px] text-red hover:text-red-dark"
        >
          {{ texts.openTask }}
        </router-link>
      </div>
      <div
        v-else
        class="mb-5 bg-white rounded-[30px] shadow-card px-5 py-4"
      >
        <p class="text-[14px] text-purple tracking-[-0.7px]">
          {{ texts.missingTask }}
        </p>
      </div>

      <section
        v-for="stepNumber in 6"
        :key="`persona-${stepNumber}`"
        class="bg-white rounded-[30px] shadow-card p-5 md:p-8 mb-5"
      >
        <h2 class="text-[20px] md:text-[24px] font-bold tracking-[-1.2px] text-black mb-5">
          {{ personaCopy.stepTitles[stepNumber] }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="field in fieldsForStep(stepNumber)"
            :key="field.key"
            :class="field.type === 'textarea' ? 'md:col-span-2' : ''"
          >
            <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-2">
              {{ personaCopy.labels[field.key] }}
            </label>
            <textarea
              v-if="field.type === 'textarea'"
              v-model="personaFields[field.key]"
              :rows="field.rows || 3"
              :placeholder="personaCopy.placeholders[field.key]"
              class="w-full rounded-[16px] border border-grey bg-light-grey px-4 py-3 text-[16px] text-black tracking-[-0.8px] outline-none focus:border-red resize-y"
            />
            <input
              v-else
              v-model="personaFields[field.key]"
              type="text"
              :placeholder="personaCopy.placeholders[field.key]"
              class="w-full rounded-[16px] border border-grey bg-light-grey px-4 py-3 text-[16px] text-black tracking-[-0.8px] outline-none focus:border-red"
            >
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, reactive, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'
import {
  BUYER_PERSONA_FIELDS,
  emptyBuyerPersonaFields,
  getBuyerPersonaCopy,
} from '@/data/buyerPersonaWorksheet'

const router = useRouter()
const languageStore = useLanguageStore()

const isLoading = ref(true)
const error = ref('')
const personaFields = reactive(emptyBuyerPersonaFields())
const planTaskId = ref(null)
const isHydrated = ref(false)
const isSaving = ref(false)
const saveState = ref('idle')
const isExporting = ref(null)
let saveTimer = null

const personaCopy = computed(() => getBuyerPersonaCopy(languageStore.language))

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      section: 'Ressourcen',
      headerTitle: 'Buyer Persona Vorlage',
      intro: 'Hier siehst du alles, was du in der Aufgabe ausgefüllt hast. Du kannst die Vorlage direkt ergänzen und als PDF oder Word herunterladen.',
      back: 'Zurück zu Ressourcen',
      linkedTask: 'Daten kommen aus deiner Buyer-Persona-Aufgabe.',
      missingTask: 'Die Buyer-Persona-Aufgabe ist in deinem Plan noch nicht vorhanden. Sobald sie da ist, speichern sich die Angaben automatisch.',
      openTask: 'Aufgabe öffnen',
      downloadPdf: 'Als PDF',
      downloadWord: 'Als Word',
      downloading: 'Wird erstellt…',
      saving: 'Speichert…',
      saved: 'Gespeichert',
      saveError: 'Speichern fehlgeschlagen',
      loadError: 'Vorlage konnte nicht geladen werden',
    }
  }

  return {
    section: 'Resources',
    headerTitle: 'Buyer Persona Template',
    intro: 'This is the data you entered in the task. You can add more here and download the filled template as PDF or Word.',
    back: 'Back to resources',
    linkedTask: 'This data comes from your buyer persona task.',
    missingTask: 'The buyer persona task is not in your plan yet. Once it is, your answers will save here automatically.',
    openTask: 'Open task',
    downloadPdf: 'Download PDF',
    downloadWord: 'Download Word',
    downloading: 'Preparing…',
    saving: 'Saving…',
    saved: 'Saved',
    saveError: 'Could not save',
    loadError: 'Could not load the template',
  }
})

const saveStatusLabel = computed(() => {
  if (saveState.value === 'saving' || isSaving.value) return texts.value.saving
  if (saveState.value === 'saved') return texts.value.saved
  if (saveState.value === 'error') return texts.value.saveError
  return ''
})

const saveStatusClass = computed(() => {
  if (saveState.value === 'error') return 'text-red'
  if (saveState.value === 'saved') return 'text-green'
  return 'text-purple/70'
})

const fieldsForStep = (stepNumber) => BUYER_PERSONA_FIELDS.filter((field) => field.step === stepNumber)

const goBack = () => {
  router.push('/dashboard/learn')
}

const hydrateFields = (fields) => {
  Object.keys(personaFields).forEach((key) => {
    personaFields[key] = ''
  })
  if (!fields || typeof fields !== 'object') return
  Object.entries(fields).forEach(([key, value]) => {
    if (key in personaFields) {
      personaFields[key] = value || ''
    }
  })
}

const saveFields = async () => {
  if (!isHydrated.value || !planTaskId.value) return
  isSaving.value = true
  saveState.value = 'saving'
  try {
    await axios.put('/api/buyer-persona', {
      fields: { ...personaFields },
    })
    saveState.value = 'saved'
  } catch (saveError) {
    console.error('Failed to save buyer persona:', saveError)
    saveState.value = 'error'
  } finally {
    isSaving.value = false
  }
}

const scheduleSave = () => {
  if (!isHydrated.value || !planTaskId.value) return
  saveState.value = 'saving'
  if (saveTimer) clearTimeout(saveTimer)
  saveTimer = setTimeout(() => {
    saveFields()
  }, 700)
}

const downloadExport = async (format) => {
  try {
    isExporting.value = format
    if (isHydrated.value) {
      await saveFields()
    }
    const response = await axios.get('/api/buyer-persona/export', {
      params: { format },
      responseType: 'blob',
    })
    const extension = format === 'docx' ? 'docx' : 'pdf'
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `Buyer-Persona.${extension}`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (exportError) {
    console.error('Failed to export buyer persona:', exportError)
    error.value = texts.value.loadError
  } finally {
    isExporting.value = null
  }
}

const fetchTemplate = async () => {
  isLoading.value = true
  error.value = ''
  isHydrated.value = false
  try {
    const response = await axios.get('/api/buyer-persona')
    if (!response.data?.success) {
      error.value = texts.value.loadError
      return
    }
    hydrateFields(response.data.fields)
    planTaskId.value = response.data.plan_task_id || null
    await nextTick()
    isHydrated.value = true
    saveState.value = 'idle'
  } catch (fetchError) {
    console.error('Failed to load buyer persona:', fetchError)
    error.value = texts.value.loadError
  } finally {
    isLoading.value = false
  }
}

watch(personaFields, () => {
  scheduleSave()
}, { deep: true })

onMounted(fetchTemplate)

onUnmounted(() => {
  if (saveTimer) clearTimeout(saveTimer)
})
</script>
