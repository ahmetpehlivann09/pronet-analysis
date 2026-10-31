const WebSocket = require('ws');

const URL =
'wss://bragi-ws4.pronetgaming.eu/overview/1963betebet.com/1/0/d' +
'?X-Atmosphere-tracking-id=0' +
'&X-Atmosphere-Framework=4.0.1' +
'&X-Atmosphere-Transport=websocket' +
'&Content-Type=application/json';  
 

const clients = new Set();

const server = new WebSocket.Server({
    host: '127.0.0.1',
    port: 8765
});



server.on('connection', socket => {
    clients.add(socket);

    socket.on('close', () => clients.delete(socket));
});

function broadcast(message) {
    for (const client of clients) {
        if (client.readyState === WebSocket.OPEN) {
            client.send(message);
        }
    }
}

console.log('Yerel proxy: ws://127.0.0.1:8765');

const upstream = new WebSocket(URL, {
    headers: {
        Origin: 'https://1963betebet.com',
        'X-Atmosphere-Framework': '4.0.1',
        'X-Atmosphere-Transport': 'websocket',
        'Content-Type': 'application/json'
    },
    perMessageDeflate: false,
    handshakeTimeout: 15000
});

 

upstream.on('open', () => {
    console.log('Pronet bağlantısı açıldı.');
});

upstream.on('message', data => {
    const message = data.toString();

    //console.log('GELEN:', message.slice(0, 500));


    const fs = require('fs');
    const path = require('path')

    const logFile = path.join(__dirname, 'pronet-data.json1');
 

    fs.appendFileSync(
        logFile,
        message + '\n',
        'utf8'
    );
 

console.log('Mesaj alındı. Karakter sayısı:', message.length);
console.log('Dosyaya kaydedildi:', logFile);



    broadcast(message);
});

upstream.on('unexpected-response', (request, response) => {
    console.log('HTTP HATA:', response.statusCode);
    console.log('Başlıklar:', response.headers);
});

upstream.on('error', error => {
    console.log('BAĞLANTI HATASI:', error.message);
});

upstream.on('close', (code, reason) => {
    console.log('BAĞLANTI KAPANDI:', code, reason.toString());
});