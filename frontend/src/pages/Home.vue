<template>
  <div class="bg-white">
    <header class="header">
      <div class="container header__container">
        <div class="flex items-center justify-between">
          <router-link to="/" class="inline-flex items-center">
            <img :src="logoImage" alt="Peerie" class="h-[34px] md:h-[30px] lg:h-[51px] w-auto block">
          </router-link>

          <div class="hidden lg:flex items-center gap-6">
            <div class="h-[60px] rounded-[15px] border-2 border-[#DCDCDC] overflow-hidden inline-flex">
              <button
                type="button"
                class="px-4 font-bold uppercase text-[16px] transition-colors"
                :class="languageStore.language === 'en' ? 'bg-[#3F4369] text-white' : 'bg-white text-[#3F4369]'"
                @click="languageStore.setLanguage('en')"
              >
                EN
              </button>
              <button
                type="button"
                class="px-4 font-bold uppercase text-[16px] transition-colors"
                :class="languageStore.language === 'de' ? 'bg-[#3F4369] text-white' : 'bg-white text-[#3F4369]'"
                @click="languageStore.setLanguage('de')"
              >
                DE
              </button>
            </div>
            <router-link
              to="/login"
              class="h-[60px] w-[128px] rounded-[15px] border-2 border-[#3F4369] text-[#3F4369] inline-flex items-center justify-center font-bold uppercase text-[20px] hover:bg-[#3F4369] hover:text-white transition-colors"
            >
              {{ texts.login }}
            </router-link>
            <router-link
              to="/register"
              class="h-[60px] w-[198px] rounded-[15px] border-2 border-[#F34767] bg-[#F34767] text-white inline-flex items-center justify-center font-bold uppercase text-[20px] hover:bg-[#d93d5a] transition-colors"
            >
              {{ texts.getStarted }}
            </router-link>
          </div>

          <div class="hidden md:flex lg:hidden items-center gap-4">
            <div class="h-[40px] rounded-[10px] border-2 border-[#DCDCDC] overflow-hidden inline-flex">
              <button
                type="button"
                class="px-3 font-bold uppercase text-[12px] transition-colors"
                :class="languageStore.language === 'en' ? 'bg-[#3F4369] text-white' : 'bg-white text-[#3F4369]'"
                @click="languageStore.setLanguage('en')"
              >
                EN
              </button>
              <button
                type="button"
                class="px-3 font-bold uppercase text-[12px] transition-colors"
                :class="languageStore.language === 'de' ? 'bg-[#3F4369] text-white' : 'bg-white text-[#3F4369]'"
                @click="languageStore.setLanguage('de')"
              >
                DE
              </button>
            </div>
            <router-link
              to="/login"
              class="w-[40px] h-[40px] rounded-[10px] border-2 border-[#3F4369] inline-flex items-center justify-center text-[#3F4369]"
              aria-label="Account"
            >
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </router-link>
            <router-link
              to="/register"
              class="h-[50px] w-[160px] rounded-[10px] border-2 border-[#F34767] bg-[#F34767] text-white inline-flex items-center justify-center font-bold uppercase text-[18px] hover:bg-[#d93d5a] transition-colors"
            >
              {{ texts.getStarted }}
            </router-link>
          </div>

          <div class="md:hidden flex items-center gap-4">
            <div class="h-[40px] rounded-[10px] border-2 border-[#DCDCDC] overflow-hidden inline-flex">
              <button
                type="button"
                class="px-3 font-bold uppercase text-[12px] transition-colors"
                :class="languageStore.language === 'en' ? 'bg-[#3F4369] text-white' : 'bg-white text-[#3F4369]'"
                @click="languageStore.setLanguage('en')"
              >
                EN
              </button>
              <button
                type="button"
                class="px-3 font-bold uppercase text-[12px] transition-colors"
                :class="languageStore.language === 'de' ? 'bg-[#3F4369] text-white' : 'bg-white text-[#3F4369]'"
                @click="languageStore.setLanguage('de')"
              >
                DE
              </button>
            </div>
            <router-link
              to="/login"
              class="w-[40px] h-[40px] rounded-[10px] border-2 border-[#3F4369] inline-flex items-center justify-center text-[#3F4369]"
              aria-label="Account"
            >
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </router-link>
            <button
              type="button"
              class="w-[30px] h-[30px] inline-flex items-center justify-center text-[#1C1A1B]"
              aria-label="Menu"
              @click="mobileMenuOpen = !mobileMenuOpen"
            >
              <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path v-if="!mobileMenuOpen" d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path v-else d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>
        </div>

        <div v-if="mobileMenuOpen" class="md:hidden mt-6 rounded-[20px] border border-[#DCDCDC] bg-white shadow-lg overflow-hidden">
          <div class="p-4 flex flex-col gap-3 text-[#1C1A1B]">
            <router-link
              to="/register"
              class="mt-2 h-[50px] rounded-[10px] bg-[#F34767] text-white inline-flex items-center justify-center font-bold uppercase text-[18px]"
              @click="mobileMenuOpen = false"
            >
              {{ texts.getStarted }}
            </router-link>
            <router-link
              to="/login"
              class="h-[50px] rounded-[10px] border-2 border-[#3F4369] text-[#3F4369] inline-flex items-center justify-center font-bold uppercase text-[18px]"
              @click="mobileMenuOpen = false"
            >
              {{ texts.login }}
            </router-link>
          </div>
        </div>
      </div>
    </header>

    <main id="top" class="main">
      <section class="hero">
        <div class="section__container hero__container">
          <div class="hero__content">
            <div class="hero__content-main">
              <div class="hero__title">
                <h1>
                  {{ texts.heroTitlePrefix }}
                  <span class="hero__title-highlight">
                    <span class="hero__title-highlight-text">{{ texts.heroTitleHighlight }}</span>
                  </span>
                </h1>

                <p class="hero__subtitle">
                  {{ texts.heroSubtitle }}
                </p>

                <div class="hero__buttons">
                  <div class="hero__button button button-red">
                    <router-link
                      to="/register"
                    >
                      {{ texts.getStarted }}
                    </router-link>
                  </div>
                  <p class="text-[#3F4369] font-bold text-[16px] md:text-[16px] lg:text-[36px] leading-none hero__text">
                    <span class="underline">{{ texts.signUpUnderline }}</span> {{ texts.signUpSuffix }}
                  </p>
                </div>


              </div>

              <div class="hero__cubes" aria-hidden="true">
                <div
                  class="hero__cube hero__cube--yellow"
                  :class="{
                    'cube-pos-0': cubePositions.yellow === 0,
                    'cube-pos-1': cubePositions.yellow === 1,
                    'cube-pos-2': cubePositions.yellow === 2
                  }"
                ></div>
                <div
                  class="hero__cube hero__cube--purple"
                  :class="{
                    'cube-pos-0': cubePositions.purple === 0,
                    'cube-pos-1': cubePositions.purple === 1,
                    'cube-pos-2': cubePositions.purple === 2
                  }"
                ></div>
                <div
                  class="hero__cube hero__cube--teal"
                  :class="{
                    'cube-pos-0': cubePositions.teal === 0,
                    'cube-pos-1': cubePositions.teal === 1,
                    'cube-pos-2': cubePositions.teal === 2
                  }"
                ></div>

                <div class="hero__slider" aria-hidden="false">
                  <button
                    v-if="hasSliderInteracted"
                    type="button"
                    class="hero__slider-btn"
                    aria-label="Previous"
                    @click.stop.prevent="prevSlide"
                  >
                    <img :src="arrowRightIcon" alt="" class="hero__slider-icon hero__slider-icon--prev">
                  </button>
                  <button type="button" class="hero__slider-btn" aria-label="Next" @click.stop.prevent="nextSlide">
                    <img :src="arrowRightIcon" alt="" class="hero__slider-icon">
                  </button>
                </div>
              </div>
            </div>

          </div>
        </div>
      </section>

      <section class="features">
        <div class="features__container section__container">
          <h2 class="features__title title-2">{{ texts.featuresTitle }}</h2>

          <div class="features__content">

            <div class="features__cards">
              <div
                v-for="(item, index) in items"
                :key="index"
                class="features__card"
                :class="{ 'features__card--active': activeIndex === index }"
              />
            </div>

            <ul class="features__list">
              <li
                v-for="(item, index) in items"
                :key="index"
                ref="featureItems"
                class="features__item"
                :class="{ 'features__item--active': activeIndex === index }"
              >
                <h3 class="features__item-title title-3">{{ item.title }}</h3>
                <p class="features__item-text" v-html="item.text"></p>
              </li>
            </ul>
          </div>
        </div>
      </section>

      <section class="benefits">
        <div class="benefits__grid section__container">
          
          <article class="benefits__card benefits__card--red">
            <h3 class="benefits__title">{{ texts.benefits.redTitle }}</h3>
            <p class="benefits__text">
              {{ texts.benefits.redText }}
            </p>
            <span class="benefits__decor benefits__decor--red" />
          </article>

          <article class="benefits__card benefits__card--yellow">
            <h3 class="title-3">{{ texts.benefits.yellowTitle }}</h3>
            <p class="benefits__text">
              {{ texts.benefits.yellowText }}
            </p>
            <span class="benefits__decor benefits__decor--yellow" />
          </article>

          <article class="benefits__card benefits__card--grey">
            <h3 class="benefits__title">{{ texts.benefits.greyTitle }}</h3>
            <p class="benefits__text">
              {{ texts.benefits.greyText }}
            </p>
          </article>

          <article class="benefits__card benefits__card--green">
            <h3 class="benefits__title">{{ texts.benefits.greenTitle }}</h3>
            <p class="benefits__text">
              {{ texts.benefits.greenText }}
            </p>
            <span class="benefits__decor benefits__decor--green" />
          </article>

          <article class="benefits__card benefits__card--purple">
            <h3 class="title-3">{{ texts.benefits.purpleTitle }}</h3>
            <p class="benefits__text">
              {{ texts.benefits.purpleText }}
            </p>
            <span class="benefits__decor benefits__decor--purple" />
          </article>

        </div>
      </section>

      <section class="cta">
        <div class="container cta__inner">

          <div class="cta__button-mobile">
              <router-link
                to="/register"
                class="button button-white cta__button-link"
              >
                {{ texts.getStarted }}
              </router-link>
            </div>

          <div class="cta__content">
            <p class="cta__text">
              {{ texts.ctaText }}
            </p>
            <div class="cta__button">
              <router-link
                to="/register"
                class="button button-white cta__button-link"
              >
                {{ texts.getStarted }}
              </router-link>
            </div>
          </div>

        </div>
      </section>

      <section class="trusted">
        <div class="trusted__container section__container">
          <h2 class="trusted__title title-2">{{ texts.trustedTitlePrefix }} <span class="trusted__title-highlight">{{ texts.trustedTitleHighlight }}</span></h2>

          <div class="trusted__content">
            <div class="trusted__slider" ref="trustedSlider">
              <div class="trusted__grid">
              <div v-for="(text, index) in trustedItems" :key="index" class="trusted__card">
                <div class="trusted__card-visual">
                  <div
                    class="trusted__card-inner"
                    :class="{
                      'trusted__card-inner--1': index === 0,
                      'trusted__card-inner--2': index === 1,
                      'trusted__card-inner--3': index === 2,
                      'trusted__card-inner--4': index === 3
                    }"
                  ></div>
                </div>
                <p class="trusted__card-text">{{ text }}</p>
              </div>
              </div>
            </div>

            <button
              type="button"
              class="trusted__nav trusted__nav--prev"
              :class="{ 'trusted__nav--disabled': !canScrollTrustedPrev }"
              :disabled="!canScrollTrustedPrev"
              aria-label="Previous"
              @click="scrollTrusted(-1)"
            >
              <img :src="arrowRightIcon" alt="" class="trusted__nav-icon trusted__nav-icon--prev">
            </button>
            <button
              type="button"
              class="trusted__nav trusted__nav--next"
              :class="{ 'trusted__nav--disabled': !canScrollTrustedNext }"
              :disabled="!canScrollTrustedNext"
              aria-label="Next"
              @click="scrollTrusted(1)"
            >
              <img :src="arrowRightIcon" alt="" class="trusted__nav-icon">
            </button>
          </div>
        </div>
      </section>

      <section class="faq">
        <div class="faq__container section__container">
          <h2 class="faq__title title-2">
                {{ texts.faqTitlePrefix }}
                <span class="faq__title-brand">Peerie</span>
                {{ texts.faqTitleMiddle }} <span class="faq__title-underlined">{{ texts.faqTitleUnderlined }}</span>
          </h2>
          <div class="faq__content">
            <div class="faq__left">
              <div class="faq__decor faq__decor--mint"></div>
              <div class="faq__decor faq__decor--gray"></div>
            </div>

            <div class="faq__right">
              <div class="faq__list">
                <div
                  v-for="(item, index) in faqItems"
                  :key="index"
                  class="faq__item"
                  :class="{ 'faq__item--open': item.isOpen }"
                >
                  <button type="button" class="faq__item-header" @click="toggleFaq(index)">
                    <span class="faq__item-question" v-html="item.question"></span>
                    <span class="faq__item-toggle" aria-hidden="true">
                      <svg class="faq__chevron" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                    </span>
                  </button>

                  <div v-if="item.isOpen" class="faq__answer">
                    <div class="faq__answer-divider"></div>
                    <p class="faq__answer-text">{{ item.answer }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="trial">
        <div class="trial__container section__container">
          <h2 class="trial__title title-2">
            {{ texts.trialTitlePrefix }} <span class="trial__title-accent">{{ texts.trialTitleAccent }}</span>
          </h2>
          <div class="trial__content">

            <p class="trial__subtitle">{{ texts.trialSubtitle }}</p>
            <router-link to="/register" class="trial__button">{{ texts.getStarted }}</router-link>
          </div>
        </div>
      </section>

      

    </main>
  </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import logoImage from '@/assets/images/logos/logo.svg'
import arrowRightIcon from '@/assets/images/icons/home-page/arrow-right.svg'
import { useLanguageStore } from '@/stores/language'

const mobileMenuOpen = ref(false)
const languageStore = useLanguageStore()

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      login: 'Anmelden',
      getStarted: 'Loslegen',
      heroTitlePrefix: 'Digitales Marketing,',
      heroTitleHighlight: 'einfach',
      heroSubtitle: 'Mit einer Plattform, die kleine Unternehmen stärkt',
      signUpUnderline: 'Registriere dich',
      signUpSuffix: 'für Early Access',
      featuresTitle: 'Was ist drin?',
      benefits: {
        redTitle: 'Fokussiere dich auf das, was für dein Business funktioniert',
        redText: 'Erhalte einen Schritt-für-Schritt-Marketingplan, der zu deinem Business passt – ohne Standard-Vorlagen. Du weißt, womit du startest und was du jede Woche tun solltest, um echtes Wachstum zu sehen.',
        yellowTitle: 'Umsatz steigern',
        yellowText: 'Erreiche Kund:innen dort, wo sie bereits sind, und führe sie zum Kauf.',
        greyTitle: 'Bleib konsequent',
        greyText: 'Werde Teil einer Community von kleinen Unternehmen mit ähnlichen Herausforderungen. Teile Feedback, tausche Tipps aus und bleib dran – Marketing funktioniert besser, wenn du es nicht allein machst.',
        greenTitle: 'Keine Nächte mehr für Marketing opfern',
        greenText: 'Du brauchst zusätzliche Unterstützung? Unser Expertenteam übernimmt Webentwicklung oder bietet individuelles Consulting, damit du dich auf dein Business konzentrierst.',
        purpleTitle: 'Zeit sparen & produktiver werden',
        purpleText: 'Erstelle Inhalte in Minuten mit branchenspezifischen Ideen und Vorlagen – und bleib produktiv ohne Burnout.'
      },
      ctaText: 'Keine generischen Templates. Optimierte Strategie und Inhalte speziell für dein Business.',
      trustedTitlePrefix: 'Vertraut von',
      trustedTitleHighlight: 'kleinen Unternehmen',
      faqTitlePrefix: 'Fragst du dich noch, ob',
      faqTitleMiddle: 'das Richtige',
      faqTitleUnderlined: 'für dich ist?',
      trialTitlePrefix: 'Teste Peerie',
      trialTitleAccent: 'kostenlos!',
      trialSubtitle: 'Starte deinen 7‑Tage GRATIS‑Test – jederzeit kündbar!'
    }
  }

  return {
    login: 'Log in',
    getStarted: 'Get started',
    heroTitlePrefix: 'Digital marketing made',
    heroTitleHighlight: 'Simple',
    heroSubtitle: 'With a platform built to empower small businesses',
    signUpUnderline: 'Sign up',
    signUpSuffix: 'for early access',
    featuresTitle: "What’s Inside?",
    benefits: {
      redTitle: 'Focus on What Works for Your Business',
      redText: 'Receive a step‑by‑step marketing plan tailored to your business—no cookie‑cutter playbooks. Know where to start and what to do each week to see real growth.',
      yellowTitle: 'Generate Revenue',
      yellowText: 'Reach clients where they already are and guide them to purchase.',
      greyTitle: 'Stay Consistent',
      greyText: 'Join a community of fellow small‑business owners solving the same challenges. Share feedback, swap tips and get accountability—because marketing works best when you’re not doing it alone.',
      greenTitle: 'Stop Losing Nights to Marketing',
      greenText: 'Need more hands on deck? Our expert team can handle website development or provide individual consulting, so you stay focused on running your business.',
      purpleTitle: 'Save Time & Increase Productivity',
      purpleText: 'Create content in minutes using industry-specific ideas and templates and stay productive without a burnout.'
    },
    ctaText: 'No generic templates. Optimised strategy and content specifically for your business.',
    trustedTitlePrefix: 'Trusted by',
    trustedTitleHighlight: 'Small Businesses',
    faqTitlePrefix: 'Still wondering if',
    faqTitleMiddle: 'right',
    faqTitleUnderlined: 'for you?',
    trialTitlePrefix: 'Try Peerie',
    trialTitleAccent: 'for free!',
    trialSubtitle: 'Start your 7-day FREE trial — cancel anytime!'
  }
})

