import os
from Crypto.PublicKey import RSA
from secretsharing import PlaintextToHexSecretSharer

from encrypt import decrypt

# for i in range(10):
#     if not os.path.exists('key-%s.private' %i):
#         os.system('python3 ~/RsaCtfTool/RsaCtfTool.py --publickey key-%s.pem --private --output key-%s.private --timeout 5' %(i, i))

messages = []
for i in range(1, 6): # cipher number
    for j in range(4): # key number
        ciphertext = open('ciphertext-%s.bin' %i, 'rb').read()
        key = RSA.import_key(open('key-%s.private' %j).read())
        message = decrypt(key, ciphertext)
        if message:
            print(message.decode())
            messages.append(str(message.decode()))

for i in range(4):
    shares = []
    for j in range(3):
        shares.append(messages[j].split('\n')[i+1])
    print(PlaintextToHexSecretSharer.recover_secret(shares))
