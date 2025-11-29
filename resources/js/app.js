
import './bootstrap';
import { createApp } from 'vue';
import DepartmentsModal from './components/DepartmentsModal.vue';

const app = createApp({});
app.component('DepartmentsModal', DepartmentsModal);
app.mount('#departments-modal-root');

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js').catch((error) => {
            console.error('SW registration failed:', error);
        });
    });
}
