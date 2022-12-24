# 题目描述

Mr. Pascal has placed his bet on a game, again. But he thinks you can help him this time.

Flag format: niteCTF{}

# 解决方案

题目提供了源码，审计一遍后发现随机生成器的参数可以爆破出来，爆破出来后就可以知道服务器用来设置挑战的随机数，成功通过10次挑战后，就可以拿到密文，加密算法是[Paillier](https://en.wikipedia.org/wiki/Paillier_cryptosystem)，同样，由于随机生成器的参数是已知的，因此可以直接计算出私钥，然后根据解密公式算出明文，为niteCTF{n0T_sO_R@nd0m}。

获取随机数生成器参数的逻辑参考[solve.py](solution/solve.py)，解密的逻辑参考[solve2.py](solution/solve2.py)。