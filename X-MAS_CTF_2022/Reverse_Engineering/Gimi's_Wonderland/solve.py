import string

def to_bits(x, mode, rkey):
    offset = 0;
    if mode != 0:
        offset = 2;
    else:
        rkey = (x + rkey) & 0xFF
    
    world_629 = {}
    for i in range(8):
        if (x & (1<<i)):
            world_629[-291 + 4 * i + offset] = 'minecraft:redstone_block'
        else:
            world_629[-291 + 4 * i + offset] = 'minecraft:wool'

    return rkey, world_629

def from_bits(world_691):
    xstart = -311;
    dict = {
        36: 'A',
        206: 'C',
        236: 'G',
        246: 'T'
    }
    
    encflag = ''
    for i in range(4):
        num = 0
        for j in range(7):
            x = xstart + 20 * i + 2 * j
            bl = world_691.get(x);
            if bl == "minecraft:lit_redstone_lamp":
                num = num + 1
            num = num << 1
        encflag += dict[num]
    return encflag

red_map = {}
red_map[0] = {'xx': '--x----', 'x-': '---xx--', '-x': 'x-xx-xx', '--': 'x--x---'}
red_map[1] = {'xx': '--x----', 'x-': '---xx--', '-x': 'x-xx-xx', '--': 'x--x---'}
red_map[2] = {'xx': '--x----', 'x-': 'x-xx-xx', '-x': '---xx--', '--': 'x--x---'}
red_map[3] = {'xx': '--x----', 'x-': 'x-xx-xx', '-x': '---xx--', '--': 'x--x---'}

target = 'GACGCCTGACCCTTATATGGCGTATCCTTGAGCGGCCCCTAAGATCCCTCAGGGGTTTACGCGGAGACCTCTCAAAGGGTGGTGGCCCCTCAGCGAAGATCGAGTGGCAGCTGTCATGACGATTCATAGGATCCAGACTAGGCCATGA'
flag = ''
candidates = string.ascii_letters + string.digits + '{}-_'
rkey = 0x8f
for k in range(int(len(target) / 4)):
    for c in candidates:
        new_rkey, world_629_1 = to_bits(rkey, 1, rkey)
        new_rkey, world_629_2 = to_bits(ord(c), 0, new_rkey)

        world_629_1.update(world_629_2)
        world_629 = world_629_1
        line = ''
        for i in range(8):
            if world_629[-261 - 4 * i] == world_629[-261 - 2 - 4 * i]:
                line += 'x'
            else:
                line += '-'

        world_691 = {}
        for i in range(4):
            for j, ele in enumerate(red_map[i][line[i*2:(i+1)*2]]):
                if ele == '-':
                    world_691[-239 - j * 2 - i * 20] = 'minecraft:lit_redstone_lamp'
                else:
                    world_691[-239 - j * 2 - i * 20] = 'minecraft:redstone_lamp'
        encflag = from_bits(world_691)
        if encflag == target[k*4:(k+1)*4]:
            flag += c
            break
    rkey = new_rkey
print(flag)
