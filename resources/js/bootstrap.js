import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from "laravel-echo";
import { io } from "socket.io-client";

window.io = io;

window.Echo = new Echo({
    broadcaster: "socket.io",
    host: window.location.hostname + ":6001"
});

// Exemple d'écoute d'un channel
window.Echo.channel("chat")
    .listen("MessageSent", (e) => {
        console.log("Message reçu :", e.message);
    });
