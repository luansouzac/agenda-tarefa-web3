import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { pt } from 'vuetify/locale'

export default createVuetify({
    components,
    directives,
    locale: { locale: 'pt', messages: { pt } },
    theme: {
        defaultTheme: 'agenda',
        themes: {
            agenda: {
                dark: false,
                colors: {
                    background: '#F4F7F6',
                    surface: '#FFFFFF',
                    primary: '#0F766E',
                    'primary-claro': '#14A394',
                    secondary: '#F59E0B',
                    success: '#16A34A',
                    error: '#DC2626',
                    warning: '#EA580C',
                    info: '#2563EB',
                    roxo: '#7C3AED',
                    rosa: '#DB2777'
                }
            }
        }
    },
    defaults: {
        VBtn: { style: 'text-transform: none; letter-spacing: 0;' },
        VTextField: { variant: 'outlined', density: 'comfortable' },
        VSelect: { variant: 'outlined', density: 'comfortable' }
    }
});