const hasSliderInteracted = ref(false)
const cubePositions = ref({
  yellow: 0,
  purple: 1,
  teal: 2
})

const rotateCubes = (delta) => {
  cubePositions.value = {
    yellow: (cubePositions.value.yellow + delta + 3) % 3,
    purple: (cubePositions.value.purple + delta + 3) % 3,
    teal: (cubePositions.value.teal + delta + 3) % 3
  }
}

const nextSlide = () => {
  hasSliderInteracted.value = true
  rotateCubes(-1)
}

const prevSlide = () => {
  rotateCubes(1)
}

const faqItems = ref([])

const getFaqItemsByLanguage = () => {
  if (languageStore.language === 'de') {
    return [
      {
        question: 'Du weißt, dass digitales Marketing mehr Umsatz bringen kann, aber du weißt nicht, wo du anfangen sollst?',
        answer: 'Wir geben dir eine Strategie, die zu deinem Business passt, damit du jede Woche genau weißt, was zu tun ist.'
      },
      {
        question: 'Du liebst, was du tust, und willst mehr Zeit mit Kund:innen verbringen statt Stunden mit Content?',
        answer: 'Wir liefern Plug‑and‑Play‑Vorlagen und Content‑Ideen für deine Branche, damit du Zeit sparst.'
      },
      {
        question: 'Du würdest gern regelmäßig posten, aber du hast keine Idee, was?',
        answer: 'Wir bringen dir frische Content‑Ideen für Social, E‑Mail und Website – passend zu deiner Nische.'
      },
      {
        question: 'Du hast digitales Marketing schon ausprobiert, aber aufgegeben, bevor du Ergebnisse gesehen hast?',
        answer: 'Wir machen digitales Marketing leicht umsetzbar und helfen dir, dranzubleiben.'
      },
      {
        question: 'Du postest schon wie ein Profi, willst aber weitere Bereiche des digitalen Marketings erkunden?',
        answer: 'Wir helfen dir beim Start von E‑Mail‑Kampagnen, der Optimierung für Local SEO und dem Aufbau deiner Website.'
      },
      {
        question: 'Du hast ganz andere Herausforderungen beim digitalen Marketing?',
        answer: 'Erzähl uns von deinem Business und wir passen Plan und Ressourcen an deine Ziele und Rahmenbedingungen an.'
      }
    ]
  }

  return [
    {
      question: "You realise that digital marketing can generate more revenue, but don’t know where to start?",
      answer: 'We provide you with a strategy tailored to your specific business, so you know what to do each week.'
    },
    {
      question: 'You love what you do and want to spend more time with your customers instead of spending hours on content?',
      answer: 'We deliver plug-and-play templates and content ideas for your industry to save you time.'
    },
    {
      question: 'You would love to post regularly, but have no idea what to share?',
      answer: 'We bring you fresh content ideas tailored to your niche across social, email, and website.'
    },
    {
      question: 'You have tried digital marketing before, but had given up before you saw any results?',
      answer: 'We make digital marketing easy to run and help you stay on track.'
    },
    {
      question: "You're already posting like a pro, but want to explore other aspects of digital marketing?",
      answer: 'We help you start your email marketing campaigns, optimise for local SEO, and build your website.'
    },
    {
      question: 'You have entirely different challenges when it comes to digital marketing?',
      answer: 'Tell us about your business and we will tailor your plan and resources around your goals and constraints.'
    }
  ]
}

