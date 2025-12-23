// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE_URL || 'http://localhost/api',
      apiToken: process.env.NUXT_PUBLIC_API_TOKEN || '',
    },
  },

  app: {
    head: {
      link: [
        {
          rel: 'preconnect',
          href: 'https://fonts.googleapis.com',
        },
        {
          rel: 'preconnect',
          href: 'https://fonts.gstatic.com',
          crossorigin: '',
        },
        {
          rel: 'stylesheet',
          href: 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap',
        },
      ],
    },
  },

  modules: ['@nuxtjs/tailwindcss'],
  css: ['~/assets/css/main.css'],
})
