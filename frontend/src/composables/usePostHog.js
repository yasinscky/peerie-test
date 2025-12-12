import posthog from 'posthog-js'

export function usePostHog() {
  const apiKey = import.meta.env.VITE_POSTHOG_API_KEY || 'phc_fJuoWPSDFqNQCFKgSuSUeDSC3Tnya3Wojg7ePXemkr1'
  const apiHost = import.meta.env.VITE_POSTHOG_HOST || 'https://eu.i.posthog.com'

  if (!posthog.__loaded) {
    posthog.init(apiKey, {
      api_host: apiHost,
      person_profiles: 'identified_only',
      capture_pageview: false,
      capture_pageleave: true,
      autocapture: true,
      session_recording: {
        maskAllInputs: true,
        maskTextSelector: '[data-ph-mask]',
        blockSelector: '[data-ph-block]',
        ignoreSelector: '[data-ph-ignore]',
        maskTextClass: 'ph-mask',
        blockClass: 'ph-block',
        ignoreClass: 'ph-ignore',
        recordCrossOriginIframes: false
      }
    })
  }

  window.posthog = posthog

  return { posthog }
}
