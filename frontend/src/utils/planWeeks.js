const SHORT_MONTHS = {
  en: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  de: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
}

export function weekNumberForDay(day) {
  const value = Number(day) || 1
  if (value <= 7) return 1
  if (value <= 14) return 2
  if (value <= 21) return 3
  return 4
}

export function currentPlanWeek(year, month, now = new Date()) {
  const selectedYear = Number(year)
  const selectedMonth = Number(month)
  if (now.getFullYear() === selectedYear && now.getMonth() + 1 === selectedMonth) {
    return weekNumberForDay(now.getDate())
  }

  const selectedStart = new Date(selectedYear, selectedMonth - 1, 1)
  const currentStart = new Date(now.getFullYear(), now.getMonth(), 1)
  return selectedStart > currentStart ? 1 : 4
}

export function weekBounds(year, month, week) {
  const lastDay = new Date(Number(year), Number(month), 0).getDate()
  const ranges = {
    1: [1, Math.min(7, lastDay)],
    2: [Math.min(8, lastDay), Math.min(14, lastDay)],
    3: [Math.min(15, lastDay), Math.min(21, lastDay)],
    4: [Math.min(22, lastDay), lastDay],
  }
  const [start, end] = ranges[week] || ranges[1]
  return { start, end }
}

export function formatWeekRange(year, month, week, language) {
  const { start, end } = weekBounds(year, month, week)
  const names = language === 'de' ? SHORT_MONTHS.de : SHORT_MONTHS.en
  const label = names[Number(month) - 1] || ''
  if (language === 'de') {
    return start === end ? `${start}. ${label}` : `${start}.–${end}. ${label}`
  }
  return start === end ? `${start} ${label}` : `${start}–${end} ${label}`
}

export function daysUntilWeekEnd(year, month, week, now = new Date()) {
  const { end } = weekBounds(year, month, week)
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
  const endDate = new Date(Number(year), Number(month) - 1, end)
  const diff = Math.round((endDate.getTime() - today.getTime()) / 86400000)
  return diff >= 0 ? diff + 1 : 0
}

export function comparePlanTasks(left, right) {
  const rank = (task) => {
    if (task?.frequency === 'weekly') return 0
    if (task?.frequency === 'bi_weekly') return 1
    return 2
  }
  const leftTask = left?.task || left
  const rightTask = right?.task || right
  const orderOf = (task) => {
    if (task?.global_order === null || task?.global_order === undefined || task?.global_order === '') {
      return 500
    }
    const value = Number(task.global_order)
    return Number.isFinite(value) ? value : 500
  }
  const rankDiff = rank(leftTask) - rank(rightTask)
  if (rankDiff !== 0) return rankDiff
  const orderDiff = orderOf(leftTask) - orderOf(rightTask)
  if (orderDiff !== 0) return orderDiff
  return (Number(leftTask?.id) || 0) - (Number(rightTask?.id) || 0)
}
