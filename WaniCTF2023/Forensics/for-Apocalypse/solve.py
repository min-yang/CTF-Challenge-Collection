import struct
import binascii

for i in range(1024, 2048):
    for j in range(1024, 2048):
        width = struct.pack('>i', j)
        height = struct.pack('>i', i)
        data = b'\x49\x48\x44\x52' + width + height + b'\x08\x02\x00\x00\x00'
        if binascii.crc32(data) == 0xf478d4fa:
            print(i, j)