const { Server } = require("socket.io");

const io = new Server({
    cors: {
        origin: "*", // Permet toutes les connexions (à sécuriser en prod)
    },
    port: 6001
});

io.on("connection", (socket) => {
    console.log("Client connecté : " + socket.id);

    socket.on("message", (data) => {
        console.log("Message reçu :", data);
        io.emit("message", data); // renvoie à tous les clients
    });

    socket.on("disconnect", () => {
        console.log("Client déconnecté : " + socket.id);
    });
});

console.log("Serveur WebSocket lancé sur le port 6001");