const syncFaqItems = () => {
  faqItems.value = getFaqItemsByLanguage().map((item) => ({
    ...item,
    isOpen: false
  }))
}

syncFaqItems()

const toggleFaq = (index) => {
  const willOpen = !faqItems.value[index].isOpen
  faqItems.value = faqItems.value.map((item, i) => ({
    ...item,
    isOpen: i === index ? willOpen : false
  }))
}

const trustedItems = ref([
  'Lorem ipsum dolor sit amet consectetur.',
  'Lorem ipsum dolor sit amet consectetur.',
  'Lorem ipsum dolor sit amet consectetur.',
  'Lorem ipsum dolor sit amet consectetur.'
])

const trustedSlider = ref(null)
const canScrollTrustedPrev = ref(false)
const canScrollTrustedNext = ref(false)

const updateTrustedNav = () => {
  const el = trustedSlider.value
  if (!el) return

  const maxLeft = el.scrollWidth - el.clientWidth
  canScrollTrustedPrev.value = el.scrollLeft > 1
  canScrollTrustedNext.value = el.scrollLeft < maxLeft - 1
}

const getTrustedStep = () => {
  const el = trustedSlider.value
  if (!el) return 0
  const card = el.querySelector('.trusted__card')
  const grid = el.querySelector('.trusted__grid')
  if (!card || !grid) return 0

  const cardWidth = card.getBoundingClientRect().width
  const style = window.getComputedStyle(grid)
  const gap = parseFloat(style.columnGap || style.gap || '0') || 0
  return Math.max(1, Math.round(cardWidth + gap))
}

