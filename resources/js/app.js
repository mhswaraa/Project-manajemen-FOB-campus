import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse'; // 1. Import plugin

Alpine.plugin(collapse); // 2. Daftarkan plugin ke Alpine

window.Alpine = Alpine;

Alpine.start();
