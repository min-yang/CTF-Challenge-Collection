"""
根据base64还原下面三个数据：
Os9mhOQRdqW2cwVrnNI72DLcAXpXUJ1HGwJBANWiJcDUGxZpnERxVw7s0913WXNtV4GqdxCzG0pG5EHThtoTRbyX0aqRP4U/hQ9tRoSoDmBn+3HPITsnbCy67VkCQBM4xZPTtUKM6Xi+16VTUnFVs9E4rqwIQCDAxn9UuVMBXlX2Cl0xOGUF4C5hItrX2woF7LVS5EizR63CyRcPovMCQQDVyNbcWD7N88MhZjujKuSrHJot7WcCaRmTGEIJ6TkU8NWt9BVjR4jVkZ2EqNd0KZWdQPukeynPcLlDEkIXyaQx

024100 # d % (p -1)
d5a225c0d41b16699c4471570eecd3dd7759736d5781aa7710b31b4a46e441d386da1345bc97d1aa913f853f850f6d4684a80e6067fb71cf213b276c2cbaed59
0240 # d % (q - 1)
1338c593d3b5428ce978bed7a553527155b3d138aeac084020c0c67f54b953015e55f60a5d31386505e02e6122dad7db0a05ecb552e448b347adc2c9170fa2f3
024100 # (inverse of q) mod p
d5c8d6dc583ecdf3c321663ba32ae4ab1c9a2ded6702691993184209e93914f0d5adf415634788d5919d84a8d77429959d40fba47b29cf70b943124217c9a431
"""
from Crypto.Util.number import *

x1 = bytes_to_long(bytes.fromhex('d5a225c0d41b16699c4471570eecd3dd7759736d5781aa7710b31b4a46e441d386da1345bc97d1aa913f853f850f6d4684a80e6067fb71cf213b276c2cbaed59'))
x2 = bytes_to_long(bytes.fromhex('1338c593d3b5428ce978bed7a553527155b3d138aeac084020c0c67f54b953015e55f60a5d31386505e02e6122dad7db0a05ecb552e448b347adc2c9170fa2f3'))
x3 = bytes_to_long(bytes.fromhex('d5c8d6dc583ecdf3c321663ba32ae4ab1c9a2ded6702691993184209e93914f0d5adf415634788d5919d84a8d77429959d40fba47b29cf70b943124217c9a431'))

e = 65537
n1 = x1 * e - 1
n2 = x2 * e - 1
for r in range(e, 0, -1):
    if n1 % r == 0: # 可以被整除
        p = n1 // r + 1
        if isPrime(p):
            break

for r in range(e, 0, -1):
    if n2 % r == 0:
        q = n2 // r + 1
        if isPrime(q):
            break
print(p, q, x3)

N = p * q
phi = (p - 1) * (q - 1)
d = pow(e, -1, phi)
assert pow(q, -1, p) == x3

data = bytes_to_long(open('flag.enc', 'rb').read())
print(long_to_bytes(pow(data, d, N)))