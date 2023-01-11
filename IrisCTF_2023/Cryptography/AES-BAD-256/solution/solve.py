from random import sample

from pwn import *

#context.log_level = 'DEBUG'

p = process(['python3', '../file/chal.py'])
#p = remote('aes.chal.irisc.tf', '10100')

p.recvuntil(b'> ')
p.sendline(b'1')
p.recvuntil(b'> ')
p.sendline(b'a' * 5)

orign_data = p.recvuntil(b'\n', drop=True).decode()

def run_command(command):
    p.recvuntil(b'> ')
    p.sendline(b'2')
    p.recvuntil(b'> ')
    p.sendline(command)
    data_type = p.recvuntil(b'1. Get', drop=True).decode()
    if 'Unknown command type' in data_type:
        match = re.search(r'type (.{4})\.\.\.', data_type, flags=re.DOTALL)
        if match:
            return match.group(1)
        print(repr(data_type))
    elif '{' in data_type:
        ok = input('flag: %s, if right input any character, if not, input nothing?' %repr(data_type))
        if ok.strip():
            exit(0)
    return None

def search_flag(orign_data, n_block):
    for i in range(0, 32, 2):
        for b in range(256):
            b = b.to_bytes(1, 'little')
            new_data = orign_data[:n_block*32+i] + b.hex() + orign_data[n_block*32+i+2:]
            tmp_type = run_command(new_data.encode())
            if tmp_type and tmp_type[key] == mapping[key][1]:
                print('flag hit:', repr(tmp_type))
                #input('continue?')
                return new_data
    raise ValueError('can not find flag')

candidates = '0123456789abcdef'
mapping = {0: ('e', 'f'), 1: ('c', 'l'), 2: ('h', 'a'), 3: ('o', 'g')}
for key in mapping:
    hit = False
    for n_block in range(16):
        # modify first byte
        new_data = orign_data[:n_block*32] + ''.join(sample(candidates, 2, counts=[2]*16)) + orign_data[n_block*32+2:]
        data_type = run_command(new_data.encode())
        if data_type and data_type[key] != mapping[key][0]:
            hit = True
            print('block offset: %s, hit %s: %s' %(n_block, key, repr(data_type)))
            new_data = search_flag(orign_data, n_block)
            orign_data = new_data
            break
    if not hit:
        raise ValueError('first byte not work, try run again!')

p.interactive()