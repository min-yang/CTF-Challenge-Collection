from z3 import *

SBOX = [99, 124, 119, 123, 242, 107, 111, 197, 48, 1, 103, 43, 254, 215, 171, 118, 202, 130, 201, 125, 250, 89, 71, 240, 173, 212, 162, 175, 156, 164, 114, 192, 183, 253, 147, 38, 54, 63, 247, 204, 52, 165, 229, 241, 113, 216, 49, 21, 4, 199, 35, 195, 24, 150, 5, 154, 7, 18, 128, 226, 235, 39, 178, 117, 9, 131, 44, 26, 27, 110, 90, 160, 82, 59, 214, 179, 41, 227, 47, 132, 83, 209, 0, 237, 32, 252, 177, 91, 106, 203, 190, 57, 74, 76, 88, 207, 208, 239, 170, 251, 67, 77, 51, 133, 69, 249, 2, 127, 80, 60, 159, 168, 81, 163, 64, 143, 146, 157, 56, 245, 188, 182, 218, 33, 16, 255, 243, 210, 205, 12, 19, 236, 95, 151, 68, 23, 196, 167, 126, 61, 100, 93, 25, 115, 96, 129, 79, 220, 34, 42, 144, 136, 70, 238, 184, 20, 222, 94, 11, 219, 224, 50, 58, 10, 73, 6, 36, 92, 194, 211, 172, 98, 145, 149, 228, 121, 231, 200, 55, 109, 141, 213, 78, 169, 108, 86, 244, 234, 101, 122, 174, 8, 186, 120, 37, 46, 28, 166, 180, 198, 232, 221, 116, 31, 75, 189, 139, 138, 112, 62, 181, 102, 72, 3, 246, 14, 97, 53, 87, 185, 134, 193, 29, 158, 225, 248, 152, 17, 105, 217, 142, 148, 155, 30, 135, 233, 206, 85, 40, 223, 140, 161, 137, 13, 191, 230, 66, 104, 65, 153, 45, 15, 176, 84, 187, 22]

TRANSPOSE = [[3, 1, 4, 5, 6, 7, 0, 2],
 [1, 5, 7, 3, 0, 6, 2, 4],
 [2, 7, 5, 4, 0, 6, 1, 3],
 [2, 0, 1, 6, 4, 3, 5, 7],
 [6, 5, 0, 3, 2, 4, 1, 7],
 [2, 0, 6, 1, 5, 7, 4, 3],
 [1, 6, 2, 5, 0, 7, 4, 3],
 [4, 5, 6, 1, 2, 3, 7, 0]]

RR = [4, 2, 0, 6, 9, 3, 5, 7]
ROUNDS = 2
KEYLEN = 4 + ROUNDS * 4

def getKey():
    subF = Function('subByte_f', BitVecSort(8), BitVecSort(8))
    def initSbox(s):
        for i in range(256):
            s.add(subF(i) == SBOX[i])

    def rr(bv, n):
        n = n % 8                       # vv logical shift right
        return (bv << (bv.size() - n)) | LShR(bv, n)

    def xor(a, b):
        return [i ^ j for i, j in zip(a, b)]

    MASK = bytes.fromhex('1f983a40c3f801b1')
    MASK = [BitVecVal(i, 8) for i in MASK]
    def encrypt(block, key):
        assert len(block) == 8
        assert len(key) == KEYLEN

        for r in range(ROUNDS):
            block = xor(block, key[r*4:(r+2)*4])
            for i in range(8):
                block[i] = subF(block[i])
                block[i] = rr(block[i], RR[i])

            temp = [BitVecVal(0, 8) for _ in range(8)]
            for i in range(8):
                for j in range(8):
                    temp[j] |= (LShR(block[i], TRANSPOSE[i][j]) & 1) << i
            block = temp

            block = xor(block, MASK)
        return block

    key = [BitVec("key%d" % i, 8) for i in range(KEYLEN)]

    s = Solver()
    initSbox(s)
    pairs = [["4b0c569de9bf6510", "3298255d5314ad33"], ["5d81105912c7f421", "805146efee62f09f"], ["6e23f94180be2378", "207a88ced8ab64d1"], ["9751eeee344a8c74", "0b561354ebbb50fa"], ["f4fbf94509aaea25", "4ba4dc46bbde5c63"], ["3e571e4e9604769e", "10820c181de8c1df"], ["1f7b64083d9121e8", "0523ce32dd7a9f02"], ["69b3dfd8765d4267", "23c8d59a34553207"]]

    for pt, ct in pairs: # converting pairs to symbolic constants
        pt = [BitVecVal(i, 8) for i in bytes.fromhex(pt)]
        ct = [BitVecVal(i, 8) for i in bytes.fromhex(ct)]
        pt = encrypt(pt, key)
        for i in range(8):
            s.add(pt[i] == ct[i])

    print(s.check())
    m = s.model()
    kk = [] # extract key from model
    for k in key:
        kk.append(m[k].as_long())
    print(bytes(kk).hex())
    return bytes(kk)

from pwn import xor

def rr(c, n):
    n = n % 8
    return ((c << (8 - n)) | (c >> n)) & 0xff

def decrypt(block, key):
    MASK = bytes.fromhex('1f983a40c3f801b1')
    block = bytearray(block)

    for r in range(ROUNDS-1, -1, -1):
        block = xor(block, MASK)

        temp = bytearray(8)
        for i in range(8):
            for j in range(8):
                temp[i] |= ((block[TRANSPOSE[i].index(j)] >> i) & 1) << j
        block = temp

        for i in range(8):
            block[i] = rr(block[i], 8 - RR[i])
            block[i] = SBOX.index(block[i])

        block = xor(block, key[r*4:(r+2)*4])

    return block

key = getKey()
flag = bytes.fromhex('ceb51064c084e640690c31bf55c1df4950bc81b484f559dce0ae7d509aa0fe07f7ee127e9ecb05eb4b1b58b99494f72c0b4f3f5fe351c1cb')
for i in range(0, len(flag), 8):
    print(decrypt(flag[i:i+8], key).decode(), end='')