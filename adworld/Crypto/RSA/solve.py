import base64
import gmpy2
from Crypto.Util.number import long_to_bytes, bytes_to_long
from Crypto.PublicKey import RSA
from Crypto.Cipher import PKCS1_v1_5

p = 184333227921154992916659782580114145999
q = 336771668019607304680919844592337860739
e = 9850747023606211927
n = p * q
c = base64.b64decode(open('flag.enc', 'rb').read())
c_ = bytes_to_long(c)
print(len(c))

i = 1
while True:
    print(i)
    i += 1
    n = q * p
    if n >= c_:
            phi = (p-1) * (q-1)
            d = pow(e, -1, phi)
            prikey = RSA.construct((int(n), int(e), int(d)))
            key = PKCS1_v1_5.new(prikey)
            dec = key.decrypt(c, None)
            print(dec)
            break
    else:
            p = gmpy2.next_prime(p**2+q**2)
            q = gmpy2.next_prime(2*q*p)
            e = gmpy2.next_prime(e**2)
