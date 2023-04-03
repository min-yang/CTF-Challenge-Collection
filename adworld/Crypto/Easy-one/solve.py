'''
    char k[] = "CENSORED";
    char c, p, t = 0;
    int i = 0;
    while ((p = fgetc(input)) != EOF) {
        c = (p + (k[i % strlen(k)] ^ t) + i*i) & 0xff;
        t = p;
        i++;
        fputc(c, output);
    }
'''
import string

p = open('msg001', 'rb').read()
enc = open('msg001.enc', 'rb').read()
t = 0
i = 0
k = []
for i, c in enumerate(enc):
    for k_char in string.ascii_letters + string.digits:
        tmp = (c - ((ord(k_char) ^ t) + i * i)) & 0xff
        if tmp == p[i]:
            k.append(k_char)
            break
    t = p[i]
    i = i + 1
print(''.join(k))

k = b'VeryLongKeyYouWillNeverGuess'
enc = open('msg002.enc', 'rb').read()
t = 0
i = 0
p = []
for c in enc:
    p.append((c - ((k[i%len(k)] ^ t) + i*i)) & 0xff)
    t = p[-1]
    i += 1
for ele in p:
    print(chr(ele), end='')