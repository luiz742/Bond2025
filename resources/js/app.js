import './bootstrap';
import '../css/app.css';

import { createApp, h, onMounted } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import '@tailwindplus/elements';  // <-- Aqui!

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);

        // Injeta o script do chatbot quando o app é montado
        // vueApp.mixin({
        //     mounted() {
        //         if (!document.querySelector('script[data-chatbot-id="GGZL13KInljjpctOz04zvFPfWUTRfLHb"]')) {
        //             const script = document.createElement('script');
        //             script.async = true;
        //             script.src = "https://notbrxypl65h4qetzzdlfahw.agents.do-ai.run/static/chatbot/widget.js";
        //             script.setAttribute("data-agent-id", "419b7089-64ca-11f0-bf8f-4e013e2ddde4");
        //             script.setAttribute("data-chatbot-id", "GGZL13KInljjpctOz04zvFPfWUTRfLHb");
        //             script.setAttribute("data-name", "agent-document-checker Chatbot");
        //             script.setAttribute("data-primary-color", "#031B4E");
        //             script.setAttribute("data-secondary-color", "#E5E8ED");
        //             script.setAttribute("data-button-background-color", "#0061EB");
        //             script.setAttribute("data-starting-message", "Hello! How can I help you today?");
        //             script.setAttribute("data-logo", "/static/chatbot/icons/default-agent.svg");
        //             document.body.appendChild(script);
        //         }
        //     }
        // });

        vueApp.mount(el);
        return vueApp;
    },
    progress: {
        color: '#4B5563',
    },
});