const scrollTrusted = (direction) => {
  const el = trustedSlider.value
  if (!el) return
  const step = getTrustedStep()
  el.scrollBy({ left: step * direction, behavior: 'smooth' })
}

const whyItems = computed(() => {
  if (languageStore.language === 'de') {
    return [
      { title: 'Einfach & bezahlbar', description: 'Abo-Preise machen Kosten planbar – mit Add-ons, wenn du Expert:innen brauchst.' },
      { title: 'Individuelle Lösung', description: 'Ressourcen für deine Branche, damit du keine Stunden mit generischen Templates verlierst.' },
      { title: 'Ständig aktuell', description: 'Vorlagen und Strategien werden laufend an Trends und Best Practices angepasst.' },
      { title: 'Mehr als Templates', description: 'Peerie kombiniert Plug‑and‑Play‑Content mit Schritt‑für‑Schritt‑Guidance.' }
    ]
  }
  return [
    { title: 'Easy & Affordable', description: 'Subscription-based pricing keeps costs predictable, with add-on services when you need an expert.' },
    { title: 'Tailored Solution', description: "Resources are built for your industry, so you don't waste hours adapting generic templates." },
    { title: 'Constantly Evolving', description: 'Templates and strategies are updated to match new trends and best practices.' },
    { title: 'More Than Just Templates', description: 'Peerie combines plug-and-play content with step-by-step guidance.' }
  ]
})

