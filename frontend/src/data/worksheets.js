import { BUYER_PERSONA_FIELDS, emptyBuyerPersonaFields, getBuyerPersonaCopy } from './buyerPersonaWorksheet'

export const WORKSHEET_SLUGS = [
  'buyer-persona',
  'brand-identity',
  'content-bank',
  'content-audit',
  'content-planning',
]

export function kindToSlug(kind) {
  return String(kind || '').replaceAll('_', '-')
}

export function slugToKind(slug) {
  return String(slug || '').replaceAll('-', '_')
}

function normalizeTitle(title) {
  return String(title || '')
    .toLowerCase()
    .replaceAll('-', ' ')
    .replaceAll('–', ' ')
    .replaceAll('—', ' ')
    .replace(/\s+/g, ' ')
}

export function worksheetKindFromTitle(title) {
  const normalized = normalizeTitle(title)
  if (normalized.includes('buyer persona')) return 'buyer_persona'
  if (normalized.includes('brand identity') || normalized.includes('markenidentit') || normalized.includes('unternehmensmarke') || normalized.includes('company brand')) {
    return 'brand_identity'
  }
  if (normalized.includes('content bank')) return 'content_bank'
  if (normalized.includes('content audit')) return 'content_audit'
  if (normalized.includes('content planning') || normalized.includes('content calendar') || normalized.includes('monats content') || normalized.includes('plan content for the month') || normalized.includes('plane deinen content')) {
    return 'content_planning'
  }
  return null
}

const BRAND_FIELDS = [
  { key: 'why_started', step: 1, type: 'textarea', rows: 4 },
  { key: 'change_to_create', step: 1, type: 'textarea', rows: 3 },
  { key: 'unique_approach', step: 1, type: 'textarea', rows: 3 },
  { key: 'brand_story', step: 1, type: 'textarea', rows: 8 },
  { key: 'core_values', step: 2, type: 'textarea', rows: 6 },
  { key: 'personality_traits', step: 3, type: 'textarea', rows: 5 },
  { key: 'communication_style', step: 3, type: 'textarea', rows: 5 },
  { key: 'colours', step: 4, type: 'textarea', rows: 3 },
  { key: 'fonts', step: 4, type: 'text' },
  { key: 'imagery_style', step: 4, type: 'textarea', rows: 3 },
  { key: 'visual_notes', step: 4, type: 'textarea', rows: 3 },
]

const PLANNING_FIELDS = [
  { key: 'active_channels', step: 1, type: 'textarea', rows: 4 },
  { key: 'time_per_week', step: 1, type: 'text' },
  { key: 'monthly_goal', step: 2, type: 'textarea', rows: 3 },
  { key: 'whats_happening', step: 2, type: 'textarea', rows: 3 },
  { key: 'audience_need', step: 2, type: 'textarea', rows: 3 },
  { key: 'monthly_theme', step: 2, type: 'text' },
  { key: 'hero_pieces', step: 3, type: 'textarea', rows: 5 },
  { key: 'ctas', step: 3, type: 'textarea', rows: 4 },
  { key: 'channel_map', step: 4, type: 'textarea', rows: 6 },
]

const AUDIT_INSIGHT_FIELDS = [
  { key: 'insights_topics', step: 3, type: 'textarea', rows: 4 },
  { key: 'insights_formats', step: 3, type: 'textarea', rows: 4 },
  { key: 'insights_ignored', step: 3, type: 'textarea', rows: 3 },
  { key: 'gaps', step: 4, type: 'textarea', rows: 4 },
]

const CONTENT_BANK_CATEGORIES = [
  'quick_wins',
  'education',
  'results',
  'behind_scenes',
  'myths',
  'promo',
  'seasonal',
]

const AUDIT_ROW_KEYS = ['date', 'platform', 'topic', 'format', 'category', 'engagement', 'notes']

