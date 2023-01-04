import base64
from struct import pack

from pwn import *
from Crypto.Cipher import Blowfish

key = b'Q2hyNGlzdG1hc0pveQ=='

def print_results(msg):
    print(msg)
    print(xor(msg, key))

for mode in [
    Blowfish.MODE_ECB,
    Blowfish.MODE_CBC,
    Blowfish.MODE_CFB,
    Blowfish.MODE_OFB,
    Blowfish.MODE_CTR,
    Blowfish.MODE_OPENPGP,
    Blowfish.MODE_EAX,
]:
    print('Trying mode', mode)
    try:
        cipher = Blowfish.new(key, mode)
        ct = base64.standard_b64decode('N+ke3xIGF/h//tiT4SxIECOxGG7moZui0dccxtqmUg0=')
        msg = cipher.decrypt(ct)
        print_results(msg)
    except Exception as e:
        print(e)
