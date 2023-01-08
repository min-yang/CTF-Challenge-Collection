# 题目描述

I encrypted a public string and the flag with AES. There's no known key recovery attacks against AES, so you can't decrypt the flag.

# 解决方案

根据AES CBC加密原理，由明文、密文、密钥可以推出iv，然后第二次加密使用的key为第一次的iv，且第二次的iv已知，直接解密拿到flag，为irisctf{the_iv_aint_secret_either_way_using_cbc}，具体参考脚本[solve.py](solution/solve.py)。