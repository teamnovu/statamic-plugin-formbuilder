// Shared display strings for the Form Builder fieldtypes.
//
// These are keyed off the *content* locale (meta.locale) of the submission
// being shown — not the CP user's UI locale — so a submission made on the
// German site reads in German for every viewer. Falls back to English, then
// to the key itself.
//
// Add a new locale by adding its handle to each entry; add a new string by
// adding a new top-level key.

const strings = {
  empty: {
    en: 'The user left this field empty',
    de: 'Der Benutzer hat dieses Feld leer gelassen',
    fr: 'L\'utilisateur a laissé ce champ vide',
  },
  noOptionsSelected: {
    en: 'No options selected',
    de: 'Keine Optionen ausgewählt',
    fr: 'Aucune option sélectionnée',
  },
}

export function trans(key, locale) {
  return strings[key]?.[locale] ?? strings[key]?.en ?? key
}
