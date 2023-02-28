from pwn import *

p = process('../file/rusty_snake')

pause()

p.interactive()