start = 0xd42ff8
middle = '~}|{zyxwvutsrqponmlkjihgfedcba`_^]\\[ZYXWVUTSRQPONMLKJIHGFEDCBA@?>=<;:9876543210/.-,+*)(\'&%$#"! '
middle_offset = 0xd43018 - start
target = 'DDCTF{reverseME}'

flag = ''
for ele in target:
    flag += chr(middle.index(ele) + middle_offset)
print(flag)