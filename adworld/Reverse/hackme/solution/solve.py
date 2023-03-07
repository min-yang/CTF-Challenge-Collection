import string

middle = [0x3a53ef4e, 0xd6cd80b7, 0x3a53ef4e, 0xe87f5342, 0x8bee779e, 0x9cdfa830, 0x3039, 0x6848c19b, 0x81c794ec, 0x8bee779e]
middle2 = b'+\x81+t\xf2\t_\xa3\x8b\xf2'
middle3 = b'\x5f\xf2\x5e\x8b\x4e\x0e\xa3\xaa\xc7\x93\x81\x3d\x5f\x74\xa3\x09\x91\x2b\x49\x28\x93\x67'

password = ''
for i in range(0x16):
    local_18 = 0
    for j in range(i+1):
        local_18 = (local_18 * 0x6d01788d + 0x3039) % 2**32
    print('0x%x' %local_18)
    password += chr((local_18 ^ middle3[i]) % 256)
print(password)