function emptyFromKeys(keys) {
  return keys.reduce((acc, key) => {
    acc[key] = ''
    return acc
  }, {})
}

export function schemaFromTask(task) {
  const fields = Array.isArray(task?.document_fields) ? task.document_fields : []
  const layout = task?.document_layout || 'fields'
  const labels = {}
  const schemaFields = []
  const keys = []
  fields.forEach((field, index) => {
    const key = String(field?.key || '').trim() || `field_${index + 1}`
    keys.push(key)
    labels[key] = field?.label || key
    schemaFields.push({
      key,
      type: field?.type === 'textarea' ? 'textarea' : 'text',
      step: Number(field?.step) > 0 ? Number(field.step) : 1,
      rows: field?.type === 'textarea' ? 4 : undefined,
    })
  })
  return {
    kind: slugToKind(task?.document_key || ''),
    layout,
    fields: layout === 'fields' ? schemaFields : [],
    categoryKeys: layout === 'categories' ? keys : [],
    rowKeys: layout === 'rows' ? keys : [],
    labels,
    placeholders: { idea: 'New idea...' },
    stepTitles: {},
    title: task?.title || '',
    intro: task?.document_description || '',
  }
}

export function schemaFromApi(payload, fallbackKind) {
  if (!payload || typeof payload !== 'object') return null
  return {
    kind: payload.kind || fallbackKind,
    layout: payload.layout || 'fields',
    fields: Array.isArray(payload.fields) ? payload.fields : [],
    categoryKeys: payload.categoryKeys || [],
    rowKeys: payload.rowKeys || [],
    labels: payload.labels || {},
    placeholders: payload.placeholders || { idea: 'New idea...' },
    stepTitles: payload.stepTitles || {},
    title: payload.title || '',
    intro: payload.intro || '',
  }
}

export function mergeDocumentCatalog(base, extra) {
  const items = [...base]
  ;(extra || []).forEach((item) => {
    if (!item?.kind) return
    const index = items.findIndex((entry) => entry.kind === item.kind)
    const mapped = {
      kind: item.kind,
      group: item.group || 'planning',
      title: item.title || item.kind,
      shortLabel: item.shortLabel || '',
      description: item.description || '',
    }
    if (index >= 0) {
      const current = items[index]
      items[index] = {
        ...mapped,
        ...current,
        group: current.group || mapped.group,
        title: current.title || mapped.title,
        shortLabel: current.shortLabel || mapped.shortLabel,
        description: current.description || mapped.description,
      }
      return
    }
    items.push(mapped)
  })
  return items
}

export function emptyWorksheetDocument(kind, schema) {
  if (schema?.layout === 'categories') {
    return {
      type: kind,
      fields: {},
      categories: (schema.categoryKeys || []).reduce((acc, key) => {
        acc[key] = ['']
        return acc
      }, {}),
    }
  }
  if (schema?.layout === 'rows') {
    return {
      type: kind,
      fields: emptyFromKeys((schema.fields || []).map((field) => field.key)),
      rows: [emptyFromKeys(schema.rowKeys || [])],
    }
  }
  if (schema?.fields?.length) {
    return { type: kind, fields: emptyFromKeys(schema.fields.map((field) => field.key)) }
  }
  if (kind === 'buyer_persona') {
    return { type: kind, fields: emptyBuyerPersonaFields() }
  }
  if (kind === 'brand_identity') {
    return { type: kind, fields: emptyFromKeys(BRAND_FIELDS.map((field) => field.key)) }
  }
  if (kind === 'content_planning') {
    return { type: kind, fields: emptyFromKeys(PLANNING_FIELDS.map((field) => field.key)) }
  }
  if (kind === 'content_audit') {
    return {
      type: kind,
      fields: emptyFromKeys(AUDIT_INSIGHT_FIELDS.map((field) => field.key)),
      rows: [emptyFromKeys(AUDIT_ROW_KEYS)],
    }
  }
  if (kind === 'content_bank') {
    return {
      type: kind,
      fields: {},
      categories: CONTENT_BANK_CATEGORIES.reduce((acc, key) => {
        acc[key] = ['']
        return acc
      }, {}),
    }
  }
  return { type: kind, fields: {} }
}

