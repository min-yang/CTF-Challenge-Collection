# 反编译二进制文件，发现关键部分，进入/cgi-bin/后，将后续的路径做自定义运算，运算后的值如果等于给定的值，就通过验证
# 以下是逆向程序，通过输出和参数倒推出输入
param = b'\x84\xe2\x96\x84\xe2\x96\x84\x20'
target = b'\x41\xfc\x1b\x18\xff\x10\x0d\xb4\x1d\xfc\x1b\x18\xff\x11\x0d\xb7\x18\xff\x16\x0d\xe5\x04\x3b'
input_ = ''
for i in range(0x17):
    print((target[i] - 0x42) % 256 ^ param[i % 8])
    input_ += chr((target[i] - 0x42) % 256 ^ param[i % 8])

print(input_)