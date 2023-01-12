# 题目描述

I'm sure you'll never guess what comes next: Now do it without the key!

# 解决方案

解决这个问题需要用到[z3](https://github.com/Z3Prover/z3)，一个定理证明器。

首先定义未知变量key，如下：

```
key = [BitVec("key%d" % i, 8) for i in range(KEYLEN)]
```

然后改写加密函数，使其支持z3求解器，然后根据已知的8对pair（明文和密文）定义约束条件，然后开始求解，即可拿到key，然后根据[SMarT_1](../SMarT_1)的解密脚本解密，拿到flag，为irisctf{if_you_didnt_use_a_smt_solver_thats_cool_too}。

由于不熟悉z3这个框架，包括其语法规则等，所以以后碰到这种题还是做不出来，以后需要抽空阅读z3的文档。

参考链接：

- https://github.com/Seraphin-/ctf/blob/master/irisctf2023/smart.md