# 题目描述

We've suffered a breach in our database and some of our files have been stolen by the Grinch. We managed to get some of them back, but we need your help in recovering the lost data, most especially because we think some of them have been altered in some way.

PS: Start with the image then continue with the zip, Keepass file. Not your typical kind of steganography. :). ”Think like an attacker”

Password: Christmas

# 注意事项

题目描述提供的密码是用来解压附件提供的压缩包的，库中存储的是解压后的文件

# 解决方案

通过二进制编辑器修改[图片](file/X-MAS.jpg)的高度，然后可以看到一串字符`RUDOLF_1S_4_N4UGHTY_BOY`，这串字符是[压缩包](file/flag.zip)的密码，解压后就可以看到[passwd.txt](file/passwd.txt)的内容；

然后关注[Flag.kdbx](file/Flag.kdbx)文件，使用John the Ripper获取文件的hash，命令如下：

```
keepass2john Flag.kdbx > Keepasshash.txt
```

然后爆破hash对应的密码，命令如下：

```
john --wordlist=rockyou.txt Keepasshash.txt
```

rockyou.txt是常用的密码字典文件，可以从网上下载，爆破的结果为`dracula`，使用该密码打开[Flag.kdbx](file/Flag.kdbx)文件，得到如下内容： 

```
We managed to extract this data but I can't figure out what it is. The attacker said it should be something easy. We only know that he likes some good cooked fish.

Q2hyNGlzdG1hc0pveQ==

N+ke3xIGF/h//tiT4SxIECOxGG7moZui0dccxtqmUg0=
```

然后使用Blowfish算法进行解密，解密脚本为[solve.py](solution/solve.py)，解密后发现一个链接，为[https://pastebin.com/CPzeYJmb](https://pastebin.com/CPzeYJmb)，打开后发现需要密码，这个时候使用[passwd.txt](file/passwd.txt)文件中的内容作为密码（注意需要base64解码），就可以拿到flag，为
X-MAS{S4Nt4_4nD_h1S_R31nd3ers}。