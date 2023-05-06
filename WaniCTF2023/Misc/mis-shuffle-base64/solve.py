import base64
import itertools
import hashlib

def make_shuffle_list(m):
    num = []
    for i in range(len(m) // 3):
        num.append(i)

    return list(itertools.permutations(num, len(m) // 3))

cipher = b'fWQobGVxRkxUZmZ8NjQsaHUhe3NAQUch'
cipher = base64.b64decode(cipher).decode()
shuffle_list = make_shuffle_list(cipher)

for shuffle in shuffle_list:
    flag = ''
    for i in shuffle:
        flag += cipher[i*3:i*3+2]
    if flag[:5] == 'FLAG{' and flag[-2] == '}':
        print(flag)
    if hashlib.sha256(flag.encode()).hexdigest == '19B0E576B3457EDFD86BE9087B5880B6D6FAC8C40EBD3D1F57CA86130B230222'.lower():
        print(flag)
    