const howItems = computed(() => {
  if (languageStore.language === 'de') {
    return [
      { number: 1, title: 'Erzähl uns von deinem Business', description: 'Registriere dich und beantworte ein paar kurze Fragen. Je mehr du teilst, desto besser wird dein Plan.', desktopOnly: false },
      { number: 2, title: 'Erhalte deinen individuellen Plan', description: 'Wir verwandeln deine Antworten in eine klare Roadmap und Ressourcen passend zu deinen Zielen.', desktopOnly: false },
      { number: 3, title: 'Öffne deinen Workspace', description: 'Greife auf Plan, Templates und Workbooks zu – abgestimmt auf Branche und Land.', desktopOnly: true },
      { number: 4, title: 'Folge dem Plan', description: 'Öffne jede Woche die Checkliste und erledige die Aufgaben in der richtigen Reihenfolge.', desktopOnly: true },
      { number: 5, title: 'Bleib dran', description: 'Dein Plan wird monatlich mit neuen Ideen und Vorlagen aktualisiert – Konsistenz wird dadurch leichter.', desktopOnly: true }
    ]
  }

  return [
    { number: 1, title: 'Tell us about your business', description: 'Register and answer a few quick questions. The more you share, the sharper your plan.', desktopOnly: false },
    { number: 2, title: 'Receive a custom plan', description: 'We turn your answers into a clear roadmap and resources tailored to your goals.', desktopOnly: false },
    { number: 3, title: 'Open your workspace', description: 'Access your plan, templates, and workbooks tuned to your industry and country.', desktopOnly: true },
    { number: 4, title: 'Follow the plan', description: 'Each week, open the checklist and deliver the tasks in the right order.', desktopOnly: true },
    { number: 5, title: 'Keep momentum', description: 'Your plan refreshes monthly with new ideas and templates, so staying consistent stays easy.', desktopOnly: true }
  ]
})



