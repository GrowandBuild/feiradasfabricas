
import './bootstrap';
import { createApp } from 'vue';
import DepartmentsModal from './components/DepartmentsModal.vue';

const app = createApp({});
app.component('DepartmentsModal', DepartmentsModal);
app.mount('#departments-modal-root');

// Service Worker registration removido - está sendo feito no layout principal para evitar duplicação
