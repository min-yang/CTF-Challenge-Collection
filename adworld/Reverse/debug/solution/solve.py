from hashlib import md5

middle = [
    2,
    3,
    5,
    7,
    11,
    13,
    17,
    19,
    23,
    29,
    31,
    37,
    41,
    43,
    47,
    53,
    59,
    61,
    67,
    71,
    73,
    79,
    83,
    89,
    97,
    101,
    103,
    107,
    109,
    113
]
middle2 = b'CreateByTenshine'

flag = ''
for i, ele in enumerate(middle2):
    count = 1
    while count < 15:
        ele = middle[count] ^ ele
        count += 1
    flag += chr(ele)

print('flag{' + md5(flag.encode()).hexdigest().upper() + '}')
