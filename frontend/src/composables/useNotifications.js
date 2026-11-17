import { computed, ref } from 'vue'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'
import { documentStatus, getDocumentCatalog, mergeDocumentCatalog } from '@/data/worksheets'

const adminNotifications = ref([])
const documentReminders = ref([])
const markingRead = ref(false)

const monthsSince = (value) => {
  const updated = new Date(value).getTime()
  if (!updated) return 0
  return Math.max(1, Math.round((Date.now() - updated) / (30 * 24 * 60 * 60 * 1000)))
}

export function useNotifications() {
  const languageStore = useLanguageStore()
  const notifications = computed(() => [...documentReminders.value, ...adminNotifications.value])
  const unreadCount = computed(() => (
    adminNotifications.value.filter((item) => !item.read).length + documentReminders.value.length
  ))
  const hasUnreadAnnouncements = computed(() => adminNotifications.value.some((item) => !item.read))

  const formatNotificationDate = (value) => {
    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return ''
    const locale = languageStore.language === 'de' ? 'de-DE' : 'en-US'
    const month = date.toLocaleString(locale, { month: 'short' }).replace('.', '').toUpperCase()
    const time = date.toLocaleString(locale, { hour: 'numeric', minute: '2-digit' })
    return `${month} ${date.getDate()} · ${time}`
  }

  const fetchDocumentReminders = async () => {
    try {
      const response = await axios.get('/api/worksheets')
      const catalog = mergeDocumentCatalog(
        getDocumentCatalog(languageStore.language),
        response.data?.catalog || [],
      )
      const byKind = Object.fromEntries((response.data?.worksheets || []).map((item) => [item.kind, item]))
      const isDe = languageStore.language === 'de'
      documentReminders.value = catalog
        .map((meta) => {
          const live = byKind[meta.kind] || {}
          return { ...meta, ...live, status: documentStatus(live) }
        })
        .filter((item) => item.plan_task_id && item.status === 'needs_review')
        .sort((a, b) => new Date(a.updated_at || 0) - new Date(b.updated_at || 0))
        .slice(0, 5)
        .map((item) => {
          const months = monthsSince(item.updated_at)
          const ago = isDe
            ? `vor ${months} ${months === 1 ? 'Monat' : 'Monaten'}`
            : `${months} ${months === 1 ? 'month' : 'months'} ago`
          return {
            id: `document-${item.kind}`,
            title: isDe ? `${item.title} prüfen` : `Review ${item.title}`,
            body: isDe
              ? `Zuletzt ${ago} bearbeitet. Aktualisiere es, damit der Rest von Peerie stimmt.`
              : `Last updated ${ago}. Review it so the rest of Peerie stays accurate.`,
            published_at: item.updated_at,
            read: false,
            to: `/dashboard/documents/${item.kind.replaceAll('_', '-')}`,
          }
        })
    } catch (error) {
      documentReminders.value = []
    }
  }

  const fetchNotifications = async () => {
    await Promise.all([
      (async () => {
        try {
          const response = await axios.get('/api/notifications', {
            params: { language: languageStore.language },
          })
          adminNotifications.value = response.data?.notifications || []
        } catch (error) {
          adminNotifications.value = []
        }
      })(),
      fetchDocumentReminders(),
    ])
  }

  const markAllRead = async () => {
    if (!adminNotifications.value.some((item) => !item.read) || markingRead.value) return
    markingRead.value = true
    try {
      const response = await axios.post('/api/notifications/read', null, {
        params: { language: languageStore.language },
      })
      adminNotifications.value = response.data?.notifications || adminNotifications.value.map((item) => ({ ...item, read: true }))
    } catch (error) {
      console.error('Failed to mark notifications read:', error)
    } finally {
      markingRead.value = false
    }
  }

  return {
    notifications,
    unreadCount,
    hasUnreadAnnouncements,
    markingRead,
    formatNotificationDate,
    fetchNotifications,
    markAllRead,
  }
}
