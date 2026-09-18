/**
 * Call the Appswithlove one-click-content-translation CP endpoint.
 * Requires appswithlove/statamic-one-click-content-translation in the host project.
 */
export async function translateText(sourceText, targetLang, labels = {}) {
  const trimmed = String(sourceText ?? '').trim()

  if (!trimmed) {
    Statamic.$toast?.error?.(labels.missingSource ?? 'Add a default-language value first.')
    return null
  }

  const cpRoot = Statamic.$config?.get('cpRoot') || '/cp'
  const endpoint = `${cpRoot}/one-click-content-translation`

  try {
    const response = await fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': Statamic.$config.get('csrfToken'),
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        url: `${window.location.pathname}${window.location.search}`,
        texts: [{ index: 0, html: trimmed }],
        lang: targetLang,
      }),
    })

    const contentType = response.headers.get('content-type') ?? ''

    if (!contentType.includes('application/json')) {
      throw new Error(labels.failed ?? 'Translation failed.')
    }

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data?.message || `HTTP ${response.status}`)
    }

    Statamic.$toast?.success?.('Done')

    return data?.texts?.[0]?.html ?? null
  } catch (error) {
    Statamic.$toast?.error?.(error?.message || labels.failed || 'Translation failed.')
    return null
  }
}
