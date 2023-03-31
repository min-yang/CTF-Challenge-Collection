from Crypto.Util import number
from Crypto.PublicKey import RSA

c1 = number.bytes_to_long(open('cipher1.txt', 'rb').read())
c2 = number.bytes_to_long(open('cipher2.txt', 'rb').read())

key1 = RSA.import_key(open('publickey1.pem', 'rb').read())
key2 = RSA.import_key(open('publickey2.pem', 'rb').read())

with open('params.txt', 'w') as fw:
    fw.write('c : %s\n' %c1)
    fw.write('e : %s\n' %key1.e)
    fw.write('n : %s\n' %key1.n)
    fw.write('c : %s\n' %c2)
    fw.write('e : %s\n' %key2.e)
    fw.write('n : %s\n' %key2.n)