'''
get buf unsign s[256]

get buf t[256]

we have key:hello world

we have flag:????????????????????????????????


for i:0 to 256
    
set s[i]:i

for i:0 to 256
    set t[i]:key[(i)mod(key.lenth)]

for i:0 to 256
    set j:(j+s[i]+t[i])mod(256)
        swap:s[i],s[j]

for m:0 to 37
    set i:(i + 1)mod(256)
    set j:(j + S[i])mod(256)
    swap:s[i],s[j]
    set x:(s[i] + (s[j]mod(256))mod(256))
    set flag[m]:flag[m]^s[x]

fprint flagx to file
'''

key = 'hello world'

s = []
for i in range(256):
    s.append(i)

t = []
for i in range(256):
    t.append(ord(key[i % len(key)]))

j = 0
for i in range(256):
    j = (j + s[i] + t[i]) % 256
    tmp = s[i]
    s[i] = s[j]
    s[j] = tmp

enc = open('enc.txt', 'rb').read()
flag = []
i = 0
j = 0
for m in range(37):
    i = (i + 1) % 256
    j = (j + s[i]) % 256
    tmp = s[i]
    s[i] = s[j]
    s[j] = tmp
    x = (s[i] + s[j] % 256) % 256
    flag.append(enc[m] ^ s[x])

for ele in flag:
    print(chr(ele), end='')