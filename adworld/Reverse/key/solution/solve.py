middle = b'themidathemidathemida'
middle2 = b'>----++++....<<<<.'

flag = []
for i in range(18):
    flag.append((middle[i] ^ middle2[i]) + 22)

for i in range(18):
    flag[i] += 9

for ele in flag:
    print(chr(ele), end='')
print()