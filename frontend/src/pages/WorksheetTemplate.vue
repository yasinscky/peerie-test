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
          {{ schema.title }}
        </h1>
        <p class="mt-3 text-[16px] text-purple/70 tracking-[-0.8px] max-w-[720px]">
          {{ schema.intro }}
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
        <p class="text-[14px] text-purple tracking-[-0.7px]">{{ texts.linkedTask }}</p>
        <router-link
          :to="{ name: 'TaskDetail', params: { planTaskId }, query: { from: 'documents', worksheet: slug } }"
          class="text-[14px] font-bold tracking-[-0.7px] text-red hover:text-red-dark"
        >
          {{ texts.openTask }}
        </router-link>
      </div>
      <div v-else class="mb-5 bg-white rounded-[30px] shadow-card px-5 py-4">
        <p class="text-[14px] text-purple tracking-[-0.7px]">{{ texts.missingTask }}</p>
      </div>

      <WorksheetFields
        :schema="schema"
        :document="document"
        :add-label="texts.add"
        :remove-label="texts.remove"
      />
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, reactive, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'
import WorksheetFields from '@/components/WorksheetFields.vue'
import {
  applyDocument,
  emptyWorksheetDocument,
  getWorksheetSchema,
  schemaFromApi,
  slugToKind,
} from '@/data/worksheets'

const route = useRoute()
const router = useRouter()
const languageStore = useLanguageStore()

const slug = computed(() => String(route.params.kind || ''))
const kind = computed(() => slugToKind(slug.value))
const apiSchema = ref(null)
const schema = computed(() => {
  const local = getWorksheetSchema(kind.value, languageStore.language)
  if ((local.fields || []).length || (local.categoryKeys || []).length || (local.rowKeys || []).length) {
    return local
  }
  return apiSchema.value || local
})

const isLoading = ref(true)
const error = ref('')
const planTaskId = ref(null)
const isHydrated = ref(false)
const isSaving = ref(false)
const saveState = ref('idle')
const isExporting = ref(null)
const document = reactive(emptyWorksheetDocument('buyer_persona'))
let saveTimer = null

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      section: 'Dokumente',
      back: 'Zurück zu Dokumenten',
      linkedTask: 'Daten kommen aus der passenden Aufgabe in deinem Plan.',
      missingTask: 'Die passende Aufgabe ist in deinem Plan noch nicht vorhanden. Sobald sie da ist, speichern sich die Angaben automatisch.',
      openTask: 'Aufgabe öffnen',
      downloadPdf: 'Als PDF',
      downloadWord: 'Als Word',
      downloading: 'Wird erstellt…',
      saving: 'Speichert…',
      saved: 'Gespeichert',
      saveError: 'Speichern fehlgeschlagen',
      loadError: 'Vorlage konnte nicht geladen werden',
      add: 'Zeile hinzufügen',
      remove: 'Löschen',
    }
  }

  return {
    section: 'Documents',
    back: 'Back to documents',
    linkedTask: 'This data comes from the matching task in your plan.',
    missingTask: 'The matching task is not in your plan yet. Once it is, your answers will save here automatically.',
    openTask: 'Open task',
    downloadPdf: 'Download PDF',
    downloadWord: 'Download Word',
    downloading: 'Preparing…',
    saving: 'Saving…',
    saved: 'Saved',
    saveError: 'Could not save',
    loadError: 'Could not load the template',
    add: 'Add row',
    remove: 'Remove',
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

const goBack = () => {
  router.push('/dashboard/documents')
}

const resetDocument = (nextKind) => {
  const empty = emptyWorksheetDocument(nextKind, schema.value)
  Object.keys(document).forEach((key) => {
    delete document[key]
  })
  Object.assign(document, empty)
  if (empty.fields) document.fields = reactive({ ...empty.fields })
  if (empty.rows) document.rows = reactive(empty.rows.map((row) => ({ ...row })))
  if (empty.categories) {
    document.categories = reactive(
      Object.fromEntries(Object.entries(empty.categories).map(([key, items]) => [key, [...items]]))
    )
  }
}

const saveDocument = async () => {
  if (!isHydrated.value || !planTaskId.value) return
  isSaving.value = true
  saveState.value = 'saving'
  try {
    await axios.put(`/api/worksheets/${kind.value}`, {
      fields: document.fields || {},
      rows: document.rows || [],
      categories: document.categories || {},
    })
    saveState.value = 'saved'
  } catch (saveError) {
    console.error('Failed to save worksheet:', saveError)
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
    saveDocument()
  }, 700)
}

const downloadExport = async (format) => {
  try {
    isExporting.value = format
    if (isHydrated.value && planTaskId.value) {
      await saveDocument()
    }
    const response = await axios.get(`/api/worksheets/${kind.value}/export`, {
      params: { format },
      responseType: 'blob',
    })
    const extension = format === 'docx' ? 'docx' : 'pdf'
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `${schema.value.title}.${extension}`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (exportError) {
    console.error('Failed to export worksheet:', exportError)
    error.value = texts.value.loadError
  } finally {
    isExporting.value = null
  }
}

const fetchTemplate = async () => {
  isLoading.value = true
  error.value = ''
  isHydrated.value = false
  apiSchema.value = null
  try {
    const response = await axios.get(`/api/worksheets/${kind.value}`)
    if (!response.data?.success) {
      router.replace('/dashboard/documents')
      return
    }
    apiSchema.value = schemaFromApi(response.data.schema, kind.value)
    resetDocument(kind.value)
    applyDocument(document, response.data.document, kind.value, schema.value)
    planTaskId.value = response.data.plan_task_id || null
    await nextTick()
    isHydrated.value = true
    saveState.value = 'idle'
  } catch (fetchError) {
    console.error('Failed to load worksheet:', fetchError)
    error.value = texts.value.loadError
  } finally {
    isLoading.value = false
  }
}

watch(document, () => {
  scheduleSave()
}, { deep: true })

watch(() => route.params.kind, () => {
  fetchTemplate()
})

onMounted(fetchTemplate)

onUnmounted(() => {
  if (saveTimer) clearTimeout(saveTimer)
})
</script>
