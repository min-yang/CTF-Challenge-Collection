from pwn import *
from base64 import b64encode

hashcode = 0x21DD09EC

ele = int(hashcode / 5)
ele2 = hashcode - 4 * ele
print(ele)
print(p32(ele))

print(ele2)
print(p32(ele2))

print(b64encode(p32(ele) * 4 + p32(ele2)))