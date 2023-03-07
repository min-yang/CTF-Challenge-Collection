
data_map = {
    0: 'i',
    1: 'e',
    3: 'n',
    4: 'd',
    5: 'a',
    6: 'g',
    7: 's',
    9: 'r',
}

flag = ''
key = 0
while True:
    if key not in data_map:
        break
    flag += data_map[key]
    key = ((key + 1) * 7) % 11
print(flag)