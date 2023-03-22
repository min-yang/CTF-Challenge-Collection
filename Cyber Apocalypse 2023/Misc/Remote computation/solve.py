from pwn import *

context.log_level = 'DEBUG'

p = remote('188.166.152.84', '31306')

p.sendlineafter(b'> ', b'1')

for _ in range(500):
    p.recvuntil(b']: ')
    exp = p.recvuntil(b' = ?', drop=True)
    try:
        res = round(eval(exp), 2)
        if res > 1337 or res < -1337:
            res = 'MEM_ERR'
    except ZeroDivisionError:
        res = 'DIV0_ERR'
    except SyntaxError:
        res = 'SYNTAX_ERR'
    except Exception as e:
        print(exp, type(e))
    p.sendline(str(res).encode())

p.interactive()