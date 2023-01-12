# 题目描述

I made a small, efficient block cipher. It's small because I can fit it on the page and it's efficient because it only uses the minimal amount of rounds. I didn't even try it but I'm sure it works. Here's some output with the key. Can you get the flag back out?

# 解决方案

题目提供的附件包括加密过程中使用到的所有参数，包括密钥也是给出来的，所以这道密码题仅需根据加密算法写出解密算法，需要熟悉加密脚步中涉及到的运算，如果不熟悉，很难写出逆向的解密算法，就跟AES-ECB加密一样，这里使用的加密算法将输入进行分组，每一组的输入决定输出，这里是8位一组，加密脚本如下：

```python
def rr(c, n):
    n = n % 8
    return ((c << (8 - n)) | (c >> n)) & 0xff

def encrypt(block, key):
    assert len(block) == 8
    assert len(key) == KEYLEN
    block = bytearray(block)

    for r in range(ROUNDS):
        block = bytearray(xor(block, key[r*4:(r+2)*4]))
        for i in range(8):
            block[i] = SBOX[block[i]]
            block[i] = rr(block[i], RR[i])

        temp = bytearray(8)
        for i in range(8):
            for j in range(8):
                temp[j] |= ((block[i] >> TRANSPOSE[i][j]) & 1) << i

        block = temp

        block = xor(block, MASK)
    return block
```

其中异或运算逆很简单，SBOX对打乱输入，恢复也很简单，直接`SBOX.index(block[i])`即可，关键rotate部分和transpose部分如何逆向，仔细观察rr函数，其实就是循环右移函数，可以自己实现个循环左移函数或者使用`rr(block[i], 8-RR[i])`来逆向，因为循环右移8位就等于自身。

然后难点是transpose部分，根据计算规则，可以直到每个temp字节都跟block的8个字节相关，且所有temp字节的最低位是由block的第一个字节算出来的，并根据TRANSPOSE打乱顺序，比如`TRANSPOSE[i][j]=k`代表temp的第j个字节的第i位等于block的第i个字节的第k位的值，下面给出一次转换的示例：

```
block: 01100110 01101110 10000111 11010101 00111110 01101111 01011111 11110110
temp:  11101110 11111111 11100000 10111011 11111101 00010010 11010110 01101001
```

转换使用的TRANSPOSE的值如下：

```python
TRANSPOSE = [[3, 1, 4, 5, 6, 7, 0, 2],
 [1, 5, 7, 3, 0, 6, 2, 4],
 [2, 7, 5, 4, 0, 6, 1, 3],
 [2, 0, 1, 6, 4, 3, 5, 7],
 [6, 5, 0, 3, 2, 4, 1, 7],
 [2, 0, 6, 1, 5, 7, 4, 3],
 [1, 6, 2, 5, 0, 7, 4, 3],
 [4, 5, 6, 1, 2, 3, 7, 0]]
```

已知`TRANSPOSE[0] = [3, 1, 4, 5, 6, 7, 0, 2]`，block的第一个字节按这个规则打乱顺序，由01100110转换为01011001，我们看所有temp字节的最低位组合起来也是01011001，以此类推，block的第二个字节按规则打乱顺序，然后赋给temp所有字节第2低位。

解下来思考如何逆向，直接通过公式`block[i][j] = temp[TRANSPOSE[i].index(j)][i]`，逆向过程如下：

```python
temp = bytearray(8)
for i in range(8):
    for j in range(8):
        temp[i] |= ((block[TRANSPOSE[i].index(j)] >> i) & 1) << j
```

上面符号是反过来的，block是transpose后的结果，我们需要逆向，并将逆向后的结果写入temp；由于不能直接操纵比特位，我们借助移位和与运算来达到操纵比特位的效果，翻译一下就是block的第`TRANSPOSE[i].index(j)`个字节的第i低位的值赋值给temp第i个字节第j低位的值。

结合起来，就可以解密拿到flag，为irisctf{ok_at_least_it_works}，具体参考解密脚本[solve.py](solution/solve.py)。 

参考链接：

- https://github.com/Seraphin-/ctf/blob/master/irisctf2023/smart.md