const items = computed(() => {
  if (languageStore.language === 'de') {
    return [
      { title: 'Marketingstrategie & Umsetzungsplan', text: 'abgestimmt auf dein Business' },
      { title: 'Content‑Vorlagen', text: 'inklusive sofort nutzbarer Posts' },
      { title: 'Frische Content‑Ideen', text: 'regelmäßig aktualisiert für deine Branche' },
      { title: 'Bildbibliothek', text: '1000 kostenlose Stock‑Bilder' },
      { title: 'Content‑Kalender', text: 'zum Planen, Terminieren und Organisieren' },
      { title: 'Hashtag‑Bibliothek', text: 'kuratierte Hashtags für deine Nische' },
      { title: 'Peer‑Community', text: 'für Austausch und Business‑Chancen' }
    ]
  }

  return [
    { title: 'Digital marketing strategy & execution plan', text: 'tailored to your business' },
    { title: 'Content templates', text: 'including ready-to-use posts' },
    { title: 'Fresh content ideas', text: 'updated regularly for your industry' },
    { title: 'Image library', text: '1000 free stock images' },
    { title: 'Content calendar', text: 'to plan, schedule, and stay organised' },
    { title: 'Hashtag library', text: 'handpicked and proved for your niche' },
    { title: 'Peer community', text: 'for experience exchange and business opportunities' }
  ]
})

