import hashlib
import base64
from Crypto.Cipher import AES
from Crypto.Util.number import long_to_bytes

ps = open('ps').readlines()
ps = [int(ele) for ele in ps]
pbits=[bin(x).rfind('1')-2 for x in ps]

r = int(open('r').read())

bchoose = ['0'] * 512
for i in range(511, -1, -1):
    ind = pbits.index(i)
    tt = bin(r)[2:].rjust(512,'0')[i]
    if tt == '1':
        bchoose[ind] = '1'
        r ^= ps[ind]

choose = int(''.join(bchoose), 2)
key = long_to_bytes(int(hashlib.md5(long_to_bytes(choose)).hexdigest(), 16))
aes_obj = AES.new(key, AES.MODE_ECB)

ef = open('ef').read()
cipher = base64.b64decode(ef)
print(aes_obj.decrypt(cipher))