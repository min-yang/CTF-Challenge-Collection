import base64
from unlzw3 import unlzw
from pwn import *

context.log_level = 'debug'

p = remote('pwnable.kr', '9005')

p.recvuntil(b'wait...\n')
data = base64.b64decode(p.recvline())
data = unlzw(data)
open('bin', 'wb').write(data)

p.interactive()

