from pwn import *

context.log_level = 'ERROR'

def do_request(ID, PW=b''):
    p = remote('localhost', '9006')
    p.sendlineafter(b'ID\n', ID)
    p.sendlineafter(b'PW\n', PW)
    p.recvuntil(b'data (')
    data = p.recvuntil(b')', drop=True)
    p.close()
    return data.decode()

cookie = ''
allowed = '1234567890abcdefghijklmnopqrstuvwxyz-_'

# 1 ~ 14
for i in range(14):
    target = do_request(b'-' * (13 - i))[:32]
    for char in allowed:
        data = do_request(b'-' * (15 - i) + cookie.encode() + char.encode())
        if data[:32] == target:
            cookie += char
            print(cookie)
            break

# 15 ~ 30
start = 32
while True:
    for i in range(16):
        target = do_request(b'-' * (15 - i))[start:start+32]
        for char in allowed:
            data = do_request(b'-' * (17 - i) + cookie.encode() + char.encode())
            if data[start:start+32] == target:
                cookie += char
                print(cookie)
                if len(cookie) == 49:
                    exit()
                break
    start += 32

# you_will_never_guess_this_sugar_honey_salt_cookie