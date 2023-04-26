key = b'69800876143568214356928753'

flag = [0] * 7
flag[0] = chr(2 * key[1])
flag[1] = chr(key[4] + key[5])
flag[2] = chr(key[8] + key[9])
flag[3] = chr(2 * key[12])
flag[4] = chr(key[18] + key[17])
flag[5] = chr(key[10] + key[21])
flag[6] = chr(key[9] + key[25])

print(''.join(flag))