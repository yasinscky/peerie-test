import { worksheetKindFromTitle } from '@/data/worksheets'

const STEP_HEADING = /^(step|schritt)\s*\d+/i

const emptyParsed = (html = '') => ({
  titleHtml: '',
  introHtml: html,
  steps: [],
  outroHtml: '',
  hasPages: false,
})

function applyHeadingClass(el, className) {
  if (el) {
    el.setAttribute('class', className)
  }
}

export function parseInstruction(rawHtml) {
  const html = String(rawHtml || '')
  if (!html) {
    return emptyParsed()
  }

  try {
    const parser = new DOMParser()
    const doc = parser.parseFromString(html, 'text/html')
    const body = doc.body

    const titleEl = body?.querySelector('h1')
    applyHeadingClass(titleEl, 'text-[28px] md:text-[32px] font-bold tracking-[-1.6px] text-black mb-4')
    const titleHtml = titleEl ? titleEl.outerHTML : ''

    const h2s = Array.from(body?.querySelectorAll('h2') || [])
    h2s.forEach((h2) => {
      applyHeadingClass(h2, 'text-[20px] md:text-[24px] font-bold tracking-[-1.2px] text-black mt-2 mb-3')
    })

    if (h2s.length === 0) {
      const clone = body.cloneNode(true)
      const cloneTitle = clone.querySelector('h1')
      if (cloneTitle) {
        cloneTitle.remove()
      }
      return {
        titleHtml,
        introHtml: clone.innerHTML || html,
        steps: [],
        outroHtml: '',
        hasPages: Boolean((clone.innerHTML || html).trim()),
      }
    }

    const preambleNodes = []
    let cursor = body.firstChild
    while (cursor && cursor !== h2s[0]) {
      if (cursor !== titleEl) {
        preambleNodes.push(cursor.cloneNode(true))
      }
      cursor = cursor.nextSibling
    }
    const preambleWrapper = doc.createElement('div')
    preambleNodes.forEach((node) => preambleWrapper.appendChild(node))

    const sections = h2s.map((h2, idx) => {
      const nodes = [h2]
      let node = h2.nextSibling
      const nextH2 = h2s[idx + 1] || null
      while (node && node !== nextH2) {
        nodes.push(node)
        node = node.nextSibling
      }
      const wrapper = doc.createElement('div')
      nodes.forEach((n) => wrapper.appendChild(n.cloneNode(true)))
      const headingText = String(h2.textContent || '').trim()
      return {
        headingText,
        html: wrapper.innerHTML,
        isStep: STEP_HEADING.test(headingText),
      }
    })

    const firstStepIndex = sections.findIndex((section) => section.isStep)
    if (firstStepIndex < 0) {
      return {
        titleHtml,
        introHtml: `${preambleWrapper.innerHTML}${sections.map((section) => section.html).join('')}`,
        steps: [],
        outroHtml: '',
        hasPages: true,
      }
    }

    const introHtml = `${preambleWrapper.innerHTML}${sections.slice(0, firstStepIndex).map((section) => section.html).join('')}`
    const steps = []
    const outroParts = []
    let stepsEnded = false

    sections.slice(firstStepIndex).forEach((section) => {
      if (!stepsEnded && section.isStep) {
        steps.push({
          title: section.headingText,
          html: section.html,
        })
        return
      }
      stepsEnded = true
      outroParts.push(section.html)
    })

    return {
      titleHtml,
      introHtml,
      steps,
      outroHtml: outroParts.join(''),
      hasPages: introHtml.trim().length > 0 || steps.length > 0 || outroParts.length > 0,
    }
  } catch {
    return emptyParsed(html)
  }
}

export function isBuyerPersonaTask(task) {
  return getTaskWorksheetKind(task) === 'buyer_persona'
}

export function getTaskWorksheetKind(task) {
  if (task?.document_key) return slugToKind(task.document_key)
  if (Array.isArray(task?.document_fields) && task.document_fields.length) {
    return slugToKind(task.document_key || `task-${task.id}`)
  }
  return worksheetKindFromTitle(task?.title)
}