const activeIndex = ref(0)
const featureItems = ref([])

let observer = null
let activeRafId = null
let lastActiveChangeAt = 0
const ACTIVE_CHANGE_COOLDOWN_MS = 140

const setupFeatureObserver = async () => {
  await nextTick()
  if (observer) observer.disconnect()

  observer = new IntersectionObserver(
    (entries) => {
      const visible = entries
        .filter((entry) => entry.isIntersecting)
        .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0]

      if (!visible) return

      const index = featureItems.value.indexOf(visible.target)
      if (index === -1) return
      if (index === activeIndex.value) return

      const now = Date.now()
      if (now - lastActiveChangeAt < ACTIVE_CHANGE_COOLDOWN_MS) return

      if (activeRafId) cancelAnimationFrame(activeRafId)
      activeRafId = requestAnimationFrame(() => {
        activeIndex.value = index
        lastActiveChangeAt = Date.now()
      })
    },
    {
      threshold: 0.6
    }
  )

  featureItems.value.forEach(el => observer.observe(el))
}

onMounted(async () => {
  await nextTick()

  updateTrustedNav()
  if (trustedSlider.value) trustedSlider.value.addEventListener('scroll', updateTrustedNav, { passive: true })
  window.addEventListener('resize', updateTrustedNav, { passive: true })

  await setupFeatureObserver()
})

watch(
  () => languageStore.language,
  async () => {
    syncFaqItems()
    await setupFeatureObserver()
  }
)

onBeforeUnmount(() => {
  if (activeRafId) cancelAnimationFrame(activeRafId)
  if (observer) observer.disconnect()

  if (trustedSlider.value) trustedSlider.value.removeEventListener('scroll', updateTrustedNav)
  window.removeEventListener('resize', updateTrustedNav)
})
</script>
