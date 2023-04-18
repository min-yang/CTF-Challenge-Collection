import gmpy2
from base64 import b64encode, b64decode
from Crypto.Util.number import long_to_bytes, bytes_to_long
from Crypto.PublicKey import RSA
from Crypto.Cipher import PKCS1_v1_5
from Crypto.Random import get_random_bytes

q = 184333227921154992916659782580114145999
p = 336771668019607304680919844592337860739
e = 9850747023606211927

def encrypt(p, q, e, msg):
    while True:
        n = p * q
        try:
            phi = (p - 1)*(q - 1)
            pubkey = RSA.construct((int(n), int(e)))
            key = PKCS1_v1_5.new(pubkey)
            enc = key.encrypt(msg)
            return enc, p, q, e
        except:
            p = gmpy2.next_prime(p**2 + q**2)
            q = gmpy2.next_prime(2*p*q)
            e = gmpy2.next_prime(e**2)

enc, p, q, e = encrypt(p, q, e, b'1' * (23 * 11))
print('密文长度： %s' %len(enc))
print(enc)

phi = (p - 1) * (q - 1)
d = pow(e, -1, phi)
n = p * q

enc = b64decode(open('flag.enc').read())
key = RSA.construct((int(n), int(e), int(d)))
key = PKCS1_v1_5.new(key)
sentinel = get_random_bytes(16)
print(key.decrypt(enc, sentinel))


# n = p * q
# c = base64.b64decode(open('flag.enc', 'rb').read())
# c_ = bytes_to_long(c)
# print(len(c))

# i = 1
# while True:
#     print(i)
#     i += 1
#     n = q * p
#     if n >= c_:
#             phi = (p-1) * (q-1)
#             d = pow(e, -1, phi)
#             prikey = RSA.construct((int(n), int(e), int(d)))
#             key = PKCS1_v1_5.new(prikey)
#             dec = key.decrypt(c, None)
#             print(dec)
#             break
#     else:
#             p = gmpy2.next_prime(p**2+q**2)
#             q = gmpy2.next_prime(2*q*p)
#             e = gmpy2.next_prime(e**2)
