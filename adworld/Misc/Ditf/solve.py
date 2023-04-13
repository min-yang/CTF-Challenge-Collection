import binascii
from tqdm import tqdm
from pwn import *

def main():
    buffer = b'IHDR\x00\x00\x03\x9e\x00\x00\x04L\x08\x02\x00\x00\x00'
    target_crc = 0x38165a34
    for i in tqdm(range(800, 2048)):
        for j in range(800, 2048):
            tmp = b'IHDR' + p32(i, endian='big') + p32(j, endian='big') + b'\x08\x02\x00\x00\x00'
            if binascii.crc32(tmp) == target_crc:
                print(i, j)
                return

if __name__ == '__main__':
    main()