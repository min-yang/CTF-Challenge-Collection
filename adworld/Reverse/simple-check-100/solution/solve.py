from pwn import *

middle = b''
for ele in [0x54, 200, 0x7e, 0xe3, 100, 199, 0x16, 0x9a, 0xcd, 0x11, 0x65, 0x32, 0x2d, 0xe3, 0xd3, 0x43, 0x92, 0xa9, 0x9d, 0xd2, 0xe6, 0x6d, 0x2c, 0xd3, 0xb6, 0xbd, 0xfe, 0x6a, 0x13]:
    middle += p8(ele)
print(middle)

flag = b'\xdc\x17\xbf\x5b\xd4\x0a\xd2\x1b\x7d\xda\xa7\x95\xb5\x32\x10\xf6\x1c\x65\x53\x53\x67\xba\xea\x6e\x78\x22\x72\xd3'

for i in range(7):
    local_18 = p32(u32(middle[i*4:(i+1)*4]) ^ 0xdeadbeef)
    for j in range(3, -1, -1):
        print(chr(flag[i * 4 + j] ^ local_18[j]), end='')

print()
