from Crypto.PublicKey import RSA
from Crypto.Util.number import *
from gmpy2 import *
import libnum
c=bytes_to_long(open('cipher.bin','rb').read())
key=RSA.importKey(open('key.pem').read())
n,e=key.n,key.e
#print(hex(n)[2:])
s=iroot(n+1,2)[0]
p=s-1
q=s+1
assert p*q==n and isPrime(p) and isPrime(q)
d=inverse(e,(p-1)*(q-1))
print(long_to_bytes(pow(c,d,n)))