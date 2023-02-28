from pwn import *

context.log_level = 'DEBUG'

p = remote('trellixhax-free-yo-radicals-part-i.chals.io', '443', ssl=True)
# p = remote('127.0.0.1', '9999')

"""
deliminator(0x0e) 4, 7, 0xc, 0xf, 0x14, 0x19, 0x1e, 0x23, 0x2c
terminator(0x0c) 0x2d
第15个字节为action:
    1 -> create # 条件1
    2 -> delete # 条件2
    3 -> print # 条件3
    5 -> exit # 退出前会判断三个条件，如果三个条件都满足，就打印flag
"""
action1 = bytes.fromhex('ac16ec2c0e270f0e636581d40e00010e444d30360e637300000e000000000e000000000e00000000000000780e0c')
action2 = bytes.fromhex('ac16ec2c0e270f0e636581d40e00020e444d30360e637300000e000000000e000000000e00000000000000780e0c')
action3 = bytes.fromhex('ac16ec2c0e270f0e636581d40e00030e444d30360e637300000e000000000e000000000e00000000000000780e0c')
action5 = bytes.fromhex('ac16ec2c0e270f0e636581d40e00050e444d30360e637300000e000000000e000000000e00000000000000780e0c')

# 1、2、3动作都执行一次后，再执行动作5，拿到flag
p.sendafter(b'data... \n', action2)
p.sendafter(b'data... \n', action1)
p.sendafter(b'data... \n', action3)
p.sendafter(b'data... \n', action5)

p.interactive()