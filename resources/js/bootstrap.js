import axios from 'axios';
window.axios = axios;
// In app.js or bootstrap.js
axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
