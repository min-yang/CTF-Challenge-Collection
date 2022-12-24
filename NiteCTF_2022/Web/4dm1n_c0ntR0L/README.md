# 题目描述

Get admin control in the website somehow

Flag format: nitectf{}

# 解决方案

打开网站，发现只有一个登录框，其它啥也没有，应该是SQL注入，账号输入`1' or 1=1--#`，密码随便输一个，即可登陆进去拿到flag，其为：nitectf{w3nT_1nT0_Th3_s3rV3r}
