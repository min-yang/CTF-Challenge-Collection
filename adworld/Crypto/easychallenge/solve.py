import base64

final = 'UC7KOWVXWVNKNIC2XCXKHKK2W5NLBKNOUOSK3LNNVWW3E==='

def decode1(ans):
    s = ''
    for i in ans:
        x = ord(i)
        x = x - 25
        s += chr(x ^ 36)
    return s

def decode2(ans):
    s = ''
    for i in ans:
        x = i
        x = x ^ 36
        s += chr(x - 36)
    return s

def decode3(ans):
    return base64.b32decode(ans)

print(decode1(decode2(decode3(final))))