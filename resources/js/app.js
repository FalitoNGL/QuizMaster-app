import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Dark mode store - global state accessible from any component
Alpine.store('darkMode', {
    on: localStorage.getItem('darkMode') === 'true',

    toggle() {
        this.on = !this.on;
        localStorage.setItem('darkMode', this.on);
        this.updateClass();
    },

    updateClass() {
        if (this.on) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },

    init() {
        this.updateClass();
    }
});

Alpine.start();
