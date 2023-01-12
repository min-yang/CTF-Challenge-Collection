# 题目描述

The flag's right there, but I think it's kinda stuck. Please help me.

# 解决方案

服务器允许你设置环境变量，但环境变量不能包含以下字符：`LD, LC, PATH, ORIGIN`，有一个不安全的环境变量列表可以参考[这里](https://codebrowser.dev/glibc/glibc/sysdeps/generic/unsecvars.h.html)，发现有一个环境变量可以利用，就是`RESOLV_HOST_CONF`，这个环境变量会让域名解析过程读取这个环境变量指向的文件，并将其作为配置文件进行解析，解析错误的行会打印到标准错误输出，所以我们设置环境变量`RESOLV_HOST_CONF`的值位flag，即可拿到flag，为irisctf{very_helpful_error_message}。

这里考察的是知识范围，第一时间看到题目提供的源码时，根本不知道如何去解，如果之前了解过通过`RESOLV_HOST_CONF`读取特权文件等方法，这里就很容易解出来了，[CVE-2001-0170](https://nvd.nist.gov/vuln/detail/CVE-2001-0170)，也提到了这个环境变量存在的问题；以后要加强对各种公开漏洞学习了解，才能解类似的题。

参考链接：

- https://github.com/Seraphin-/ctf/blob/master/irisctf2023/host.md