export function getWorksheetSchema(kind, lang) {
  const isDe = lang === 'de'
  if (!kind) {
    return { kind: null, layout: 'fields', fields: [], stepTitles: {}, labels: {}, placeholders: {} }
  }
  if (kind === 'buyer_persona') {
    const copy = getBuyerPersonaCopy(lang)
    return {
      kind,
      layout: 'fields',
      fields: BUYER_PERSONA_FIELDS,
      stepTitles: copy.stepTitles,
      labels: copy.labels,
      placeholders: copy.placeholders,
      title: copy.stepTitles?.[1] ? (isDe ? 'Buyer Persona Vorlage' : 'Buyer Persona Template') : 'Buyer Persona',
      intro: isDe
        ? 'Hier siehst du alles, was du in der Aufgabe ausgefüllt hast. Du kannst die Vorlage direkt ergänzen und als PDF oder Word herunterladen.'
        : 'This is the data you entered in the task. You can add more here and download the filled template as PDF or Word.',
    }
  }

  if (kind === 'brand_identity') {
    return {
      kind,
      layout: 'fields',
      fields: BRAND_FIELDS,
      title: isDe ? 'Markenidentität-Vorlage' : 'Brand Identity Template',
      intro: isDe
        ? 'Deine Markenidentität aus der Aufgabe. Ergänze sie hier und lade sie als PDF oder Word herunter.'
        : 'Your brand identity from the task. Complete it here and download it as PDF or Word.',
      stepTitles: isDe
        ? { 1: 'Schritt 1: Markengeschichte', 2: 'Schritt 2: Kernwerte', 3: 'Schritt 3: Markenpersönlichkeit', 4: 'Schritt 4: Visuelle Identität' }
        : { 1: 'Step 1: Brand story', 2: 'Step 2: Core values', 3: 'Step 3: Brand personality', 4: 'Step 4: Visual identity' },
      labels: isDe
        ? {
            why_started: 'Warum du gestartet hast',
            change_to_create: 'Die Veränderung, die du schaffen willst',
            unique_approach: 'Was deinen Ansatz unterscheidet',
            brand_story: 'Markengeschichte',
            core_values: 'Kernwerte',
            personality_traits: 'Persönlichkeitsmerkmale',
            communication_style: 'Wie sich das in der Kommunikation zeigt',
            colours: 'Farben',
            fonts: 'Schriften',
            imagery_style: 'Bildstil',
            visual_notes: 'Visuelle Notizen',
          }
        : {
            why_started: 'Why you started',
            change_to_create: 'The change you want to create',
            unique_approach: 'What makes your approach different',
            brand_story: 'Brand story',
            core_values: 'Core values',
            personality_traits: 'Personality traits',
            communication_style: 'How these traits show up',
            colours: 'Colours',
            fonts: 'Fonts',
            imagery_style: 'Imagery style',
            visual_notes: 'Visual notes',
          },
      placeholders: isDe
        ? {
            why_started: '2–3 Sätze, ehrlich und menschlich',
            change_to_create: 'Welche Veränderung sollen Kund:innen erleben?',
            unique_approach: 'Was machst du anders als andere?',
            brand_story: '3–5 kurze Absätze',
            core_values: '3–5 Werte mit je einem Satz',
            personality_traits: '3–5 Eigenschaften',
            communication_style: 'Wie zeigt sich das in Texten und Gesprächen?',
            colours: '2–4 Farben, gerne mit Hex-Codes',
            fonts: 'Überschrift + Fließtext',
            imagery_style: 'Fotos, Illustrationen, Stimmung',
            visual_notes: 'Was soll immer gleich aussehen?',
          }
        : {
            why_started: '2–3 honest, human sentences',
            change_to_create: 'What change should customers feel?',
            unique_approach: 'What do you do differently?',
            brand_story: '3–5 short paragraphs',
            core_values: '3–5 values with one sentence each',
            personality_traits: '3–5 traits',
            communication_style: 'How this shows up in writing and conversations',
            colours: '2–4 colours, hex codes welcome',
            fonts: 'Headline + body',
            imagery_style: 'Photos, illustrations, mood',
            visual_notes: 'What should always look consistent?',
          },
    }
  }

  if (kind === 'content_planning') {
    return {
      kind,
      layout: 'fields',
      fields: PLANNING_FIELDS,
      title: isDe ? 'Monats-Content-Planning-Vorlage' : 'Monthly Content Planning Template',
      intro: isDe
        ? 'Dein Monatsplan aus der Aufgabe. Ergänze ihn hier und lade ihn als PDF oder Word herunter.'
        : 'Your monthly plan from the task. Complete it here and download it as PDF or Word.',
      stepTitles: isDe
        ? { 1: 'Schritt 1: Kanäle & Zeit', 2: 'Schritt 2: Monatlicher Fokus', 3: 'Schritt 3: Key Pieces & CTAs', 4: 'Schritt 4: Kanalplan' }
        : { 1: 'Step 1: Channels & time', 2: 'Step 2: Monthly focus', 3: 'Step 3: Key pieces & CTAs', 4: 'Step 4: Channel map' },
      labels: isDe
        ? {
            active_channels: 'Aktive Kanäle',
            time_per_week: 'Zeit pro Woche',
            monthly_goal: 'Hauptziel diesen Monat',
            whats_happening: 'Was diesen Monat passiert',
            audience_need: 'Was deine Zielgruppe jetzt braucht',
            monthly_theme: 'Monatsthema',
            hero_pieces: 'Wichtige Content-Stücke',
            ctas: 'CTAs für den Monat',
            channel_map: 'Verteilung auf die Kanäle',
          }
        : {
            active_channels: 'Active channels',
            time_per_week: 'Time available per week',
            monthly_goal: 'Main goal this month',
            whats_happening: 'What’s happening this month',
            audience_need: 'What your audience needs now',
            monthly_theme: 'Monthly theme',
            hero_pieces: 'Key content pieces',
            ctas: 'CTAs for the month',
            channel_map: 'How content maps across channels',
          },
      placeholders: isDe
        ? {
            active_channels: 'GBP, Instagram, E-Mail…',
            time_per_week: 'z. B. 3 Stunden',
            monthly_goal: 'Buchungen, Reichweite, Liste…',
            whats_happening: 'Saison, Events, Angebote',
            audience_need: 'Fragen, Themen, Pain Points',
            monthly_theme: 'z. B. Verletzungsfrei durch den Herbst',
            hero_pieces: '3–5 Hauptinhalte',
            ctas: 'Eine klare Handlung pro Ziel',
            channel_map: 'Welcher Inhalt wo erscheint',
          }
        : {
            active_channels: 'GBP, Instagram, email…',
            time_per_week: 'e.g. 3 hours',
            monthly_goal: 'Bookings, awareness, list growth…',
            whats_happening: 'Season, events, offers',
            audience_need: 'Questions, topics, pain points',
            monthly_theme: 'e.g. Stay injury-free this autumn',
            hero_pieces: '3–5 priority pieces',
            ctas: 'One clear action per goal',
            channel_map: 'Which piece goes where',
          },
    }
  }

  if (kind === 'content_bank') {
    return {
      kind,
      layout: 'categories',
      fields: [],
      categoryKeys: CONTENT_BANK_CATEGORIES,
      title: isDe ? 'Content-Bank-Vorlage' : 'Content Bank Template',
      intro: isDe
        ? 'Sammle Ideen nach Kategorien. Alles, was du in der Aufgabe festhältst, landet hier.'
        : 'Collect ideas by category. Anything you capture in the task lands here.',
      stepTitles: {},
      labels: isDe
        ? {
            quick_wins: 'Schnelle Erfolge',
            education: 'Bildung & Tipps',
            results: 'Kundenergebnisse & Testimonials',
            behind_scenes: 'Hinter den Kulissen',
            myths: 'Mythen aufklären',
            promo: 'Werbung',
            seasonal: 'Saisonal',
          }
        : {
            quick_wins: 'Quick wins',
            education: 'Education & tips',
            results: 'Client results & testimonials',
            behind_scenes: 'Behind the scenes',
            myths: 'Myth-busting',
            promo: 'Promotional',
            seasonal: 'Seasonal',
          },
      placeholders: isDe
        ? { idea: 'Neue Idee…' }
        : { idea: 'New idea…' },
    }
  }

  if (kind === 'content_audit') {
    return {
    kind,
    layout: 'rows',
    fields: AUDIT_INSIGHT_FIELDS,
    rowKeys: AUDIT_ROW_KEYS,
    title: isDe ? 'Content-Audit-Vorlage' : 'Content Audit Template',
    intro: isDe
      ? 'Dein Content-Audit aus der Aufgabe. Ergänze Zeilen, Insights und Lücken – und lade es als PDF oder Word herunter.'
      : 'Your content audit from the task. Add rows, insights and gaps — then download it as PDF or Word.',
    stepTitles: isDe
      ? { 3: 'Schritt 3: Was funktioniert', 4: 'Schritt 4: Lücken & Chancen' }
      : { 3: 'Step 3: What’s working', 4: 'Step 4: Gaps & opportunities' },
    labels: isDe
      ? {
          date: 'Datum',
          platform: 'Plattform',
          topic: 'Thema / Titel',
          format: 'Format',
          category: 'Kategorie',
          engagement: 'Engagement',
          notes: 'Notizen',
          insights_topics: 'Themen, die ankommen',
          insights_formats: 'Formate, die funktionieren',
          insights_ignored: 'Was ignoriert wird',
          gaps: 'Content-Lücken',
        }
      : {
          date: 'Date',
          platform: 'Platform',
          topic: 'Topic / title',
          format: 'Format',
          category: 'Category',
          engagement: 'Engagement',
          notes: 'Notes',
          insights_topics: 'Topics that resonate',
          insights_formats: 'Formats that perform',
          insights_ignored: 'What’s getting ignored',
          gaps: 'Content gaps',
        },
    placeholders: isDe
      ? {
          date: 'TT.MM.JJJJ',
          platform: 'Instagram, Blog…',
          topic: 'Worum ging es?',
          format: 'Reel, Karussell, Blog…',
          category: 'Bildung, Testimonial…',
          engagement: 'Saves, DMs, Klicks…',
          notes: 'Beobachtung',
          insights_topics: 'Was kam besonders gut an?',
          insights_formats: 'Welche Formate performen?',
          insights_ignored: 'Was bleibt liegen?',
          gaps: 'Was fehlt noch?',
        }
      : {
          date: 'YYYY-MM-DD',
          platform: 'Instagram, blog…',
          topic: 'What was it about?',
          format: 'Reel, carousel, blog…',
          category: 'Education, testimonial…',
          engagement: 'Saves, DMs, clicks…',
          notes: 'Observation',
          insights_topics: 'What resonated most?',
          insights_formats: 'Which formats perform?',
          insights_ignored: 'What is being ignored?',
          gaps: 'What is missing?',
        },
    }
  }

  return {
    kind,
    layout: 'fields',
    fields: [],
    categoryKeys: [],
    rowKeys: [],
    stepTitles: {},
    labels: {},
    placeholders: {},
  }
}

