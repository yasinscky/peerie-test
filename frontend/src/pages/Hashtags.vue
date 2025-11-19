<template>
  <div class="bg-white rounded-xl shadow-sm border border-[#DCDCDC] p-6">
    <div class="mb-6 space-y-3">
      <h2 class="text-xl font-semibold text-[#3F4369]">{{ headerTitle }}</h2>
      <p class="text-sm text-[#3F4369] opacity-80 leading-relaxed">
        {{ introDescription }}
      </p>
    </div>

      <div v-if="loading" class="py-10 text-center text-[#3F4369] opacity-70">Loading...</div>
    <div v-else>
      <div v-if="!hashtagBlocks.length" class="py-6 text-[#3F4369] opacity-70">
        {{ LOCALES[userLanguage]?.noHashtagsMessage || LOCALES.en.noHashtagsMessage }}
      </div>

      <div v-else class="space-y-6">
        <div v-if="topGuidelines.length" class="space-y-8">
          <div
            v-for="(section, sectionIndex) in topGuidelines"
            :key="sectionIndex"
            class="rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] p-5"
          >
            <h3 class="text-lg font-semibold text-[#3F4369] mb-4">{{ section.title }}</h3>

            <div class="space-y-4">
              <div
                v-for="(item, itemIndex) in section.items"
                :key="itemIndex"
                class="space-y-1"
              >
                <h4 v-if="item.subtitle" class="text-sm font-medium text-[#3F4369]">
                  {{ item.subtitle }}
                </h4>
                <p class="text-sm text-[#3F4369] opacity-80 leading-relaxed">
                  {{ item.text }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Hashtag Blocks -->
        <div v-for="(block, index) in hashtagBlocks" :key="index" class="space-y-4">
          <div>
            <h4 class="text-lg font-medium text-[#3F4369] mb-2">{{ block.title }}</h4>
            <p class="text-sm text-[#3F4369] opacity-70 mb-3">{{ block.description }}</p>
          </div>
          
          <!-- Hashtag Block with Copy Icon -->
          <div class="relative bg-gray-50 border border-gray-200 rounded-lg p-4">
            <!-- Copy Icon in top-right corner -->
            <button
              @click="copyBlock(block.tags)"
              class="absolute top-3 right-3 p-2 bg-black text-white rounded hover:bg-gray-800 transition-colors"
              :disabled="copying"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2M8 16h8a2 2 0 002-2V8m-6 12H8a2 2 0 01-2-2v-6m6 8l6-6"></path>
              </svg>
            </button>
            
            <!-- Hashtags -->
            <div class="text-[#3F4369] break-words pr-12">
              <!-- Branded block: display with descriptions on separate lines -->
              <template v-if="(block.title === '5 – Branded' || block.title === '5 – Marke') && isBrandedFormat(block.tags)">
                <div v-for="(item, i) in block.tags" :key="i" class="mb-3 last:mb-0">
                  <span class="font-medium">{{ item.tag || item }}</span>
                  <span v-if="item.description" class="text-[#3F4369] opacity-70 ml-2">({{ item.description }})</span>
                </div>
              </template>
              <!-- Industry block with categories -->
              <template v-else-if="(block.title === '3 – Industry & Expertise' || block.title === '3 – Branche & Expertise') && block.categories">
                <div v-for="(tags, categoryName, index) in block.categories" :key="categoryName" :class="{'mt-4': index > 0}">
                  <div class="mb-2 text-sm font-semibold text-[#3F4369]">{{ categoryName }}:</div>
                  <div>
                    <span v-for="(tag, i) in tags" :key="i" class="inline-block mr-2 mb-2">{{ tag }}</span>
                  </div>
                </div>
              </template>
              <!-- Niche block: display with example prefix -->
              <template v-else-if="block.title === '4 – Niche' || block.title === '4 – Nische'">
                <div class="mb-2 text-sm text-[#3F4369] opacity-70 italic">{{ LOCALES[userLanguage]?.nicheExamplePrefix || LOCALES.en.nicheExamplePrefix }}</div>
                <div>
                  <span v-for="(tag, i) in block.tags" :key="i" class="inline-block mr-2 mb-2 font-medium">{{ tag }}</span>
                </div>
              </template>
              <!-- Other blocks: inline display -->
              <template v-else>
                <span v-for="(tag, i) in block.tags" :key="i" class="inline-block mr-2 mb-2">{{ tag }}</span>
              </template>
            </div>
          </div>
        </div>

        <div v-if="platformGuidelines.length" class="space-y-8">
          <div
            v-for="(section, sectionIndex) in platformGuidelines"
            :key="sectionIndex"
            class="rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] p-5"
          >
            <h3 class="text-lg font-semibold text-[#3F4369] mb-4">{{ section.title }}</h3>

            <div class="space-y-4">
              <div
                v-for="(item, itemIndex) in section.items"
                :key="itemIndex"
                class="space-y-1"
              >
                <h4 v-if="item.subtitle" class="text-sm font-medium text-[#3F4369]">
                  {{ item.subtitle }}
                </h4>
                <p class="text-sm text-[#3F4369] opacity-80 leading-relaxed">
                  {{ item.text }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <teleport to="body">
    <transition name="toast">
      <div
        v-if="copyMessage"
        class="toast-container fixed bottom-6 left-6 z-50"
      >
        <div class="pointer-events-auto flex items-center gap-2 rounded-lg bg-green-100 px-4 py-3 text-sm text-green-800 shadow-lg border border-green-200">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <span>{{ copyMessage }}</span>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const LOCALES = {
  en: {
    introDescription: 'Hashtags are no longer about chasing viral reach. From 2025 onward, they are more about being found by the right audience than trying to reach everyone. Hashtags now function more like keywords. Platforms prioritise content that\'s easy to categorise and surface in search. Use hashtags the same way someone would search for your service: clear, specific, and relevant.',
    nicheExamplePrefix: 'Examples (create your own or modify):',
    copyMessage: 'Hashtags copied to clipboard',
    noHashtagsMessage: 'No hashtags found for your plan yet.',
    blockDescriptions: {
      local: 'These tags help you reach people searching for services in your area. Use at least one location-based hashtag per post—even if the volume is low, they\'re essential for connecting with local customers who are ready to book.',
      broad: 'These are high-volume hashtags (over 300,000 uses) that help the algorithm understand your general category. Use 1-2 per post to give context, but don\'t rely on them for reach—they\'re competitive and better suited as supporting tags.',
      industry: 'These are your core hashtags—medium volume: 20,000-300,000 uses), targeted, and most likely to connect you with engaged, relevant audiences. Use 2-3 from this section in every post. They\'re specific enough to stand out but popular enough to get traction.',
      niche: 'Niche hashtags are powerful for building targeted visibility, authority, and engagement within specialised communities. While they may not bring huge traffic, they help you reach exactly the right audience and establish credibility in your zone of expertise. These tags often fall below 10,000 uses, which means your content won\'t get buried, and you\'re more likely to be seen by those searching that topic. Use 1-2 per post.',
      branded: 'Branded and campaign-specific hashtags are essential for building brand identity, content discoverability, and long-term engagement. They make it easier for your followers to explore your work, group related posts, and track specific campaigns, launches, or events. These can be your signature tag (your brand, method or offer name) or short-term campaign tags (launch hashtags, challenges). Keep them short, unique, and easy to remember—avoid generic phrases already flooded with unrelated content. Use consistently across posts and encourage your clients to use them when sharing their experiences.',
    },
  },
  de: {
    introDescription: 'Hashtags dienen nicht mehr dazu, virale Reichweite zu jagen. Ab 2025 geht es darum, von der richtigen Zielgruppe gefunden zu werden, statt alle zu erreichen. Hashtags funktionieren jetzt eher wie Suchbegriffe. Plattformen priorisieren Inhalte, die leicht zu kategorisieren und in der Suche anzuzeigen sind. Nutze Hashtags so, wie jemand nach deinem Service suchen würde: klar, spezifisch und relevant.',
    nicheExamplePrefix: 'Beispiele (erstelle deine eigenen oder passe an):',
    copyMessage: 'Hashtags in die Zwischenablage kopiert',
    noHashtagsMessage: 'Noch keine Hashtags für deinen Plan gefunden.',
    blockDescriptions: {
      local: 'Diese Tags helfen dir, Personen zu erreichen, die nach Services in deiner Region suchen. Nutze mindestens einen standortbasierten Hashtag pro Beitrag – selbst wenn das Volumen niedrig ist, sind sie essenziell, um lokale Kunden zu verbinden, die bereit sind zu buchen.',
      broad: 'Das sind hochvolumige Hashtags (über 300.000 Nutzungen), die dem Algorithmus helfen, deine allgemeine Kategorie zu verstehen. Nutze 1-2 pro Beitrag für Kontext, aber verlasse dich nicht auf sie für Reichweite – sie sind umkämpft und eignen sich besser als unterstützende Tags.',
      industry: 'Das sind deine Kern-Hashtags – mittleres Volumen (20.000-300.000 Nutzungen), zielgerichtet und am wahrscheinlichsten, um dich mit engagierten, relevanten Zielgruppen zu verbinden. Nutze 2-3 aus diesem Bereich in jedem Beitrag. Sie sind spezifisch genug, um hervorzustechen, aber populär genug für Traktion.',
      niche: 'Nischen-Hashtags sind kraftvoll, um gezielte Sichtbarkeit, Autorität und Engagement in spezialisierten Communities aufzubauen. Sie bringen vielleicht nicht riesigen Traffic, aber sie helfen dir, genau die richtige Zielgruppe zu erreichen und Glaubwürdigkeit in deinem Expertenbereich aufzubauen. Diese Tags liegen oft unter 10.000 Nutzungen, was bedeutet, dass deine Inhalte nicht untergehen und du eher von denen gesehen wirst, die nach diesem Thema suchen. Nutze 1-2 pro Beitrag.',
      branded: 'Marken- und kampagnenspezifische Hashtags sind essenziell für den Aufbau von Markenidentität, Content-Auffindbarkeit und langfristigem Engagement. Sie machen es deinen Followern leichter, deine Arbeit zu erkunden, verwandte Beiträge zu gruppieren und spezifische Kampagnen, Launches oder Events zu tracken. Das können dein Signatur-Tag (dein Marken-, Methoden- oder Angebotsname) oder kurzfristige Kampagnen-Tags (Launch-Hashtags, Challenges) sein. Halte sie kurz, einzigartig und leicht zu merken – vermeide generische Phrasen, die bereits mit unverwandten Inhalten überflutet sind. Nutze sie konsistent über Beiträge hinweg und ermutige deine Kunden, sie zu verwenden, wenn sie ihre Erfahrungen teilen.',
    },
  },
}

const DEFAULT_INTRO_DESCRIPTION = LOCALES.en.introDescription

const DEFAULT_GUIDELINES = {
  en: [
    {
      'title': 'Why Hashtags Still Matter',
      'items': [
        {
          'subtitle': 'They organise your content',
          'text': 'Hashtags work as metadata—they tell platforms what your post is about so it can be sorted, searched, and suggested to the right people.',
        },
        {
          'subtitle': 'They expand your reach',
          'text': 'When you use relevant hashtags, you connect with people beyond your followers—those actively searching for what you offer or scrolling topics they care about.',
        },
        {
          'subtitle': 'They build community',
          'text': 'Branded hashtags unite your content, encourage user-generated posts, and create a shared space for your customers and peers. Whether it\'s a product launch, a seasonal offer, or a movement you\'re part of, they create a thread people can follow and join.',
        },
      ],
    },
    {
      'title': 'Modern Hashtag Strategy',
      'items': [
        {
          'subtitle': 'Keep it simple',
          'text': 'Save your hashtags in a notes app, spreadsheet, or scheduling tool. Copy the relevant set for each post—but don\'t use the exact same list every time. Rotate your hashtags and tailor them to the specific post. Platforms can flag repetitive hashtag use, which may limit your reach.',
        },
        {
          'subtitle': 'Go niche, not broad',
          'text': '#BeautySalon has millions of posts. #BeautySalonLondon has far fewer—but the people searching it are exactly who you want. Niche hashtags attract a smaller, more engaged audience that\'s actually interested in what you do.',
        },
        {
          'subtitle': 'Quality beats quantity',
          'text': 'Aim for 3-5 well-chosen hashtags per post. They should be relevant, specific, and varied. Stuffing 20+ hashtags into a post doesn\'t help—it can actually hurt your reach.',
        },
        {
          'subtitle': 'Avoid banned hashtags',
          'text': 'Avoid using hashtags that are overly generic, spammy, or flagged by platforms—they often do more harm than good by reducing your post\'s visibility or triggering algorithmic downranking. Examples: #Follow4Follow, #Like4Like, #InstaGood, #Love, #PhotoOfTheDay.',
        },
        {
          'subtitle': 'Track what works',
          'text': 'Check your post insights after a few weeks. Which hashtags appeared in your top-performing posts? Keep those and swap out the ones that didn\'t perform.',
        },
      ],
    },
    {
      'title': 'How to Use This List',
      'items': [
        {
          'text': 'We\'ve researched and hand-picked these hashtags specifically for your industry and country. They\'re grouped into five categories: Local, Broad, Industry & Expertise, Niche, and Branded.',
        },
        {
          'text': 'Here\'s how to mix them: Most of your hashtags should come from the Industry & Expertise section—these sit in the sweet spot with 20,000-300,000 uses (medium volume). They\'re popular enough to get you noticed but specific enough to reach the right people. Add 1-2 Niche hashtags (under 20,000 uses) to connect with people searching for your exact service, and 1-2 Broad hashtags (over 300,000 uses) to help the algorithm understand your wider category.',
        },
        {
          'text': 'Local hashtags should be used regardless of volume—they\'re essential for attracting nearby customers. Same goes for Branded hashtags—use yours consistently to build recognition and encourage customer shares.',
        },
        {
          'text': 'Pick 3-5 hashtags per post, rotate them based on what you\'re sharing, and track which ones perform best. The goal isn\'t to use every hashtag available—it\'s to use the specific ones that help the right people find you.',
        },
      ],
    },
  ],
  de: [
    {
      'title': 'Warum Hashtags noch wichtig sind',
      'items': [
        {
          'subtitle': 'Sie organisieren deine Inhalte',
          'text': 'Hashtags funktionieren als Metadaten – sie sagen den Plattformen, worum es in deinem Beitrag geht, damit er sortiert, durchsucht und den richtigen Personen vorgeschlagen werden kann.',
        },
        {
          'subtitle': 'Sie erweitern deine Reichweite',
          'text': 'Wenn du relevante Hashtags nutzt, verbindest du dich mit Personen außerhalb deiner Follower – mit denen, die aktiv nach deinem Angebot suchen oder durch Themen scrollen, die sie interessieren.',
        },
        {
          'subtitle': 'Sie bauen Community auf',
          'text': 'Marken-Hashtags vereinen deine Inhalte, fördern nutzergenerierte Beiträge und schaffen einen gemeinsamen Raum für deine Kunden und Gleichgesinnten. Ob Produktlaunch, saisonales Angebot oder Bewegung, an der du teilnimmst – sie schaffen einen roten Faden, dem Leute folgen und beitreten können.',
        },
      ],
    },
    {
      'title': 'Moderne Hashtag-Strategie',
      'items': [
        {
          'subtitle': 'Halte es einfach',
          'text': 'Speichere deine Hashtags in einer Notiz-App, Tabelle oder deinem Planungstool. Kopiere das passende Set für jeden Beitrag – aber verwende nicht jedes Mal dieselbe Liste. Rotiere deine Hashtags und passe sie dem jeweiligen Beitrag an. Plattformen können wiederholte Hashtag-Nutzung markieren, was deine Reichweite einschränken kann.',
        },
        {
          'subtitle': 'Gehe in die Nische, nicht in die Breite',
          'text': '#Coaching hat Millionen von Beiträgen. #BusinessStrategyCoach hat deutlich weniger – aber die Leute, die danach suchen, sind genau die, die du willst. Nischen-Hashtags ziehen ein kleineres, aber engagiertes Publikum an, das sich wirklich für dein Angebot interessiert.',
        },
        {
          'subtitle': 'Qualität schlägt Quantität',
          'text': 'Strebe 3-5 gut gewählte Hashtags pro Beitrag an. Sie sollten relevant, spezifisch und abwechslungsreich sein. 20+ Hashtags in einen Beitrag zu stopfen, hilft nicht – es kann sogar deiner Reichweite schaden.',
        },
        {
          'subtitle': 'Vermeide gesperrte Hashtags',
          'text': 'Vermeide Hashtags, die zu generisch, spammy oder von Plattformen markiert sind – sie richten oft mehr Schaden an, indem sie die Sichtbarkeit deines Beitrags reduzieren oder algorithmisches Downranking auslösen. Beispiele: #Follow4Follow, #Like4Like, #InstaGood, #Love, #PhotoOfTheDay.',
        },
        {
          'subtitle': 'Tracke, was funktioniert',
          'text': 'Prüfe deine Beitrags-Insights nach ein paar Wochen. Welche Hashtags tauchten in deinen erfolgreichsten Beiträgen auf? Behalte diese und tausche die aus, die nicht gut performt haben.',
        },
      ],
    },
    {
      'title': 'So nutzt du diese Liste',
      'items': [
        {
          'text': 'Wir haben diese Hashtags speziell für deine Branche und dein Land recherchiert und ausgewählt. Sie sind in fünf Kategorien gruppiert: Lokal, Breit, Branche & Expertise, Nische und Marke.',
        },
        {
          'text': 'So kombinierst du sie: Die meisten deiner Hashtags sollten aus dem Bereich Branche & Expertise kommen – diese liegen im Sweet Spot mit 20.000-300.000 Nutzungen (mittleres Volumen). Sie sind populär genug, um bemerkt zu werden, aber spezifisch genug, um die richtigen Leute zu erreichen. Füge 1-2 NischenHashtags (unter 20.000 Nutzungen) hinzu, um Personen zu erreichen, die nach genau deinem Service suchen, und 1-2 breite Hashtags (über 300.000 Nutzungen), um dem Algorithmus deine übergeordnete Kategorie zu vermitteln.',
        },
        {
          'text': 'Lokale Hashtags solltest du unabhängig vom Volumen nutzen – sie sind essenziell, um Kunden in deiner Nähe anzuziehen. Dasselbe gilt für MarkenHashtags – nutze deine konsistent, um Wiedererkennung aufzubauen und Kundenbeiträge zu fördern.',
        },
        {
          'text': 'Wähle 3-5 Hashtags pro Beitrag, rotiere sie je nach Inhalt und tracke, welche am besten performen. Das Ziel ist nicht, jeden verfügbaren Hashtag zu nutzen sondern die spezifischen, die den richtigen Leuten helfen, dich zu finden.',
        },
      ],
    },
  ],
}

const DEFAULT_PLATFORM_GUIDELINES = {
  en: [
    {
      'title': 'Platform-Specific Best Practices',
      'items': [
        {
          'subtitle': 'Instagram',
          'text': 'Use 3-5 relevant hashtags per post—mix niche, industry, location, and one broader tag if it fits. Place them in your caption. Stories allow up to 10 hashtags, but keep them minimal and relevant. Reels benefit from trending hashtags, but only if they genuinely match your content.',
        },
        {
          'subtitle': 'Facebook',
          'text': 'Facebook\'s algorithm favours text-first, conversational posts, and hashtags play a smaller role here. Stick to 1-2 targeted hashtags per post. They work best in community groups or event posts rather than on business pages.',
        },
        {
          'subtitle': 'X (formerly Twitter)',
          'text': 'Posts with one to two hashtags get 21% higher engagement than those with three or more. Keep hashtags short—under 11 characters perform best. X moves fast, so use trending and timely hashtags to join real-time conversations. Check the trending tab regularly to see what\'s gaining traction in your region.',
        },
        {
          'subtitle': 'TikTok',
          'text': 'TikTok thrives on trending and niche hashtags. Keep an eye on the Discover page to catch rising trends early. Use a mix of trending tags, industry tags specific to your audience, and one or two broader category tags. Aim for 4-6 hashtags per video. Add them to your caption—they help the algorithm categorise and distribute your content.',
        },
        {
          'subtitle': 'LinkedIn',
          'text': 'LinkedIn hashtags work more like topic filters than discovery tools. Use 3-5 relevant, professional hashtags that match your industry and content theme. Industry tags often perform better than generic ones. Place them at the end of your post.',
        },
        {
          'subtitle': 'YouTube',
          'text': 'YouTube\'s golden rule is 3-5 hashtags per video. Only the first three in your description appear above your video title, so prioritise those carefully. Mix broad category tags with specific ones to balance discoverability with relevance. If you use more than 15 hashtags, YouTube ignores all of them.',
        },
      ],
    },
  ],
  de: [
    {
      'title': 'Plattform-spezifische Best Practices',
      'items': [
        {
          'subtitle': 'Instagram',
          'text': 'Nutze 3-5 relevante Hashtags pro Beitrag – mixe Nische, Branche, Standort und einen breiteren Tag, wenn er passt. Platziere sie in deiner Caption. Stories erlauben bis zu 10 Hashtags, aber halte sie minimal und relevant. Reels profitieren von Trend-Hashtags, aber nur, wenn sie wirklich zu deinem Inhalt passen.',
        },
        {
          'subtitle': 'Facebook',
          'text': 'Facebooks Algorithmus bevorzugt textorientierte, dialogische Beiträge, und Hashtags spielen hier eine kleinere Rolle. Bleibe bei 1-2 gezielten Hashtags pro Beitrag. Sie funktionieren besser in Community-Gruppen oder Event-Posts als auf Unternehmensseiten.',
        },
        {
          'subtitle': 'X (ehemals Twitter)',
          'text': 'Beiträge mit ein bis zwei Hashtags erhalten 21% mehr Engagement als die mit drei oder mehr. Halte Hashtags kurz – unter 11 Zeichen performt am besten. X bewegt sich schnell, also nutze Trend- und zeitnahe Hashtags, um an EchtzeitGesprächen teilzunehmen. Prüfe regelmäßig den Trending-Tab, um zu sehen, was in deiner Region an Fahrt gewinnt.',
        },
        {
          'subtitle': 'TikTok',
          'text': 'TikTok lebt von Trend- und Nischen-Hashtags. Behalte die Discover-Seite im Auge, um aufkommende Trends früh zu erwischen. Nutze einen Mix aus TrendTags, branchenspezifischen Tags für deine Zielgruppe und ein oder zwei breiteren Kategorie-Tags. Strebe 4-6 Hashtags pro Video an. Füge sie zu deiner Caption hinzu – sie helfen dem Algorithmus, dein Video zu kategorisieren und zu verteilen.',
        },
        {
          'subtitle': 'LinkedIn',
          'text': 'LinkedIn-Hashtags funktionieren eher wie Themenfilter als Discovery-Tools. Nutze 3-5 relevante, professionelle Hashtags, die zu deiner Branche und deinem Content-Thema passen. Branchen-Tags performen oft besser als generische. Platziere sie am Ende deines Beitrags.',
        },
        {
          'subtitle': 'YouTube',
          'text': 'YouTubes goldene Regel sind 3-5 Hashtags pro Video. Nur die ersten drei in deiner Beschreibung erscheinen über deinem Videotitel, also priorisiere diese sorgfältig. Mixe breite Kategorie-Tags mit spezifischen, um Auffindbarkeit mit Relevanz auszubalancieren. Wenn du mehr als 15 Hashtags nutzt, ignoriert YouTube alle.',
        },
      ],
    },
  ],
}

const getDefaultGuidelines = (lang = 'en') => {
  return [...(DEFAULT_GUIDELINES[lang] || DEFAULT_GUIDELINES.en), ...(DEFAULT_PLATFORM_GUIDELINES[lang] || DEFAULT_PLATFORM_GUIDELINES.en)]
}

const loading = ref(true)
const copying = ref(false)
const copyMessage = ref('')
let copyTimer = null
const title = ref('')
const introTitle = ref('')
const userLanguage = ref('en') // Default to English
const introDescription = ref(DEFAULT_INTRO_DESCRIPTION)
const guidelines = ref(getDefaultGuidelines('en'))
const hashtagBlocks = ref([])

const topGuidelines = computed(() =>
  (guidelines.value || []).filter(
    (section) => section?.title !== 'Platform-Specific Best Practices' && section?.title !== 'Plattform-spezifische Best Practices'
  )
)

const platformGuidelines = computed(() =>
  (guidelines.value || []).filter(
    (section) => section?.title === 'Platform-Specific Best Practices' || section?.title === 'Plattform-spezifische Best Practices'
  )
)

const headerTitle = computed(() => {
  if (introTitle.value && introTitle.value.trim().length > 0) {
    return introTitle.value
  }

  return title.value && title.value.trim().length > 0
    ? `Hashtag Cheat-Sheet for ${title.value}`
    : 'Hashtag Cheat-Sheet'
})

const fetchPlanData = async () => {
  try {
    const { data } = await axios.get('/api/plans')
    if (data?.success && Array.isArray(data?.plans) && data.plans.length > 0) {
      const plan = data.plans[0]
      title.value = plan?.title || title.value
      userLanguage.value = plan?.language || 'en'
    }
  } catch (_) {
  }
}

const fetchHashtags = async () => {
  loading.value = true
  try {
    await fetchPlanData()
    
    const { data } = await axios.get('/api/hashtags')
    if (data?.success && data?.data) {
      introTitle.value = data.data.intro_title || ''
      introDescription.value = data.data.intro_description || (LOCALES[userLanguage.value]?.introDescription || LOCALES.en.introDescription)
      guidelines.value = Array.isArray(data.data.guidelines) && data.data.guidelines.length > 0 
        ? data.data.guidelines 
        : getDefaultGuidelines(userLanguage.value)
      title.value = data.data.title || ''
      
      const blocks = Array.isArray(data?.data?.hashtag_blocks) ? data.data.hashtag_blocks : null
      if (Array.isArray(blocks) && blocks.length > 0) {
        hashtagBlocks.value = blocks.map(block => {
          let blockType = null
          if (block.title?.includes('Local') || block.title?.includes('Lokal')) blockType = 'local'
          else if (block.title?.includes('Broad') || block.title?.includes('Breit')) blockType = 'broad'
          else if (block.title?.includes('Industry') || block.title?.includes('Branche')) blockType = 'industry'
          else if (block.title?.includes('Niche') || block.title?.includes('Nische')) blockType = 'niche'
          else if (block.title?.includes('Branded') || block.title?.includes('Marke')) blockType = 'branded'
          
          if ((!block.description || block.description === null) && blockType && LOCALES[userLanguage.value]?.blockDescriptions?.[blockType]) {
            return { ...block, description: LOCALES[userLanguage.value].blockDescriptions[blockType] }
          }
          return block
        })
        return
      }

      const tags = Array.isArray(data?.data?.tags) ? data.data.tags : []
      if (tags.length > 0) {
        const localTags = tags.filter(tag =>
          tag.includes('Ireland') || tag.includes('Dublin') || tag.includes('Cork') || tag.includes('Galway') ||
          tag.includes('UK') || tag.includes('London') || tag.includes('Manchester') || tag.includes('Birmingham') ||
          tag.includes('DE') || tag.includes('Berlin') || tag.includes('Munich') || tag.includes('Hamburg')
        )
        const industryTags = tags.filter(tag =>
          !(
            tag.includes('Ireland') || tag.includes('Dublin') || tag.includes('Cork') || tag.includes('Galway') ||
            tag.includes('UK') || tag.includes('London') || tag.includes('Manchester') || tag.includes('Birmingham') ||
            tag.includes('DE') || tag.includes('Berlin') || tag.includes('Munich') || tag.includes('Hamburg')
          )
        )

        hashtagBlocks.value = []
        if (localTags.length > 0) {
          hashtagBlocks.value.push({
            title: '1 - Local',
            description: 'These tags help reach people searching for local services. Use at least one location-based hashtag per post, even if the volume is low—they\'re crucial for connecting with local customers ready to book.',
            tags: localTags
          })
        }
        if (industryTags.length > 0) {
          hashtagBlocks.value.push({
            title: '2 - Industry & Expertise',
            description: 'These hashtags help you reach people interested in your specific services and expertise. They have medium volume (20,000-300,000 uses) and are perfect for most posts.',
            tags: industryTags
          })
        }
        return
      }

        hashtagBlocks.value = []
    } else {
      await fetchPlanData() // Ensure we have language
      introTitle.value = ''
      introDescription.value = LOCALES[userLanguage.value]?.introDescription || LOCALES.en.introDescription
      guidelines.value = getDefaultGuidelines(userLanguage.value)
      title.value = ''
      hashtagBlocks.value = []
    }
  } catch (e) {
      await fetchPlanData() // Ensure we have language
      introTitle.value = ''
      introDescription.value = LOCALES[userLanguage.value]?.introDescription || LOCALES.en.introDescription
      guidelines.value = getDefaultGuidelines(userLanguage.value)
    title.value = ''
    hashtagBlocks.value = []
  } finally {
    loading.value = false
  }
}

const copyText = async (text) => {
  try {
    await navigator.clipboard.writeText(text)
    handleCopyFeedback()
  } catch (e) {
    const ta = document.createElement('textarea')
    ta.value = text
    document.body.appendChild(ta)
    ta.select()
    document.execCommand('copy')
    document.body.removeChild(ta)
    handleCopyFeedback()
  }
}

const isBrandedFormat = (tags) => {
  return Array.isArray(tags) && tags.length > 0 && typeof tags[0] === 'object' && tags[0].hasOwnProperty('tag')
}

const copyBlock = (tags) => {
  if (!tags.length) return
  const tagsToCopy = isBrandedFormat(tags) 
    ? tags.map(item => item.tag || item).join(' ')
    : tags.join(' ')
  copyText(tagsToCopy)
}

const handleCopyFeedback = () => {
  copying.value = true
  copyMessage.value = LOCALES[userLanguage.value]?.copyMessage || LOCALES.en.copyMessage

  if (copyTimer) {
    clearTimeout(copyTimer)
  }

  copyTimer = setTimeout(() => {
    copying.value = false
    copyMessage.value = ''
    copyTimer = null
  }, 1500)
}

onMounted(fetchHashtags)
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
<style>
.toast-container {
  pointer-events: none;
}
</style>


