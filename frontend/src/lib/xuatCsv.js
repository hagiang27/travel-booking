export function xuatCsv(ten, hang) {
    if (!hang || !hang.length) return
    const cot = Object.keys(hang[0])
    const dong = [
      cot.join(','),
      ...hang.map((r) => cot.map((c) => `"${String(r[c] ?? '').replace(/"/g, '""')}"`).join(',')),
    ]
    // \uFEFF giúp Excel đọc đúng tiếng Việt
    const blob = new Blob(['\uFEFF' + dong.join('\n')], { type: 'text/csv;charset=utf-8' })
    const a = document.createElement('a')
    a.href = URL.createObjectURL(blob)
    a.download = ten
    a.click()
    URL.revokeObjectURL(a.href)
  }