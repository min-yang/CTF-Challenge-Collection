
data = b'\x63\x36\x31\x62\x36\x38\x33\x36\x36\x65\x64\x65\x62\x37\x62\x64\x63\x65\x33\x63\x36\x38\x32\x30\x33\x31\x34\x62\x37\x34\x39\x38'
flag = list('SharifCTF{????????????????????????????????}')
for i, b in enumerate(data):
    if i % 2 == 0:
        offset = -1
    else:
        offset = 1
    flag[i + 10] = chr(b + offset)
print(''.join(flag))