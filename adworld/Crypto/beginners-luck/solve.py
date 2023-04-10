from Crypto.Util.strxor import strxor
from pwn import *

data = open('BITSCTFfullhd.png', 'rb').read()
png_header = b'\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0dIHDR'
print(strxor(data[:16], png_header))

"""
方法1：匹配pad部分
已知的key：rkh%QP4g0&3g46@4
pad长度为19时，剩余部分的key为：P4g0&3g46@4*%f(UN#\
可以看到中间部分是匹配的
"""
for i in range(24):
    print(i, strxor(data[-24:], p8(i) * 24)[-i:])

"""
方法2：爆破出正确的key
"""
key = b'rkh%QP4g0&3g46@4'
print(strxor(data[:16], key).hex(), end='')
print('xxxxxxxx', end='')
print(strxor(data[24:24+16], key).hex())

def crc_validate():
    crc = 0x67b15614
    for i in range(2048):
        for j in range(2048):
            tmp = b'\x49\x48\x44\x52' + p32(i, endian='big') + p32(j, endian='big') + b'\x08\x02\x00\x00\x00'
            if binascii.crc32(tmp) & 0xffffffff == crc:
                print(i, j)
                return
# crc_validate()

width = p32(1920, endian='big')
height = p32(1080, endian='big')
key += strxor(data[16:24], width+height)
print(key)

# 解密
key = b'rkh%QP4g0&3g46@4*%f(UN#\\'
fw = open('fullhd.png', 'wb')
for i in range(0, len(data), 24):
    fw.write(strxor(data[i:i+24], key))
fw.close()

"""
其他方法：
    使用xortool
    fullhd其实就暗示了宽和高为1920x1080
"""