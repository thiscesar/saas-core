import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['ngrok-skip-browser-warning'] = 'true';

// Configure Livewire to skip ngrok warning page
document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ options }) => {
        options.headers = options.headers || {};
        options.headers['ngrok-skip-browser-warning'] = 'true';
    });
});
