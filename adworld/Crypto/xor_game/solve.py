import base64
import string
from Crypto.Util.strxor import strxor

def enc(data, key):
    key = (key * int(len(data) / len(key) + 1))[:len(data)]
    return strxor(data, key.encode())

data = base64.b64decode(open('cipher.txt').read())
key = ''
for i in range(21):
    for j in string.printable:
        satify = True
        for k in range(i, len(data), 21):
            if data[k] ^ ord(j) not in string.printable.encode():
                satify = False
                break
        if satify:
            key += j
            break

print(key)
print(enc(data, key).decode())