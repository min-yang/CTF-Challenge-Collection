import base64
from pwn import *

if not os.path.exists('bin'):
    context.log_level = 'DEBUG'
    p = remote('61.147.171.105', '56065')
    data = p.recv()
    open('bin', 'wb').write(base64.b64decode(data))
    p.interactive()

data = open('bin', 'rb').read()
png_header = b'\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d'
for i in range(12):
    print(chr(data[i] ^ png_header[i]), end='')

new_data = b''
key = b'WoAh_A_Key!?'
for i in range(len(data)):
    new_data += p8(data[i] ^ key[i % 12])
open('bin.png', 'wb').write(new_data)
