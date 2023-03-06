middle = b'\x0d\x13\x17\x11\x02\x01\x20\x1d\x0c\x02\x19\x2f\x17\x2b\x24\x1f\x1e\x16\x09\x0f\x15\x27\x13\x26\x0a\x2f\x1e\x1a\x2d\x0c\x22\x04'
target = b'GONDPHyGjPEKruv{{pj]X@rF'

flag = ''
for i, ele in enumerate(target):
    tmp = ele ^ middle[i]
    tmp = tmp - 0x48 ^ 0x55
    flag += chr(tmp)

new_flag = ''
for char in flag:
    if char.islower():
        new_flag += char.upper()
    elif char.isupper():
        new_flag += char.lower()
    else:
        new_flag += char
print('EIS{' + new_flag + '}')
