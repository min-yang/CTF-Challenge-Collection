
target = bytes.fromhex('616A79676B466D2E7F5F7E2D53567B386D4C6E')

flag = [0] * len(target)
for i in range(len(target)-1, -1, -1):
    if i == 18:
        flag[i] = target[i] ^ 0x13
    else:
        v3 = target[i] ^ i
        if i % 2 == 1:
            flag[i] = v3 + i
        else:
            flag[i+2] = v3

for ele in flag:
    print(chr(ele), end='')
print()