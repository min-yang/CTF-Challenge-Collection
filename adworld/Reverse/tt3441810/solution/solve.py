from pwn import *

lines = open('../file/rev100').readlines()

b = b''
for line in lines:
    for ele in line.split()[1:]:
        b += p8(int(ele, 16))
print(b)

print(disasm(b))