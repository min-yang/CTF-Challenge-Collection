local_28 = b'Dufhbmf\0pG`imos\0ewUglpt\0'

print(local_28)
flag = ''
for i in range(0xb+1):
    flag += chr(local_28[(i % 3) * 8 + i // 3 * 2] - 1)
print(flag)