export function applyDocument(target, incoming, kind, schema) {
  const empty = emptyWorksheetDocument(kind, schema)
  if (!target.fields) target.fields = {}
  Object.keys(target.fields).forEach((key) => {
    delete target.fields[key]
  })
  Object.assign(target.fields, empty.fields || {}, incoming?.fields || {})

  if (empty.rows) {
    if (!Array.isArray(target.rows)) target.rows = []
    target.rows.splice(
      0,
      target.rows.length,
      ...(incoming?.rows?.length ? incoming.rows.map((row) => ({ ...empty.rows[0], ...row })) : empty.rows)
    )
  }
  if (empty.categories) {
    if (!target.categories) target.categories = {}
    Object.keys(target.categories).forEach((key) => {
      delete target.categories[key]
    })
    Object.assign(target.categories, empty.categories)
    Object.entries(incoming?.categories || {}).forEach(([key, items]) => {
      if (key in target.categories && Array.isArray(items) && items.length) {
        target.categories[key] = items.map((item) => item || '')
      }
    })
  }
}

const STALE_AFTER_MS = 90 * 24 * 60 * 60 * 1000

export function documentStatus(item) {
  if (!item?.filled) return 'not_started'
  const updated = item.updated_at ? new Date(item.updated_at).getTime() : 0
  if (updated && Date.now() - updated > STALE_AFTER_MS) return 'needs_review'
  if (!item.completed) return 'draft'
  return 'up_to_date'
}

