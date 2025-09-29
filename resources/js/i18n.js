import { createI18n } from 'vue-i18n';

const i18n = createI18n({
  legacy: false, // Use Composition API mode
  globalInjection: true, // Allow $t to be used globally
  locale: 'en', // Default locale
  fallbackLocale: 'en', // Fallback locale
  messages: {}, // Initial empty messages
});

const files = import.meta.glob('/lang/*/*.json', { eager: false });

/**
 * Load locale messages dynamically.
 * @param {string} locale - The locale to load messages for.
 */

async function loadLocaleMessages(locale) {
  try {
    const errorMessagesResponse = await fetch(`/lang/${locale}/error_messages.json`);
    const errorMessages = await errorMessagesResponse.json();
    const pagesNamesResponse = await fetch(`/lang/${locale}/pages_names.json`);
    const pagesNames = await pagesNamesResponse.json();
    const successMessagesResponse = await fetch(`/lang/${locale}/success_messages.json`);
    const successMessages = await successMessagesResponse.json();
    const viewPages_1Response = await fetch(`/lang/${locale}/view_pages_1.json`);
    const viewPages_1 = await viewPages_1Response.json();
    const viewPages_2Response = await fetch(`/lang/${locale}/view_pages_2.json`);
    const viewPages_2 = await viewPages_2Response.json();
    const viewPages_3Response = await fetch(`/lang/${locale}/view_pages_3.json`);
    const viewPages_3 = await viewPages_3Response.json();

    // ... fetch other files similarly

    const combinedMessages = {
      ...errorMessages,
      ...pagesNames,
      ...successMessages,
      ...viewPages_1,
      ...viewPages_2,
      ...viewPages_3,

    };

    i18n.global.setLocaleMessage(locale, combinedMessages);
  } catch (error) {
    console.error(`Error loading messages for locale "${locale}":`, error);
  }
}

/**
 * Initialize i18n with the specified locale.
 * @param {string} locale - The locale to initialize.
 */
async function initI18n(locale) {
  await loadLocaleMessages(locale); // Load the messages
  i18n.global.locale.value = locale; // Set the locale
}

/**
 * Handle dynamic locale changes reactively.
 */
function setupLocaleReactivity() {
  const localeWatcher = new MutationObserver(async () => {
    const currentLocale = i18n.global.locale.value;
    if (!i18n.global.getLocaleMessage(currentLocale)) {
      await loadLocaleMessages(currentLocale);
    }
  });

  localeWatcher.observe(document.documentElement, { attributes: true, attributeFilter: ['lang'] });
}

setupLocaleReactivity(); // Ensure messages are updated when locale changes dynamically

export default i18n;
export { loadLocaleMessages, initI18n };
