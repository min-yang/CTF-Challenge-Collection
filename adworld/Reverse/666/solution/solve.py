
enc_flag = b'\x69\x7a\x77\x68\x72\x6f\x7a\x22\x22\x77\x22\x76\x2e\x4b\x22\x2e\x4e\x69'
print(enc_flag)

key = []
for i in range(0, 0x12, 3):
    key.append((enc_flag[i] ^ 0x12) - 6)
    key.append((enc_flag[i+1] ^ 0x12) + 6)
    key.append(enc_flag[i+2] ^ 0x14)


for ele in key:
    print(chr(ele), end='')
print()
