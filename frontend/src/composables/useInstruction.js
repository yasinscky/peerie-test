import { slugToKind, worksheetKindFromTitle } from '@/data/worksheets'

const STEP_HEADING = /^(step|schritt)\s*\d+/i
const ANSWER_HEADER = /^(your answers?|dein(?:e)? antwort(?:en)?)$/i

export function stripAnswerTables(rawHtml) {
  const html = String(rawHtml || '')
  if (!html || !/<table/i.test(html)) return html

  const doc = new DOMParser().parseFromString(html, 'text/html')
  ;[...doc.querySelectorAll('table')].forEach((table) => {
    const header = [...(table.rows[0]?.cells || [])].map((cell) => cell.textContent.replace(/\u00a0/g, ' ').trim())
    if (!header.some((text) => ANSWER_HEADER.test(text))) return

    let heading = null
    let cursor = table.previousSibling
    while (cursor) {
      if (cursor.nodeType === 1 && /^H[1-3]$/.test(cursor.tagName)) {
        heading = cursor
        break
      }
      cursor = cursor.previousSibling
    }

    let current = heading || table
    while (current) {
      const next = current.nextSibling
      const isTable = current === table
      current.remove()
      if (isTable) break
      current = next
    }
  })

  return doc.body.innerHTML
}

const emptyParsed = (html = '') => ({
  titleHtml: '',
  introHtml: html,
  steps: [],
  outroHtml: '',
  hasPages: false,
})

function snapshotCell(cell) {
  return {
    tag: cell.tagName.toLowerCase(),
    html: cell.innerHTML,
    style: cell.getAttribute('style') || '',
    className: cell.className || '',
    align: cell.getAttribute('align') || '',
  }
}

function cellIsEmpty(html) {
  return String(html || '')
    .replace(/&nbsp;|\u00a0/gi, '')
    .replace(/<[^>]*>/g, '')
    .trim() === ''
}

function repairInstructionTables(doc) {
  doc.querySelectorAll('table').forEach((table) => {
    const rows = [...table.rows]
    if (!rows.length) return
    const spanned = [...table.querySelectorAll('td, th')].some((cell) => {
      return Number(cell.getAttribute('rowspan') || 1) > 1 || Number(cell.getAttribute('colspan') || 1) > 1
    })
    const uneven = new Set(rows.map((row) => row.cells.length)).size > 1
    if (!spanned && !uneven) return

    const grid = []
    rows.forEach((row, rowIndex) => {
      let column = 0
      ;[...row.cells].forEach((cell) => {
        while (grid[rowIndex]?.[column]) column += 1
        const rowspan = Math.max(1, Number(cell.getAttribute('rowspan') || 1))
        const colspan = Math.max(1, Number(cell.getAttribute('colspan') || 1))
        for (let rowOffset = 0; rowOffset < rowspan; rowOffset += 1) {
          for (let columnOffset = 0; columnOffset < colspan; columnOffset += 1) {
            if (!grid[rowIndex + rowOffset]) grid[rowIndex + rowOffset] = []
            grid[rowIndex + rowOffset][column + columnOffset] = rowOffset === 0 && columnOffset === 0
              ? snapshotCell(cell)
              : { tag: cell.tagName.toLowerCase(), html: '', style: '', className: '', align: '' }
          }
        }
        column += colspan
      })
    })

    const width = Math.max(0, ...grid.map((row) => row.length))
    const tbody = doc.createElement('tbody')
    grid.forEach((row) => {
      const tr = doc.createElement('tr')
      for (let column = 0; column < width; column += 1) {
        const snapshot = row[column] || { tag: 'td', html: '', style: '', className: '', align: '' }
        const cell = doc.createElement(snapshot.tag === 'th' ? 'th' : 'td')
        if (snapshot.style) cell.setAttribute('style', snapshot.style)
        if (snapshot.className) cell.className = snapshot.className
        if (snapshot.align) cell.setAttribute('align', snapshot.align)
        cell.innerHTML = cellIsEmpty(snapshot.html) ? '\u00a0' : snapshot.html
        tr.appendChild(cell)
      }
      tbody.appendChild(tr)
    })
    table.textContent = ''
    table.appendChild(tbody)
  })
}

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
    repairInstructionTables(doc)
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