export function getDocumentCatalog(lang) {
  const isDe = lang === 'de'
  return [
    {
      kind: 'buyer_persona',
      group: 'brand',
      title: isDe ? 'Buyer Persona' : 'Buyer Persona',
      shortLabel: isDe ? 'Wer dein idealer Kunde ist' : 'Who your ideal client is',
      description: isDe
        ? 'Dein ideales Gegenüber: Wer kauft, was sie brauchen und wie du sie erreichst.'
        : 'Who you sell to — their world, pains and how they decide.',
    },
    {
      kind: 'brand_identity',
      group: 'brand',
      title: isDe ? 'Markenidentität' : 'Brand Identity',
      shortLabel: isDe ? 'Wie deine Marke wirkt' : 'How your brand looks and sounds',
      description: isDe
        ? 'Geschichte, Werte, Persönlichkeit und visuelle Leitplanken deiner Marke.'
        : 'Your story, values, personality and visual identity in one place.',
    },
    {
      kind: 'content_audit',
      group: 'planning',
      title: isDe ? 'Content Audit' : 'Content Audit',
      shortLabel: isDe ? 'Was bereits funktioniert' : 'What content already works',
      description: isDe
        ? 'Ein Blick darauf, was du bisher gepostet hast und was funktioniert hat, damit neuer Content darauf aufbaut.'
        : 'A snapshot of what you have posted so far and what worked, so future content builds on what already resonates.',
    },
    {
      kind: 'content_bank',
      group: 'planning',
      title: isDe ? 'Content Bank' : 'Content Bank',
      shortLabel: isDe ? 'Ideen, bereit zum Posten' : 'Ideas ready to post',
      description: isDe
        ? 'Eine laufende Liste mit Ideen und Entwürfen, auf die du zugreifen kannst, wenn du etwas posten willst.'
        : 'A running list of content ideas and drafts you can pull from whenever you need something to post.',
    },
    {
      kind: 'content_planning',
      group: 'planning',
      title: isDe ? 'Monatsplanung' : 'Monthly Planning',
      shortLabel: isDe ? 'Dein Content für den Monat' : "This month's content plan",
      description: isDe
        ? 'Themen, Kanäle und CTAs für den Monat — damit du nicht jedes Mal bei null anfängst.'
        : 'Themes, channels and CTAs for the month, so you are not starting from scratch each time.',
    },
  ]
}

export function getDocumentGroups(lang) {
  const isDe = lang === 'de'
  return [
    { id: 'brand', title: isDe ? 'Marke' : 'Brand' },
    { id: 'planning', title: isDe ? 'Planung' : 'Planning' },
  ]
}
