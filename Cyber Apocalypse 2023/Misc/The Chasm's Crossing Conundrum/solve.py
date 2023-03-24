from pwn import *

context.log_level = 'DEBUG'

p = remote('68.183.37.122', '30936')

p.sendlineafter(b'> ', b'2')
p.recvuntil(b'flashlight.\n')
p.recvline()

minutes = []
while True:
    data = p.recvline().decode()
    match = re.search(r'Person \d will take (.*?) ', data)
    if match:
        minutes.append(int(match.group(1)))
    else:
        break

min_idx = minutes.index(min(minutes))
payload = ''
for i in range(len(minutes)):
    if i == min_idx:
        continue
    if i != 0:
        payload += ','
    payload += '[%s,%s],[%s]' %(min_idx, i, min_idx)
payload = payload.split(',')
payload = ','.join(payload[:-1])
print(minutes)
print(payload)

p.sendlineafter(b'> ', payload)

p.interactive()
