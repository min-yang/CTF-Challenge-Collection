# 问题描述

I was experimenting with making a new encryption algorithm, and came up with this one. I unfortunately decided to test it on a file which was very important to me, and now I need help in decrypting the file :(

flag format: NITE{}

# 解决方案

观察加密脚本，发现两个信息，一是密码长度为8，二是有一段明文为HiDeteXT，由于给出的明文其长度正好也是8，因此可以推出可能的密码组合；然后我们推测密码是可打印字符，所以排除了所有包含非打印字符的密码组合；最后，由于对应明文HiDeteXT的密码其顺序是不一定，因此我们需要将推出的每一个密码尝试其8种可能的顺序来对密文解密，最后从所有输出结果组合中过滤出包含flag的明文。

具体逻辑可参考[solve.py](solution/solve.py)，最后得到明文为：`Oh, You Searching for HiDeteXT ??\n\nNITE{BrUT3fORceD_y0uR_wAy_iN}\n`
