from Crypto.Util.number import *
from Crypto.Util.Padding import pad
from random import randint
from Crypto.Util.strxor import strxor
from Crypto.Cipher import AES
from hashlib import sha256
from hashlib import md5

flag = b'xxx'

def Get_Parameters():
    w = getPrime(25)
    a = getPrime(15)
    b = getPrime(15)
    x = getPrime(30)
    return w,a,b,x

def Malicious_ECDH():
    w,a,b,x = Get_Parameters()
    
    P = getPrime(512)
    A = getRandomNBitInteger(30)
    B = getRandomNBitInteger(40)
    F = GF(P)
    E = EllipticCurve(F, [A, B])
    G = E.random_point()
    k1 = getRandomNBitInteger(50)
    M1 = k1 * G
    
    Y = x * G
    t = randint(0,1)
    t = 1
    z = (k1 - w * t) * G + (-a*k1 - b) * Y
    k2 = sha256(str(z[0]).encode()).digest()[:6]
    k2 = bytes_to_long(k2)
    M2 = k2 * G
    k_rec = getRandomNBitInteger(50)
    B_ = k_rec * G
    shared_key1 = k_rec * M2 
    shared_key2 = k2 * B_
    assert shared_key1 == shared_key2
    
    print((w,a,b,x))
    print((A,B,P))
    print(G.xy())
    print(M1.xy())
    print(M2.xy())
    print(B_.xy())
    return shared_key1

def easy_enc(pt,key):
    key = md5(str(int(key[0])).encode()).digest()
    cipher = AES.new(key, AES.MODE_ECB)
    ct = cipher.encrypt(pad(pt,16))
    print(ct)
    
key = Malicious_ECDH()
easy_enc(flag,key)
    