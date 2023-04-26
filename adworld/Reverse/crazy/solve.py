key = b'327a6c4304ad5938eaf0efb6cc3e53dc'

flag = []
for i in range(len(key)):
    flag.append((key[i] - 11) ^ 0x13)

for i in range(len(key)):
    print(chr((flag[i] - 23) ^ 0x50), end='')