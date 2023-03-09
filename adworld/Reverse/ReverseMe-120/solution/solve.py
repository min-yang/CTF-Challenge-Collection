from base64 import b64encode

target = b'you_know_how_to_remove_junk_code'

flag = ''
for ele in target:
    flag += chr(ele ^ 0x25)
print(flag)
print(b64encode(flag.encode()))