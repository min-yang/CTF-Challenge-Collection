from Crypto.Cipher import AES

cipherText = bytes.fromhex('de49b7bb8e3c5e9ed51905b6de326b39b102c7a6f0e09e92fe398c75d032b41189b11f873c6cd8cdb65a276f2e48761f6372df0a109fd29842a999f4cc4be164')
key = bytes.fromhex('4ee04f8303c0146d82e0bbe376f44e10')
plainText = b'Hello, this is a public message. This message contains no flags.'

iv1 = b"a" * 16
aes = AES.new(key, AES.MODE_CBC, iv1)
fakePlainText = aes.decrypt(cipherText)
crackIV = b''

for i in range(16):
    crackIV += (fakePlainText[i] ^ iv1[i] ^ plainText[i]).to_bytes(1, 'big')

iv2 = bytes.fromhex('1fe31329e7c15feadbf0e43a0ee2f163')
cipherText2 = bytes.fromhex('f6816a603cefb0a0fd8a23a804b921bf489116fcc11d650c6ffb3fc0aae9393409c8f4f24c3d4b72ccea787e84de7dd0')
aes = AES.new(crackIV, AES.MODE_CBC, iv2)
print(aes.decrypt(cipherText2))