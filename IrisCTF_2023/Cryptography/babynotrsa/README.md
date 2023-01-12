# 题目描述

Everyone knows [RSA](https://en.wikipedia.org/wiki/RSA_(cryptosystem)), but everyone also knows that RSA is slow. Why not just use a faster operation than exponentiation?

# 解决方案

参考[模乘法逆元](https://en.wikipedia.org/wiki/Modular_multiplicative_inverse)，`flag = encrypted * e^-1 mod n`，具体参考脚本[solve.py](solution/solve.py)。

如果使用sage，可以使用如下脚本：

```python
R = Zmod(n)
flag = R(encrypted) / e
```

参考链接：

- https://github.com/Seraphin-/ctf/blob/master/irisctf2023/babynotrsa.md
